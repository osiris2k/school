<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormValue extends Model{
	use SoftDeletes;
	public $timestamps = false;

	public function	submission()
	{
		return $this->belongsTo('\App\Submission');
	}	

	public function formProperty()
	{
		return $this->belongsTo('\App\FormProperty','form_property_id');
	}
}
?>