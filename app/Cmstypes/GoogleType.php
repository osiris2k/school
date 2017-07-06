<?php namespace App\CmsTypes;

class GoogleType{
	
	protected $latitude;
	protected $longitude;

	public function setType($latitude,$longitude)
	{		
		$this->latitude  = $latitude;
		$this->longitude = $longitude;
	}

	public function getValue()
	{	
		$map['latitude']   = $this->latitude;
		$map['longitude']  = $this->longitude;
		$value = array('value'=>json_encode($map));
		return $value;
	}
}
?>