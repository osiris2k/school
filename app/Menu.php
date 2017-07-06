<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model{
	use SoftDeletes;

	public function menuGroup()
	{
		return $this->belongsTo('App\MenuGroup');
	}

	public function content()
	{
		return $this->belongsTo('App\Content');
	}

	public function languages()
	{
		return $this->belongsToMany('App\Language','language_menus')->withPivot('label','language_id','detail');
	}
		
}
?>