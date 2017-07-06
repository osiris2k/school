<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

use App\Libraries\UserEmail;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Session;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */


    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    /*   public function __construct()
       {
           $this->middleware('guest');
       }*/
    public function getEmail()
    {
        return view('system.password.reset_password');

    }

    public function postEmail()
    {
        $input = Input::all();

        $rule = ['email' => 'required|email|exists:users'];
        $validator = Validator::make($input, $rule);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);

        } else {

            $user = User::whereEmail(Input::get('email'))->first();
            $user->token = md5(uniqid() . rand(0, 1000));
            $user->save();
            if (!empty($user->id)) {
                UserEmail::sendResetPasswordEmail($user);
            }

            return response()->json(['message' => 'We have just send you an email. Please check your email to reset your password.'], 200);
        }
    }

    public function getResetPassword($token)
    {

        return view('system.password.new_password', ['token' => $token]);

    }

    public function postNewPassword()
    {
        $token = Input::get('user_token');
        $user = User::whereToken($token)->first();

        if (!empty($user)) {

            $post = Input::all();

            $rules = array(
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required|min:6'
            );

            $validator = Validator::make($post, $rules);

            if ($validator->fails()) {

                return response()->json($validator->errors(), 400);

            } else {

                $user->token = null;
                $user->password = Hash::make($post['password']);
                $user->save();
                UserEmail::sendResetPasswordConfirmation($user, $post['password']);

                Auth::loginUsingId($user->id);

                $redirect_page = url('system/menu-groups');
                $data = [
                    'message' => 'Reset password successful',
                    'redirect_page' => $redirect_page
                ];
                return response()->json($data, 200);
            }
        } else {
            return response()->json(['message' => 'Token problem'], 400);
        }
    }
}

?>
