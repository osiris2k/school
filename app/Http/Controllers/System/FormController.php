<?php namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

use App\Submission;
use App\FormObject;
use App\FormProperty;
use App\FormPropertyLabel;
use App\Language;
use \Auth;
use App\AjaxResponse;

class FormController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['formSaveValue']]);
        $this->data['page_herader'] = 'Form Management';
        $this->data['tag_first_menu'] = 'form';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id)
    {
        $this->data['title'] = 'Title';
        $this->data['action'] = 'system/forms';
        $this->data['method'] = 'post';
        $this->data['form_object'] = FormObject::find($id);
        $this->data['forms'] = Submission::all();
        $this->data['properties'] = FormProperty::where('form_object_id', '=', $id)->with('DataType')->orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();
        //$this->data['languages']      = Language::all();
        $this->data['languages'] = Language::whereStatus(1)->orderBy('priority')->get();

        return view('system.forms.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = \Request::all();
        $language_id = $input['language_id'];
        $form_object_id = $input['form_object_id'];
        unset($input['form_object_id']);
        unset($input['language_id']);
        unset($input['_token']);
        $array = array();
        foreach ($input as $key => $value) {
            $tmp = explode('-', $key);
            $form_property_id = $tmp[sizeof($tmp) - 1];
            unset($tmp[sizeof($tmp) - 1]);
            $key = implode('-', $tmp);
            if ($key != 'tooltip') {
                $tmp_form_property_label = FormPropertyLabel::where('language_id', '=', $language_id)->where('form_property_id', '=', $form_property_id)->first();
                if ($tmp_form_property_label) {
                    $tmp_form_property_label->language_id = $language_id;
                    $tmp_form_property_label->form_property_id = $form_property_id;
                    $tmp_form_property_label->label = $value;
                    $tmp_form_property_label->tooltip = $input['tooltip-' . $form_property_id];
                    $tmp_form_property_label->save();
                } else {
                    $tmp_form_property_label = new FormPropertyLabel();
                    $tmp_form_property_label->language_id = $language_id;
                    $tmp_form_property_label->form_property_id = $form_property_id;
                    $tmp_form_property_label->label = $value;
                    $tmp_form_property_label->tooltip = $input['tooltip-' . $form_property_id];
                    $tmp_form_property_label->save();
                }
            }
        }

        return redirect('system/forms/create/' . $form_object_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // $contents                  = Submission::with('FormProperties')->where('form_object_id','=',$id)->get();
        // $this->data['objects']     = $contents;
        $this->data['object_id'] = $id;
        $form_object = FormObject::find($id);
        $this->data['object_name'] = $form_object->name;
        $this->data['form_properties'] = FormProperty::where('form_object_id', '=', $id)->get();
        $this->data['tag_first_menu'] = 'submission';

        return view('system.lists.form', $this->data);
    }

    public function getAjax($id)
    {
        $request = \Request::input();
        $contents = Submission::with('FormProperties')->where('form_object_id', '=', $id);

        if ($request['filter_date_start'] != '' && $request['filter_date_end'] != '') {
            $contents->whereBetween('submission_date', [date('Y-m-d H:i:s', strtotime($request['filter_date_start'])),
                date('Y-m-d 23:59:59', strtotime($request['filter_date_end']))]);
        }

        $contents = $contents->orderBy('submission_date', 'desc')->get();
        // $contents = \App\FormValue::with('formProperty')->where('form_object_id','=',$id)->get();
        $submissions = array();
        foreach ($contents as $content) {
            $tmp = array();
            foreach ($content->FormProperties as $property) {
                $tmp[$property->variable_name] = $property->pivot->value;
                $tmp['id'] = $property->pivot->submission_id;
                $submission_date = Submission::whereId($property->pivot->submission_id)->first()->submission_date;

                if (!empty($submission_date)) {
                    $tmp['submission_date'] = date("j F Y : H:i:s", strtotime($submission_date));
                    $tmp['submission_date'] = date("j F Y : H:i:s", strtotime($submission_date));
                } else {
                    $tmp['submission_date'] = $submission_date;
                }
            }
            $submissions[] = $tmp;
        }
        $data['data'] = $submissions;

        return json_encode($data);
    }

    public function export($id)
    {
        $request = \Request::input();

        $obj = \App\FormObject::find($id);
        $mytime = \Carbon\Carbon::now();
        $file_name = $obj->name . $mytime->toDateString();
        \Excel::create($file_name, function ($excel) use ($id, $request) {
            $excel->sheet('report', function ($sheet) use ($id, $request) {
                $data['form_properties'] = \App\FormProperty::where('form_object_id', '=', $id)->get();
                $data['contents'] = \App\Submission::with('FormProperties')->where('form_object_id', '=', $id);

                if ($request['filter_date_start'] != '' && $request['filter_date_end'] != '') {
                    $data['contents']->whereBetween('submission_date', [date('Y-m-d H:i:s', strtotime($request['filter_date_start'])),
                        date('Y-m-d 23:59:59', strtotime($request['filter_date_end']))]);
                }

                $data['contents'] = $data['contents']->orderBy('submission_date', 'desc')->get();

                $sheet->setStyle(array(
                    'font' => array(
                        'name' => 'Calibri',
                        'size' => 10
                    )
                ));
                $sheet->freezeFirstRow();
                $sheet->loadView('system.export')->with($data);
            });
        })->export('xls');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    public function destroy_submission($id)
    {
        try {
            $obj = Submission::findOrFail($id);
            if ($obj->delete()) {
                $return = new AjaxResponse(200, 'Delete Success');
                $return->setData(array('delete' => 'tr'));
            } else {
                $return = new AjaxResponse(503, 'Unable to delete');
            }
        } catch (ModelNotFoundException $e) {
            $return = new AjaxResponse(404, 'Object is not found', $e->getMessage());
        }

        return $return->getJson();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $obj = Form::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            if ($obj->delete()) {
                $return = new AjaxResponse(200, 'Delete Success');
            } else {
                $return = new AjaxResponse(503, 'Unable to delete');
            }
        } catch (ModelNotFoundException $e) {
            $return = new AjaxResponse(404, 'Object is not found', $e->getMessage());
        }

        return $return->getJson();
    }

    public function formPrepareRule($formObjectId)
    {
        $form_properties = FormProperty::with('dataType')
            ->where('form_object_id', '=', $formObjectId)
            ->get();

        $prepare_rule = array();
        foreach ($form_properties as $form_property) {
            $tmp['name'] = $form_property->name;
            $tmp['variable_name'] = $form_property->variable_name;
            $tmp['type'] = $form_property->dataType->name;
            $tmp['option'] = $form_property->options;
            $tmp['is_mandatory'] = $form_property->is_mandatory;
            $prepare_rule[] = $tmp;
        }

        return $prepare_rule;
    }

    public function formSaveValue($id)
    {
        $input = \Request::all();

        if (isset($input['honey_pot']) && !empty($input['honey_pot'])) {
            if (\Request::ajax()) {
                return json_encode(['code' => 200, 'message' => 'error']);
            } else {
                $url = '';
                if (isset($input['submit'])) {
                    $url = $input['submit'];
                }

                return redirect()->back();
            }
        }

        unset($input['honey_pot']);

        // validate
        $prepare_rule = $this->formPrepareRule($id);
        $rule = \App\Rule::prepareRule($prepare_rule, $input);
        $validate = \Validator::make($input, $rule);

        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $submission = new Submission;
        $submission->form_object_id = $id;
        $submission->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $submission->submission_date = date("Y-m-d H:i:s");
        $submission->save();
        $submission_id = $submission->id;

        $properties = FormProperty::where('form_object_id', '=', $id)->get();
        $tmp = array();
        $has_attach = false;
        $file_path = "";
        foreach ($properties as $property) {
            /** Add Form Label to email  */
            $new_data['properties_label'][$property->variable_name]['label'] = $property->name;
            if (isset($input[$property->variable_name])) {
                $tmp_value = $input[$property->variable_name];
                // Add to save file
                if ($property->data_type_id == 19) {
                    $upload = new \App\Upload();
                    $tmp_value = $upload->uploadFile($property->variable_name);
                    $file_path = $tmp_value;
                    $has_attach = true;
                    /** Add Uploaded File Path to email  */
                    $new_data['uploaded_files'][$property->variable_name]['file_path'] = asset($tmp_value);
                }
                // End

            } else {
                $tmp_value = '';
            }
            $value = array('value'            => $tmp_value,
                'submission_id'    => $submission_id,
                'form_property_id' => $property->id,
                'form_object_id'   => $id);
            $tmp[] = $value;

        }

        $submission->formProperties()->sync($tmp); // save value

        $form_object = FormObject::find($id);

        $pma_email = env('POSTMARKAPPEMAIL', '');
        $pma_token = env('POSTMARKAPPTOKEN', '');

        if ($form_object->is_send == 1) {
            $new_data['data'] = $input;
            unset($new_data['data']['submit']);
            unset($new_data['data']['_token']);
            foreach ($properties as $property) {
                if ($property->data_type_id == 19) {
                    if (array_key_exists($property->variable_name, $new_data['data'])) {
                        /** Not remove file field */
                        //unset($new_data['data'][$property->variable_name]);
                    }
                }
            }
            $hotelEmail = '';
            if(isset($input['hotel_email'])){
                $hotelEmail = $input['hotel_email'];
            }
            $new_data['cms_url'] = \URL::to('thailand/system/forms/' . $id);
            $new_data['submission_name'] = ucfirst(str_replace("_", " ", $form_object->name));

            if (!empty($pma_email) && !empty($pma_token)) {
                $htmlBody = '<html><body>' . view('system.email', $new_data) . '</body></html>';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://api.postmarkapp.com/email');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'X-Postmark-Server-Token: ' . $pma_token));
//                curl_setopt($ch, CURLOPT_POSTFIELDS, "{From: '" . $pma_email . "', To: '" . $form_object->email . "' ".($hotelEmail!=''? ", Cc: '".$hotelEmail."'" : "").", Subject: 'New Submission to " . ucfirst(str_replace("_", " ", $new_data['submission_name'])) . "', HtmlBody: '" . $htmlBody . "'}");
                curl_setopt($ch, CURLOPT_POSTFIELDS, "{From: '" . $pma_email . "', To: '" . $hotelEmail . "' , Subject: 'New Submission to " . ucfirst(str_replace("_", " ", $new_data['submission_name'])) . "', HtmlBody: '" . $htmlBody . "'}");
                curl_exec($ch);

            } else {
                // TODO Modify this later
                \Mail::send('system.email', $new_data, function ($message) use ($new_data, $form_object, $has_attach, $file_path) {
                    $message->from('cms@mg.quo-global.com', 'QUO CMS');
                    $message->to($form_object->email)->subject('New Submission to ' . $new_data['submission_name']);
                    if ($has_attach) {
                        $message->attach($file_path);
                    }
                });
            }

        }

        if ($form_object->user_notification == 1) {
            $user_email = '';
            $new_data['data'] = $input;
            unset($new_data['data']['submit']);
            unset($new_data['data']['_token']);
            foreach ($properties as $property) {
                if ($property->data_type_id == 19) {
                    if (array_key_exists($property->variable_name, $new_data['data'])) {
                        /** Not remove file field */
                        //unset($new_data['data'][$property->variable_name]);
                    }
                }
                // get user email
                if ($property->data_type_id == 17) {
                    if (array_key_exists($property->variable_name, $new_data['data'])) {
                        $user_email = $new_data['data'][$property->variable_name];
                    }
                }
            }

            if (!empty($user_email)) {
                $new_data['submission_name'] = ucfirst(str_replace("_", " ", $form_object->name));
                $hotelEmail = '';
                if(isset($input['hotel_email'])){
                    $hotelEmail = $input['hotel_email'];
                }
                if (!empty($pma_email) && !empty($pma_token)) {
                    $htmlBody = '<html><body>' . view('system.email.user_notification', $new_data) . '</body></html>';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://api.postmarkapp.com/email');
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'X-Postmark-Server-Token: ' . $pma_token));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "{From: '" . $pma_email . "', To: '" . $user_email . "' ".($hotelEmail!=''? ", Cc: '".$hotelEmail."'" : "").", Subject: 'New Submission to " . ucfirst(str_replace("_", " ", $new_data['submission_name'])) . "', HtmlBody: '" . $htmlBody . "'}");
                    curl_exec($ch);

                } else {
                    // TODO Modify this later

                    \Mail::send('system.email.user_notification', $new_data, function ($message) use ($new_data, $form_object, $user_email) {
                        if (!empty($form_object->email)) {
                            $message->from($form_object->email, 'Administrator');

                        } else {
                            $message->from('cms@mg.quo-global.com', 'Administrator');

                        }
                        $message->to($user_email)->subject('Thank you for your submission');
                    });

                }
            }

        }


        if (\Request::ajax()) {
            return json_encode(['code' => 200, 'message' => 'Success']);
        } else {
            $url = '';
            if (isset($input['submit'])) {
                $url = $input['submit'];
            }

//            return redirect($url);
            return redirect()->back()->with(['FORM_STATUS' => 'SUCCESS']);
        }
    }
}
