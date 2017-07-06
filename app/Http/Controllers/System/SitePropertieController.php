<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use App\DataType as DataType;
use App\SiteProperty as SiteProperty;
use App\DataTypeOption as DataTypeOption;
use App\AjaxResponse as AjaxResponse;

class SitePropertieController extends Controller {

	
	public $data;
	
	public function __construct()
	{
		$this->middleware('auth');
		$this->data['tag_first_menu'] = 'site-objects';
		$this->data['page_herader'] = 'Site Property Management';
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
	public function create()
	{
		$result                        = SiteProperty::with('DataType')->orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();
		$this->data['title']           = "Add Site Property";
		$this->data['dataTypes']       = DataType::all();
		$this->data['site_properties'] = $result;
		return view('system.forms.site-properties',$this->data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input                        = \Request::all();
		$rule['var'] = 'unique:site_properties,variable_name';
		$validate = \Validator::make($input, $rule);
	    if ($validate->fails())
	    {
	    	$messages = $validate->messages();
	        return redirect()->back()->withErrors($validate->errors())->withInput();
	    }
		$site_property                = new SiteProperty();
		$site_property->name          = $input['label'];
		$site_property->variable_name = $input['var'];
		$site_property->data_type_id  = $input['data_type_id'];
		if(isset($input['is_mandatory'])){
			$site_property->is_mandatory  = $input['is_mandatory'];
		}else{
			$site_property->is_mandatory = 0;	
		}
		$data_type_options            = DataTypeOption::where('data_type_id','=',$input['data_type_id'])->get();
		$object                       = array();

		foreach ($data_type_options as $data_type_option) {
			$tmp          = array();
			$tmp['type']  = $data_type_option->type;
			$tmp['name']  = $data_type_option->name;
			$tmp['value'] = Request::input($data_type_option->name);
			$object[]     = $tmp;
		}
		$site_property->options = json_encode($object);
		$id = $site_property->save();
		\Session::flash('response', 'Create success');
		// $return = new AjaxResponse(200,'Create Success', $site_property->toArray());
		return redirect('system/site-properties/create');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input         = \Request::all();			
		$rule['var'] = 'unique:site_properties,variable_name,'.$id;
		$validate = \Validator::make($input, $rule);
	    if ($validate->fails())
	    {
	    	$messages = $validate->messages();
	        return redirect()->back()->withErrors($validate->errors())->withInput();
	    }		
		$site_property = SiteProperty::find($id);
		$site_property->name          = $input['label'];
		$site_property->variable_name = $input['var'];
		$site_property->data_type_id  = $input['data_type_id'];
		if(isset($input['is_mandatory'])){
			$site_property->is_mandatory  = $input['is_mandatory'];
		}else{
			$site_property->is_mandatory = 0;	
		}
		$data_type_options            = DataTypeOption::where('data_type_id','=',$input['data_type_id'])->get();
		$object                       = array();
		foreach ($data_type_options as $data_type_option) {
			$tmp          = array();
			$tmp['type']  = $data_type_option->type;
			$tmp['name']  = $data_type_option->name;
			if($data_type_option->name=='list'){
				$input = Request::input($data_type_option->name);
				$input = explode(',',$input[0]);
				$tmp['value'][] = $input;
			}else{
				$tmp['value'][] = Request::input($data_type_option->name);
			}
			$object[]     = $tmp;
		}			
		$site_property->options = json_encode($object);
		$site_property->save();
		\Session::flash('response', 'Create success');
		return redirect('system/site-properties/create');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$obj    = SiteProperty::find($id);
		$obj->delete();
		$return = new AjaxResponse(200,'Delete Success');
		$return->setData(array('delete'=>'.ibox'));
		return $return->getJson();
	}

}
