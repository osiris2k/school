<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model {

	use SoftDeletes;
	
	public function Languages()
    {
		return $this->belongsToMany('App\Language','language_translation')->withPivot('values','language_id');
    }

}
