<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteLanguages extends Model
{

	public function language()
	{
		return $this->belongsTo('App\Language');
	}

	public function sites()
	{
		return $this->belongsTo('App\Site');
	}

}
