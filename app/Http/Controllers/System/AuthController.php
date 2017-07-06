<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Input;

class AuthController extends Controller {

	public function login()
	{
		if(Auth::check()){
			return redirect()->intended('system/dashboard');
		}
		return view('system.login');
	}

	public function auth()
	{
		$email    = Input::get('email');
		$password = Input::get('password');

		Auth::attempt(['email' => $email, 'password' => $password]);
		if(Auth::check()){
			return redirect()->intended('system/dashboard');
		}else{
			\Session::flash('message', 'Email or password is incorrect.');
			return redirect('system/login');
		}
	}

	public function logout()
	{
		Auth::logout();
		return redirect('system/login');
	}
}
