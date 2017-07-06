<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSite extends Model {


	//
	public function sites()
	{
		return $this->belongsTo('App\Site');
	}

}
