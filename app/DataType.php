<?php namespace App;

// TODO Hotel Property Relation


use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{

    public function dataTypeOptions()
    {
        return $this->hasMany('DataTypeOption');
    }

    public function formProperties()
    {
        return $this->hasMany('FormProperty');
    }

    public function contentProperties()
    {
        return $this->hasMany('ContentProperty');
    }

    public function siteProperties()
    {
        return $this->hasMany('SiteProperty');
    }

    public function hotelProperties()
    {
        return $this->hasMany('App\HotelProperty');
    }

}

?>