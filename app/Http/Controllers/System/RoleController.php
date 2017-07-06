<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class RoleController extends Controller {


	public function __construct()
	{
		$this->middleware('auth');
	}
	
	public function rules()
	{
		return ['name' => 'required|unique'];
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
		// 
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$post = \Request::all();
		$v = \Validator::make($post->all(), $this->rules());
		if($v->fails())
		{
			return redirect()->back()->withErrors($v->errors());
		}
		$role = new \Role();
		$role = $post['name'];
		$role = $post['is_defult'];
		$role->save();
		$message = '';
		\Session::flash('response',$message);
		return redirect('system/roles');
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
		$role = \Role::find($id);
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
		$v = \Validator::make($post->all(), $this->rules());
		if($v->fails())
		{
			return redirect()->back()->withErrors($v->errors());
		}
		$role = new \Role::find($id);
		$role = $post['name'];
		$role = $post['is_defult'];
		$role->save();
		$message = '';
		\Session::flash('response',$message);
		return redirect('system/roles');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$role = \Role::find($id);
		$role->delete();
		$message = '';
		\Session::flash('response',$message);
		return redirect('system/roles');
	}

}
