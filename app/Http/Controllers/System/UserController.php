<?php namespace App\Http\Controllers\system;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Site;
use App\UserSite;
use \Auth;
use App\AjaxResponse;

use Illuminate\Http\Request;

class UserController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
		$this->data['page_herader'] = 'User Management';
		$this->data['tag_first_menu'] = 'user';
	}
	
	public function rules()
	{
		return [
			'email'     			=> 'required|email|unique:users|max:50|min:6',
			'password'  			=> 'required|min:6|confirmed',
			'password_confirmation' => 'required|min:6',
			'firstname' 			=> 'required',
			'lastname'  			=> 'required',
			'role_id'   			=> 'required|integer',
	    ];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->data['objs']  = User::all();
		return view('system.user',$this->data);
	}

	public function getAjax()
	{
		$user = Auth::user();

		$col = array('users.id','users.email','users.firstname','users.lastname','roles.name');

		if($user->role_id == 1 ){
			
			$return['data'] = User::join('roles','users.role_id','=','roles.id')->select($col)->get()->toArray();

		}elseif($user->role_id == 4){ // Supervisor profile

			$return['data'] = User::join('roles','users.role_id','=','roles.id')->select($col)
			->where('users.id','!=',1) // do not select developer profile
			->get()
			->toArray();

		}elseif($user->role_id == 2){

			$return['data'] = User::join('roles','users.role_id','=','roles.id')->select($col)
			->where('users.id',$user->id)
			->orWhere('created_by',$user->id)
			->get()
			->toArray();

		}elseif($user->role_id == 3){

			$return['data'] = User::join('roles','users.role_id','=','roles.id')->select($col)
			->where('users.id',$user->id)
			->get()
			->toArray();
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
		$user = Auth::user();
		if($user->role_id == 1 ){

			$this->data['roles']  = Role::orderBy('priority','desc')->get();
			$this->data['sites'] = Site::whereActive(1)->get();

		}elseif($user->role_id == 4){

			$this->data['roles']  = Role::where('id','!=',1)->orderBy('priority','desc')->get();
			$this->data['sites'] = Site::whereActive(1)->get();

		}else if($user->role_id == 2){

			$this->data['roles']  = Role::whereIn('id',[2,3])->orderBy('priority','desc')->get();
			$this->data['role_id'] = Role::whereId(3)->get()->first()->id;
			$this->data['sites'] = $user->sites()->get();
		}
		$this->data['user']		=   $user;
		$this->data['user_sites'] = array();
		$this->data['action'] = 'system/users';
		$this->data['method'] = 'post';
		
		return view('system.forms.users',$this->data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$post = \Request::all();
		$v = \Validator::make($post, $this->rules());
	    if ($v->fails())
	    {
	        return redirect()->back()->withErrors($v->errors());
	    }else{
			$user            = new User();
			$user->email  = $post['email'];
			$user->password  = \Hash::make($post['password']);
			$user->firstname = $post['firstname'];
			$user->lastname  = $post['lastname'];
			$user->role_id   = $post['role_id'];
			$user->created_by = Auth::user()->id;  
			$user->save();

			if(!empty($post['sites'])){

				foreach ($post['sites'] as $k => $v) {

					$user_site = new UserSite();
					$user_site->site_id = $v;
					$user_site->user_id = $user->id;
					$user_site->save();
				}

			}else if(!empty($post['checksite'])){
				$user_site = new UserSite();
				$user_site->site_id = $post['checksite'];
				$user_site->user_id = $user->id;
				$user_site->save();
			}

			
			$message         = '<div class="alert alert-success"> Create Success </div>';
			\Session::flash('response',$message);
//			return redirect('system/users');
	    }
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
		$current_user = Auth::user();

		$user = User::find($id);

		if($current_user->role_id == 1  ){
			
			$this->data['sites'] = Site::whereActive(1)->get();
			$this->data['roles'] = Role::orderBy('priority','desc')->get();

		}elseif($current_user->role_id == 4){
			$this->data['sites'] = Site::whereActive(1)->get();
			$this->data['roles'] = Role::where('id','!=',1)->orderBy('priority','desc')->get();

		}elseif($current_user->role_id == 2){

			$this->data['sites'] = $current_user->sites()->get();
			$this->data['roles'] = Role::whereIn('id',[2,3])->orderBy('priority','desc')->get();
		}elseif($current_user->role_id == 3){

			$this->data['sites'] = $current_user->sites()->get();
			$this->data['roles'] = Role::where('id',3)->orderBy('priority','desc')->get();
		}
		
		$this->data['obj'] 		= $user;
		$this->data['action'] 	= 'system/users/'.$id;
		$this->data['method'] 	= 'put';
		$this->data['user_sites'] = $user->sites()->get()->lists('id');
		$this->data['user']		=   $current_user;
		
		return view('system.forms.users',$this->data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$post = \Request::all();
		if($post['password']=="")
		{
			$rule = ['firstname' => 'required',
					 'lastname'  => 'required',
					 'role_id'   => 'required|integer'];
		}else{
			$rule = $this->rules();
			unset($rule['email']);
		}
		unset($post['email']);
		$v = \Validator::make($post, $rule);

	    if ($v->fails())
	    {
	        return redirect()->back()->withErrors($v->errors());
	    }else{
			$user            = User::find($id);
			$user->firstname = $post['firstname'];
			$user->lastname  = $post['lastname'];
			$user->role_id   = $post['role_id'];
			$user->updated_by = Auth::user()->id; 
			if($post['password']!="")
			{
				$user->password   = \Hash::make($post['password']);
			}
			$user->save();
	
			$user_site = UserSite::whereUserId($id)->delete();

			if(!empty($post['sites']) && $user->role_id !=3){

				foreach ($post['sites'] as $k => $v) {

					$user_site = new UserSite();
					$user_site->site_id = $v;
					$user_site->user_id = $user->id;
					$user_site->save();
				}

			}else if(!empty($post['checksite'])){
				$user_site = new UserSite();
				$user_site->site_id = $post['checksite'];
				$user_site->user_id = $user->id;
				$user_site->save();
			}

			$message         = '<div class="alert alert-success"> Update successful </div>';
			\Session::flash('response',$message);
			return redirect('system/users');
	    }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$count = User::count();
		if($count>1)
		{
			try{
			    $obj = User::findOrFail($id);
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
		}else{
			$return = new AjaxResponse(503,'Can not delete this user');
		}	
		return $return->getJson();	
	}
}
