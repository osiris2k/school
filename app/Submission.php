<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model{
	
    use SoftDeletes;
	public $timestamps = false;

	public function FormObject()
    {
        return $this->belongsTo('App\FormObject');
    }       

    public function FormProperties()
    {
        return $this->belongsToMany('App\FormProperty','form_values')->withPivot('value','form_object_id');
    }
}
?>