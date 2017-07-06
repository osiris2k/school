<?php namespace App\Http\Controllers\System;

use App\Hotel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataType;
use App\DataTypeOption;
use App\FormObject;
use App\FormProperty;
use App\Site;
use Request;
use \Auth;
use App\AjaxResponse;

class FormObjectController extends Controller
{

    public $data;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['tag_first_menu'] = 'form-objects';
        $this->data['page_herader'] = 'Form Object Management';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->data['title'] = "Form Object";
        $this->data['objs'] = FormObject::all();

        return view('system.form', $this->data);
    }

    public function getAjax()
    {
        $col = array('form_objects.id', 'form_objects.name','sites.name as site_name');
        $return['data'] = FormObject::select($col)
            ->leftJoin('sites', 'sites.id', '=', 'form_objects.site_id')
            ->with('hotel')
            ->get()
            ->toArray();

        return $return;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->data['title'] = "Form Object";
        $this->data['url'] = 'system/form-objects';
        $this->data['method'] = 'post';
        $this->data['sites'] = Site::whereActive(1)->get()->all();
        $this->data['hotels'] = Hotel::all();

        return view('system.forms.form-object', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Request::all();
        $is_send = 0;
        if (isset($input['is_send'])) {
            $is_send = $input['is_send'];
            $rule['email'] = 'email|required';
        } else {
            $rule['email'] = 'email';
        }

        $user_notification = 0;
        if (isset($input['user_notification'])) {
            $user_notification = $input['user_notification'];
        }

        $rule['name'] = 'unique:form_objects|required';
        $rule['site_id'] = 'required';
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $obj = new FormObject();
        $obj->name = $input['name'];
        $obj->email = $input['email'];
        $obj->is_send = $is_send;
        $obj->user_notification = $user_notification;
        $obj->site_id = $input['site_id'];
        $obj->save();

        if (isset($input['hotel_id']) && !empty($input['hotel_id'])) {
            $obj->hotel()->sync([$input['hotel_id']]);
        }

        return redirect('system/form-objects/' . $obj->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $this->data['url'] = 'system/form-objects/' . $id;
        $this->data['method'] = 'put';
        $this->data['obj'] = FormObject::find($id);
        $this->data['title_form'] = "Edit " . $this->data['obj']->name . " name";
        $result = FormProperty::where('form_object_id', '=', $id)->orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();
        $this->data['title'] = "Add " . $this->data['obj']->name . " Property";
        $this->data['dataTypes'] = DataType::all();
        $this->data['form_properties'] = $result;
        $this->data['sites'] = Site::whereActive(1)->get()->all();
        $this->data['hotels'] = Hotel::all();

        return view('system.forms.form-properties', $this->data);
    }

    public function updateProperties($id)
    {
        $input = \Request::all();
        $form_property = FormProperty::find($id);
        $rule['var'] = 'unique:form_properties,variable_name,' . $id . ',id,form_object_id,' . $form_property->form_object_id;
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $form_property->name = $input['label'];
        $form_property->variable_name = $input['var'];
        $form_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $form_property->is_mandatory = $input['is_mandatory'];
        } else {
            $form_property->is_mandatory = 0;
        }
        $data_type_options = DataTypeOption::where('data_type_id', '=', $input['data_type_id'])->get();
        $object = array();

        foreach ($data_type_options as $data_type_option) {
            $tmp = array();
            $tmp['type'] = $data_type_option->type;
            $tmp['name'] = $data_type_option->name;
            if ($data_type_option->name == 'list') {
                $input = Request::input($data_type_option->name);
                $input = explode(',', $input[0]);
                $tmp['value'] = array($input);
            } else {
                $tmp['value'] = array(Request::input($data_type_option->name));
            }
            $object[] = $tmp;
        }
        $form_property->options = json_encode($object);
        $form_property->updated_by = Auth::user()->id;
        // dd($content_property);
        $form_property->save();
        \Session::flash('response', 'Updated success');

        return redirect('system/form-objects/' . $form_property->form_object_id);
    }

    public function deleteProperties($id)
    {
        $obj = FormProperty::find($id);
        $obj->delete();
        $return = new AjaxResponse(200, 'Delete Success');
        $return->setData(array('delete' => '.ibox'));

        return $return->getJson();
    }

    public function addProperties($id)
    {
        $input = \Request::all();
        $rule['var'] = 'unique:form_properties,variable_name,NULL,id,form_object_id,' . $id;
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $form_property = new FormProperty();
        $form_property->name = $input['label'];
        $form_property->variable_name = $input['var'];
        $form_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $form_property->is_mandatory = $input['is_mandatory'];
        } else {
            $form_property->is_mandatory = 0;
        }
        $data_type_options = DataTypeOption::where('data_type_id', '=', $input['data_type_id'])->get();
        $object = array();

        foreach ($data_type_options as $data_type_option) {
            $tmp = array();
            $tmp['type'] = $data_type_option->type;
            $tmp['name'] = $data_type_option->name;
            if ($data_type_option->name == 'list') {
                $input = Request::input($data_type_option->name);
                $input = explode(',', $input[0]);
                $tmp['value'] = $input;
            } else {
                $tmp['value'] = Request::input($data_type_option->name);
            }
            $object[] = $tmp;
        }
        $form_property->options = json_encode($object);
        $form_property->form_object_id = $id;
        $form_property->save();
        \Session::flash('response', 'Create success');

        return redirect('system/form-objects/' . $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->data['obj'] = FormObject::find($id);
        $this->data['title'] = "Edit " . $this->data['obj']->name . " Property";
        $this->data['url'] = 'system/form-objects/' . $id;
        $this->data['method'] = 'put';
        $this->data['sites'] = Site::whereActive(1)->get()->all();

        $this->data['hotels'] = Hotel::all();

        return view('system.forms.form-object', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $input = Request::all();
        $is_send = 0;
        if (isset($input['is_send'])) {
            $is_send = $input['is_send'];
            $rule['email'] = 'email|required';
        } else {
            $rule['email'] = 'email';
        }
        $user_notification = 0;
        if (isset($input['user_notification'])) {
            $user_notification = $input['user_notification'];
        }

        $rule['name'] = 'unique:form_objects,id,' . $id . '|required';
        $rule['site_id'] = 'required';
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $obj = FormObject::find($id);
        $obj->name = $input['name'];
        $obj->email = $input['email'];
        $obj->is_send = $is_send;
        $obj->site_id = $input['site_id'];
        $obj->user_notification = $user_notification;
        $obj->save();

        if (isset($input['hotel_id']) && !empty($input['hotel_id'])) {
            $obj->hotel()->sync([$input['hotel_id']]);
        }

        return redirect('system/form-objects');
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
            $obj = FormObject::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            if ($obj->delete()) {

                /** Delete relation */
                $obj->hotel()->detach();

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

}
