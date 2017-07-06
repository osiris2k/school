<?php namespace App\Http\Controllers\System\Hotels;

use App\AjaxResponse;
use Request;
use Session;
use Auth;
use Validator;
use App\DataType;
use App\DataTypeOption;
use App\HotelProperty;
use App\Http\Controllers\Controller;

class HotelPropertyController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Hotel Property Management';
        $this->data['tag_first_menu'] = 'HOTEL';
        $this->data['tag_sub_menu'] = 'HOTEL_PROPERTY';
    }

    public function getSetHotelProperty()
    {
        $this->data['hotel_properties'] = HotelProperty::with('dataType')->orderBy('priority', 'ASC')->orderBy('updated_at', 'desc')->get();
        $this->data['dataTypes'] = DataType::all();
        $this->data['title'] = "Add Hotel Property";

        return view('system.forms.hotel-properties', $this->data);
    }

    public function postSaveHotelProperty()
    {
        $input = Request::all();
        $rule['var'] = 'unique:hotel_properties,variable_name';
        $validate = Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $hotel_property = new HotelProperty();
        $hotel_property->name = $input['label'];
        $hotel_property->variable_name = $input['var'];
        $hotel_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $hotel_property->is_mandatory = $input['is_mandatory'];
        } else {
            $hotel_property->is_mandatory = 0;
        }
        $data_type_options = DataTypeOption::where('data_type_id', '=', $input['data_type_id'])->get();
        $object = array();

        foreach ($data_type_options as $data_type_option) {
            $tmp = array();
            $tmp['type'] = $data_type_option->type;
            $tmp['name'] = $data_type_option->name;
            $tmp['value'] = Request::input($data_type_option->name);
            $object[] = $tmp;
        }

        $hotel_property->options = json_encode($object);

        $hotel_property->created_by = Auth::user()->id;
        $hotel_property->updated_by = Auth::user()->id;

        $hotel_property->save();

        Session::flash('response', 'Create success');

        return redirect(route('hotel.system.get_set_hotel_property'));
    }

    public function postUpdateHotelProperty($hotelId)
    {
        $input = Request::all();
        $rule['var'] = 'unique:hotel_properties,variable_name,' . $hotelId;
        $validate = Validator::make($input, $rule);
        if ($validate->fails()) {
            $messages = $validate->messages();

            return redirect()->back()->withErrors($validate->errors())->withInput();
        }
        $hotel_property = HotelProperty::find($hotelId);
        $hotel_property->name = $input['label'];
        $hotel_property->variable_name = $input['var'];
        $hotel_property->data_type_id = $input['data_type_id'];
        if (isset($input['is_mandatory'])) {
            $hotel_property->is_mandatory = $input['is_mandatory'];
        } else {
            $hotel_property->is_mandatory = 0;
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
                $tmp['value'][] = $input;
            } else {
                $tmp['value'][] = Request::input($data_type_option->name);
            }
            $object[] = $tmp;
        }
        $hotel_property->options = json_encode($object);
        $hotel_property->save();
        Session::flash('response', 'Update success');

        return redirect(route('hotel.system.get_set_hotel_property'));
    }

    public function deleteDeleteHotelProperty($hotelId)
    {
        $obj = HotelProperty::find($hotelId);
        $obj->delete();
        $return = new AjaxResponse(200, 'Delete Success');
        $return->setData(array('delete' => '.ibox'));

        return $return->getJson();
    }

}