<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirecturl extends Model{

	use SoftDeletes;

	public function sites()
	{
		return $this->belongsTo('App\Site');
	}
}
?>