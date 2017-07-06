<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormProperty extends Model{
	
	use SoftDeletes;
	public function submissions()
    {
		return $this->belongsToMany('App\Submission','form_values')->withPivot('value','form_object_id');
    }

	public function dataType()
	{
		return $this->belongsTo('App\DataType');
	}

	public function formPropertyLabels()
	{
		return $this->hasMany('App\FormPropertyLabel');
	}
}
?>