<?php namespace App\Http\Controllers\System;

use App\Content;
use App\ContentObjectType;
use App\ContentValue;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataType;
use App\DataTypeOption;
use App\ContentObject;
use App\ContentProperty;
use App\Language;
use Request;
use \Auth;
use App\AjaxResponse;

class ContentObjectController extends Controller
{

    public $data;

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['tag_first_menu'] = 'content-objects';
        $this->data['page_herader'] = 'Content Object Management';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->data['title'] = "Content Object";
        $this->data['objs'] = ContentObject::all();

        return view('system.content', $this->data);
    }

    public function getAjax()
    {
        $col = array('id', 'name');
        $return['data'] = ContentObject::select($col)->get();

        return json_encode($return);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->data['title'] = "Content Object";
        $this->data['url'] = 'system/content-objects';
        $this->data['method'] = 'post';
        $this->data['content_object_types_array'] = ContentObjectType::getAllContentObjectType();

        return view('system.forms.content-object', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Request::all();

        $rule['name'] = 'unique:content_objects|required|min:3';
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $obj = new ContentObject();
        $obj->content_object_types_id = $input['content_object_types'];
        $obj->name = $input['name'];
        $obj->created_by = Auth::user()->id;
        $obj->save();

        $this->generateDefaultProperties($input, $obj->id);

        return redirect('system/content-objects/' . $obj->id);
    }

    public function generateDefaultProperties($input, $contentObjId)
    {
        if ($input['generate_seo_meta'] === 'YES') {
            $this->generateSEOMeta($contentObjId);
        }
    }

    public function generateSEOMeta($contentObjId)
    {
        $seoMetaArray = [
            ['name' => 'Meta Title', 'variable_name' => 'meta_title', 'data_type_id' => 1, 'is_mandatory' => 0],
            ['name' => 'Meta Keywords', 'variable_name' => 'meta_keywords', 'data_type_id' => 3, 'is_mandatory' => 0],
            ['name' => 'Meta Description', 'variable_name' => 'meta_description', 'data_type_id' => 3, 'is_mandatory' => 0]
        ];

        foreach ($seoMetaArray as $seoItem) {
            $content_property = new ContentProperty();
            $content_property->name = $seoItem['name'];
            $content_property->variable_name = $seoItem['variable_name'];
            $content_property->data_type_id = $seoItem['data_type_id'];
            $content_property->is_mandatory = $seoItem['is_mandatory'];

            $data_type_options = DataTypeOption::where('data_type_id', '=', $seoItem['data_type_id'])->get();
            $object = array();

            foreach ($data_type_options as $data_type_option) {
                $tmp = array();
                $tmp['type'] = $data_type_option->type;
                $tmp['name'] = $data_type_option->name;
                $tmp['value'] = [$data_type_option->default];
                $object[] = $tmp;
            }

            $content_property->options = json_encode($object);
            $content_property->content_object_id = $contentObjId;
            $content_property->created_by = Auth::user()->id;

            $content_property->save();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $this->data['url'] = 'system/content-objects/' . $id;
        $this->data['method'] = 'put';
        $this->data['obj'] = ContentObject::find($id);
        $this->data['title_form'] = "Edit " . $this->data['obj']->name . " name";
        $result = ContentProperty::where('content_object_id', '=', $id)->orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();
        $this->data['title'] = "Add " . $this->data['obj']->name . " Property";
        $this->data['dataTypes'] = DataType::all();
        $this->data['content_properties'] = $result;
        $this->data['content_object_types_array'] = ContentObjectType::getAllContentObjectType();

        return view('system.forms.content-properties', $this->data);
    }

    public function updateProperties($id)
    {
        $content_property = ContentProperty::find($id);
        $input = \Request::all();
        $rule['var'] = 'unique:content_properties,variable_name,' . $id . ',id,content_object_id,' . $content_property->content_object_id;

        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $content_property = ContentProperty::find($id);
        $content_property->name = $input['label'];
        $content_property->variable_name = $input['var'];
        $content_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $content_property->is_mandatory = $input['is_mandatory'];
        } else {
            $content_property->is_mandatory = 0;
        }
        $data_type_options = DataTypeOption::where('data_type_id', '=', $input['data_type_id'])->get();
        $object = array();

        foreach ($data_type_options as $data_type_option) {
            $tmp = array();
            $tmp['type'] = $data_type_option->type;
            $tmp['name'] = $data_type_option->name;
            if ($data_type_option->name == 'list') {
                $input = Request::input($data_type_option->name);
                $input = explode(',', $input);
                $tmp['value'] = $input;
            } else {
                $tmp['value'] = array(Request::input($data_type_option->name));
            }
            $object[] = $tmp;
        }
        $content_property->options = json_encode($object);
        $content_property->updated_by = Auth::user()->id;

        $content_property->save();
        \Session::flash('response', 'Updated success');

        return redirect('system/content-objects/' . $content_property->content_object_id);
    }

    public function deleteProperties($id)
    {
        try {
            $obj = ContentProperty::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            // Edit by Piyaphong
            // Delete also content value
            ContentValue::whereContentPropertyId($id)->delete();
            if ($obj->delete()) {
                $return = new AjaxResponse(200, 'Delete Success');
                $return->setData(array('delete' => '.ibox'));
            } else {
                $return = new AjaxResponse(503, 'Unable to delete');
            }
        } catch (ModelNotFoundException $e) {
            $return = new AjaxResponse(404, 'Object is not found', $e->getMessage());
        }

        return $return->getJson();
    }

    public function addProperties($id)
    {
        $input = \Request::all();
        $rule['var'] = 'unique:content_properties,variable_name,NULL,id,content_object_id,' . $id;
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $content_property = new ContentProperty();
        $content_property->name = $input['label'];
        $content_property->variable_name = $input['var'];
        $content_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $content_property->is_mandatory = $input['is_mandatory'];
        } else {
            $content_property->is_mandatory = 0;
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

        $content_property->options = json_encode($object);
        $content_property->content_object_id = $id;
        $content_property->created_by = Auth::user()->id;

        $content_property->save();

        /*
         * Edit by Piyaphong
         * Add default value to content values
         */
        $content_object = ContentObject::find($id);
        $contents = $content_object->contents;

        $languages = Language::whereStatus(1)->get();

        foreach ($contents as $key => $v) {
            foreach ($languages as $key => $language) {
                $content_values = new ContentValue();
                $content_values->content_id = $v->id;
                $content_values->content_property_id = $content_property->id;
                $content_values->language_id = $language->id;
                $content_values->value = "";
                $content_values->content_object_id = $id;
                $content_values->save();
            }
        }


        \Session::flash('response', 'Create success');

        return redirect('system/content-objects/' . $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        // $this->data['obj']             = ContentObject::find($id);
        // $this->data['title']           = "Edit ".$this->data['obj']->name." Property";
        // $this->data['url']			   = 'system/content-objects/'.$id;
        // $this->data['method']		   = 'put';
        // return view('system.forms.content-object',$this->data);
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
        $rule['name'] = 'unique:content_objects,id,' . $id . '|required|min:3';
        $validate = \Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $obj = ContentObject::find($id);
        $obj->content_object_types_id = $input['content_object_types'];
        $obj->name = $input['name'];
        $obj->save();
        $obj->updated_by = Auth::user()->id;

        return redirect('system/content-objects');
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
            $obj = ContentObject::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
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

    public function copyContentObject($id){
        $key = md5(microtime().rand());
        $obj = ContentObject::find($id);
        $new_obj = new ContentObject();
        if(ContentObject::where('name',$obj->name)->first()){
            $new_obj->name = $obj->name."_(".$key.")";
        }

        $new_obj->save();

        foreach ($obj->contentProperties()->get() as $key => $v) {
            $new_cp = $v->replicate();
            $new_cp->content_object_id = $new_obj->id;
            $new_cp->save();
        }


        return redirect('system/content-objects/' . $new_obj->id);
    }

}
