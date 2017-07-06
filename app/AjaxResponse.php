<?php namespace App;

class AjaxResponse{
	var $code;
	var $message;
	var $data;

	function __construct($code,$message,$data=null){

		$this->code    = $code;
		$this->message = $message;
		$this->data    = $data;
	}

	function setData($data)
	{
		$this->data = $data;
	}

	function getJson()
	{
		$return         = json_encode($this);
		return $return;
	}
}