<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPropertyLabel extends Model{
	use SoftDeletes;
	public function formProperty()
	{
		return $this->belongsTo('App\FormProperty');
	}
}
?>