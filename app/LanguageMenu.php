<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class LanguageMenu extends Model{

	public function menu()
	{
		return $this->belongTo('App\Menu')->withPivot('label','language_id','menu_id','detail');
	}		
}
?>