<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteValue extends Model{
	
	use SoftDeletes;
	public $timestamps = false;

	public function siteProperties()
	{
		return $this->belongsToMany('App\SiteProperty');
	}

	public function sites()
	{
		return $this->belongsToMany('App\Site');
	}
}
?>