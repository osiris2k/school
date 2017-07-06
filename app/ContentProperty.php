<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentProperty extends Model
{

    use SoftDeletes;

    public function contentObject()
    {
        return $this->belongsTo('App\ContentObject');
    }

    public function contents()
    {
        return $this->belongsToMany('App\Content', 'content_values')->withPivot('value', 'language_id', 'content_object_id');
    }

    public function contentValue()
    {
        return $this->hasMany('App\ContentValue', 'content_property_id', 'id');
    }

    public function dataType()
    {
        return $this->belongsTo('App\DataType');
    }

    public function scopeFilterBySiteId($query, $siteIds = [])
    {
        return $query->whereIn('contents.site_id', $siteIds);
    }

    public function scopeFilterByDataTypeId($query, $dataTypeIds = [])
    {
        return $query->whereIn('content_properties.data_type_id', $dataTypeIds);
    }

    public function scopeFilterByContentObjectTypesId($query, $contentObjTypeIds = [])
    {
        return  $query->whereIn('content_objects.content_object_types_id', $contentObjTypeIds);
    }

    public function scopeFilterByLanguageInitial($query)
    {
        return  $query->where('languages.initial', '=', 1);
    }

    public static function getContentPropForReportFile()
    {
        return static::select(
            'contents.id AS content_id',
            'content_values.id AS content_value_id',
            'content_values.content_object_id AS content_object_id',
            'content_properties.id AS content_property_id',
            'content_values.language_id AS language_id',
            'contents.name AS content_name',
            'content_properties.name AS content_property_name',
            'content_properties.variable_name AS content_property_variable_name',
            'content_values.value AS content_value',
            'languages.name AS language_name',
            'languages.initial AS language_initial'
        )
            ->leftJoin('content_objects', 'content_objects.id', '=', 'content_properties.content_object_id')
            ->leftJoin('content_values', 'content_values.content_property_id', '=', 'content_properties.id')
            ->leftJoin('contents', 'contents.id', '=', 'content_values.content_id')
            ->leftJoin('languages', 'languages.id', '=', 'content_values.language_id')
            ->orderBy('contents.name', 'ASC')
            ->orderBy('content_properties.priority', 'ASC');
    }


}

?>