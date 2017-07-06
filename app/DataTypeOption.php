<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DataTypeOption extends Model{
	
	public function dataType()
	{
		return $this->belongsTo('DataType');
	}
}
?>