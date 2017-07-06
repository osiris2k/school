<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteProperty extends Model{
	
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "site_properties";

	public function dataType()
	{
		return $this->belongsTo('App\DataType');
	}
	public function sites()
	{
		return $this->belongsToMany('App\Site','site_values')->withPivot('value');
	}
}
?>