<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Site;
use App\SiteLanguages;
use App\SiteProperty;
use App\SiteValue;
use App\Rule;
use App\AjaxResponse;
use \Auth;
use App\Upload;

class SiteController extends Controller {

	public $data;
	
	public function __construct()
	{
		$this->middleware('auth');
		$this->data['action'] = '';
		$this->data['tag_first_menu'] = 'site-objects';
		$this->data['page_herader'] = 'Site Management';
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$this->data['objs'] = Site::all();
		return view('system.site',$this->data);
	}

	public function getAjax()
	{
		$user = Auth::user();

		$user_sites = $user->sites()->get()->lists('id');

		$col = array('id','name','main_site','active');

		if($user->role_id == 1 || $user->role_id == 4){

			$return['data'] = Site::select($col)->get()->toArray();
			
		}else{
			
			$return['data'] = Site::select($col)->whereIn('id',$user_sites)->get()->toArray();
		}
		
		return $return;
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->data['title']           = 'Title';
		$this->data['action']          = 'system/sites';
		$this->data['method']          = 'post';
		$this->data['sites']           = Site::whereActive(1);
		$this->data['directories']     = array_map('basename', \File::directories(public_path('templates')));		
		$this->data['site_properties'] = SiteProperty::with('DataType')->orderBy('priority','asc')->get();;
		$this->data['languages']	   = \App\Language::whereStatus(1)->get()->all();
		$this->data['language']		   = \App\Language::where('initial',1)->first()->name;
		return view('system.forms.site',$this->data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$input        = \Request::all();
		// validate
		$prepare_rule = $this->sitePrepareRule();
		$rule         = \App\Rule::prepareRule($prepare_rule,$input);
		$rule['name'] = 'required|min:4';
		$validate     = \Validator::make($input, $rule);
	    if ($validate->fails())
	    {
	    	$messages = $validate->messages();
	        return redirect()->back()->withErrors($validate->errors())->withInput();
	    }

	 //    // end validate	    
		$site              = new Site();
		$site->name        = $input['name'];
		$site->parent_site = $input['parent_site'];
		$site->template    = $input['template'];
		
		if(isset($input['active']))
		{
			$site->active  = $input['active'];
		}else{
			$site->active  = 0;
		}

		$site->save();
		$site_id           = $site->id;
		$site_properties   = siteProperty::with('dataType')->get();
		
		// processType
		$tmp = \App\CmsType::processType($site_properties,$input);		
		
		$site->siteProperties()->sync($tmp);

		//Site languages
		if(!empty($input['languages'])){

			SiteLanguages::whereSiteId($site_id)->delete();

			foreach ($input['languages'] as $language) {

				$site_languages 				= 	new SiteLanguages();
				$site_languages->site_id 		=	$site_id;
				$site_languages->language_id 	= 	$language;
				$site_languages->save();
			}
		}else{
			SiteLanguages::whereSiteId($site_id)->delete();
		}
		
		$message = 'Create site success';
		\Session::flash('response',$message);	
		return redirect('system/sites');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$site = \App\Site::find($id);
		$this->data['obj'] = $site;
		return view('system.forms.site',$this->data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$this->data['action']          = 'system/sites/'.$id;
		$this->data['method']          = 'put';
		$this->data['obj']             = Site::find($id);
		$this->data['sites']           = Site::whereActive(1)->where('id','<>',$id)->get();
		$this->data['site_properties'] = SiteProperty::with('DataType')->orderBy('priority','asc')->get();
		$this->data['directories']     = array_map('basename', \File::directories(public_path('templates')));		
		$this->data['language']		   = \App\Language::where('initial','=',1)->first()->name;
		$this->data['languages']	   = \App\Language::whereStatus(1)->get()->all();
		$this->data['site_languages']  = \App\SiteLanguages::whereSiteId($id)->get()->all();
		return view('system.forms.site',$this->data);
	}

	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input        = \Request::all();
		// validate
		$prepare_rule = $this->sitePrepareRule();
		// error validation image
		$rule         = \App\Rule::prepareRule($prepare_rule,$input);
		$rule['name'] = 'required|min:4';
		$validate     = \Validator::make($input, $rule);
	    if ($validate->fails())
	    {
	        return redirect()->back()->withErrors($validate->errors());
	    }

	    // end validate
	    
		$site              = Site::find($id);
		$site->name        = $input['name'];
		$site->parent_site = $input['parent_site'];
		$site->template    = $input['template'];
		
		if(isset($input['active']))
		{
			$site->active  = $input['active'];
		}else{
            $site->active  = 0;
        }

		if(isset($input['main_site']))
		{
            $sites = Site::where('id','!=',$site->id)->get();

            if(!empty($sites)){
                $site->main_site  = $input['main_site'];
                foreach($sites as $s){
                    $s->main_site = 0;
                    $s->save();
                }
            }
		}else{
            $site->main_site  = 0;

        }
		$site->save();	
		
		$site_properties   = siteProperty::with('dataType')->get();
		
		// processType
		$tmp     = \App\CmsType::processType($site_properties,$input,$id,'update');	
		$site->siteProperties()->sync($tmp);

		//Site languages
		if(!empty($input['languages'])){

			SiteLanguages::whereSiteId($id)->delete();

			foreach ($input['languages'] as $language) {

				$site_languages 				= 	new SiteLanguages();
				$site_languages->site_id 		=	$id;
				$site_languages->language_id 	= 	$language;
				$site_languages->save();
			}
		}else{
			SiteLanguages::whereSiteId($id)->delete();
		}

		$message = '';
		\Session::flash('response',$message);
		return redirect('system/sites');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try{
		    $obj = Site::findOrFail($id);
		    $obj->updated_by = Auth::user()->id;
			$obj->save();
		    if($obj->delete()){
		    	$return = new AjaxResponse(200,'Delete Success');
		    	$return->setData(array('delete'=>'tr'));			
			}else{
				$return = new AjaxResponse(503,'Unable to delete');
			}
		}catch(ModelNotFoundException $e){			
		    $return = new AjaxResponse(404,'Object is not found',$e->getMessage());			
		}		
		return $return->getJson();			
	}

	public function sitePrepareRule()
	{
		$site_properties = SiteProperty::with('dataType')->get();
		$prepare_rule    = array();
		foreach ($site_properties as $site_property) {
			$tmp['name']         = $site_property->name;
			$tmp['variable_name']= $site_property->variable_name;
			$tmp['type']         = $site_property->dataType->name;
			$tmp['option']       = $site_property->options;
			$tmp['is_mandatory'] = $site_property->is_mandatory;
			$prepare_rule[]      = $tmp;
		}
		return $prepare_rule;
	}
}