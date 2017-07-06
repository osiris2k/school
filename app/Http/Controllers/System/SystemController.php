<?php namespace App\Http\Controllers\System;

use App\HotelProperty;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Input;
use \App\DataTypeOption as DataTypeOption;
use \App\DataType as DataType;
use \App\CmsType;
use \Response;
use \App\Media;
use \App\SiteProperty;
use \App\Rule;

class SystemController extends Controller
{

    public $data = [];

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function getMenu()
    {
        return view('system.menu', $this->data);
    }

    public function dashboard()
    {
        return view('system.dashboard', $this->data);
    }

    public function renderType($type_id, $type_name, $type, $obj_id = false)
    {
        DataType::where('name', '=', $type_name)->get();
        $this->data['data_type_options'] = DataTypeOption::where('data_type_id', '=', $type_id)->get();
        $this->data['data_type'] = $type_name;
        $this->data['data_type_id'] = $type_id;
        if ($type == 'site') {
            $this->data['url'] = 'system/site-properties';
        } else if ($type == 'content') {
            $this->data['url'] = 'system/content-add-properties/' . $obj_id;
        } else if ($type == 'form') {
            $this->data['url'] = 'system/form-add-properties/' . $obj_id;
        } else if ($type == 'hotel') {
            $this->data['url'] = route('hotel.system.post_save_hotel_property');
        }

        return view('system.renders.render-object', $this->data);
    }

    public function uploadCropImage()
    {
        $input = \Request::all();
        $file = $input['file'];
        $extenstion = 'png';
        $file_name = strtolower(str_random(32) . '.' . $extenstion);
        $upload = new \App\Upload();
        $path = $upload->getUploadPath();
        while (file_exists($path . $file_name)) {
            $file_name = strtolower(str_random(32) . '.' . $extenstion);
        }
        $destination_path = $path . $file_name;
        // set thumbnail
        $upload->createThumbnailSize($path . 'thumbnail_' . $file_name, $file, $extenstion);
        $upload->createFullSize($destination_path, $file, $extenstion, false, $input);
        $return = array('new_path' => $destination_path, 'input' => $input);

        return Response::json($return, 200);
    }

    public function uploadImagesAjax()
    {
        $cmsType = new CmsType();
        $return = $cmsType->UploadImagesAjax('file');
        if ($return)
            return Response::json($return, 200);
        else
            return Response::json('error', 400);
    }

    public function uploadImageFromEdit()
    {
        $dir_name = "uploads/";
        move_uploaded_file($_FILES['file']['tmp_name'], $dir_name . $_FILES['file']['name']);
        echo $dir_name . $_FILES['file']['name'];
    }

    public function getMedia()
    {
        $obj = Media::all();

        return Response::json($obj, 200);
    }

    public function getRule($id, $type = false)
    {
        if ($type == 'contents') {
            $property = \App\ContentProperty::find($id);
        } else if ($type == 'sites') {
            $property = \App\SiteProperty::find($id);
        } else if ($type == 'forms') {
            $property = \App\FormProperty::find($id);
        }
        $prepare_rule = array();
        $tmp['name'] = $property->name;
        $tmp['variable_name'] = $property->variable_name;
        $tmp['type'] = $property->dataType->name;
        $tmp['option'] = $property->options;
        $tmp['is_mandatory'] = $property->is_mandatory;
        $prepare_rule[] = $tmp;
        $rule = \App\Rule::prepareRuleImage($prepare_rule, $property->variable_name);

        return Response::json($rule, 200);
    }

    public function orderProperty($id, $type, $order)
    {
        if ($type == 'site') {
            $property = \App\SiteProperty::find($id);
        } else if ($type == 'content') {
            $property = \App\ContentProperty::find($id);
        } else if ($type == 'form') {
            $property = \App\FormProperty::find($id);
        } else if ($type == 'menu') {
            $property = \App\Menu::find($id);
        }
        $property->priority = $order;
        $property->save();
        $property->touch();
    }

    public function orderPropertyType($type)
    {
        $sort = \Input::get('sort');
        switch ($type) {
            case $type == 'site':
                foreach ($sort as $key => $value) {
                    $property = \App\SiteProperty::find($value);
                    $property->priority = $key;
                    $property->save();
                    $property->touch();
                }
                break;
            case $type == 'content' :
                foreach ($sort as $key => $value) {
                    $property = \App\ContentProperty::find($value);
                    $property->priority = $key;
                    $property->save();
                    $property->touch();
                }
                break;
            case $type == 'form' :
                foreach ($sort as $key => $value) {
                    $property = \App\FormProperty::find($value);
                    $property->priority = $key;
                    $property->save();
                    $property->touch();
                }
                break;
            case $type == 'menu' :
                foreach ($sort as $key => $value) {
                    $property = \App\Menu::find($value);
                    $property->priority = $key;
                    $property->save();
                    $property->touch();
                }
                break;
            case $type == 'language' :
                foreach ($sort as $key => $value) {
                    $property = \App\Language::find($value);
                    $property->priority = $key;

                    $property->save();
                    $property->touch();
                    if ($property->priority == 0) {
                        $property->initial = 1;
                    } else {
                        $property->initial = 0;
                    }
                    $property->save();
                    $property->touch();
                }
                break;
            case $type == 'hotel':
                foreach ($sort as $key => $value) {
                    $property = HotelProperty::find($value);
                    $property->priority = $key;
                    $property->save();
                    $property->touch();
                }
                break;
            default :
                break;
        }

        // $property->priority = $order;
        // $property->save();
        // $property->touch();
    }
}
