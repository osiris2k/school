<?php
namespace App\Libraries;

use App\Helpers\CmsHelper;
use App\Helpers\ViewHelper;
use Mail;

class UserEmail
{
    public static function sendVerifyEmail($user,$input){

        $data = [
            'email' => $user->email,
            'password' => $input['password'],
            'token' => $user->token
        ];

        Mail::send('system.email.user_verification_email',$data, function($message) use($user)
        {
            $site = ViewHelper::getMainSite();
            $message->from('cms@mg.quo-global.com',$site->name);
            $message->to($user->email)->subject(' Confirmation');
        });
    }
    public static function sendResetPasswordEmail($user){

        $data = [
            'token' => $user->token
        ];
        Mail::send('system.email.user_reset_password',$data, function($message) use($user)
        {
            $site = ViewHelper::getMainSite();
            $message->from('cms@mg.quo-global.com',$site->name);
            $message->to($user->email)->subject(' Reset your password');
        });
    }
    public static function sendResetPasswordConfirmation($user,$password){

        $user_fullname = $user->firstname." ".$user->lastname;
        $data = [
            'user_fullname' => $user_fullname,
            'email' => $user->email,
            'password' => $password,
        ];
        Mail::send('system.email.user_reset_password_confirmation',$data, function($message) use($user)
        {
            $site = ViewHelper::getMainSite();
            $message->from('cms@mg.quo-global.com',$site->name);
            $message->to($user->email)->subject(' Reset your password successful');
        });
    }
}

?>