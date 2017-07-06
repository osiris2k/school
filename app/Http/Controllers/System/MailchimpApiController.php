<?php
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

require("system/vendors/mailchimp/autoload.php");

class MailchimpApiController extends Controller
{
    private $api = null;

    public function __construct()
    {
        $this->api = $this->getInstance();
    }

    public function getInstance()
    {
        if (empty($this->api)) {
            $apikey = env('MAILCHIMP_API_KEY', '');
            return new \Drewm\MailChimp($apikey);
        } else {
            return $this->api;
        }
    }

    public function signup()
    {
        $connection = $this->getInstance();
        $primary_list = env('MAILCHIMP_PRIMARY_LIST', '');

        $data = \Request::all();

        if (empty($data['honey_pot']) && !empty($data['MERGE0'])) {

            $result = $connection->call('lists/subscribe', array(
                'id' => $primary_list,
                'email' => ['email' => $data['MERGE0']]
            ));

            return response()->json(["message" => "success", "data" => $result, 200]);
        } else {
            return response()->json(["message" => "error"], 403);
        }
    }
}