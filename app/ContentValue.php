<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentValue extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    public function content()
    {
        return $this->belongsTo('App\Content');
    }

    public function language()
    {
        return $this->belongsTo('App\Language', 'language_id', 'id');
    }

    public function contentProperty()
    {
        return $this->belongsTo('App\ContentProperty', 'content_property_id', 'id');
    }

    //    public function contentObject()
    //    {
    //    	return $this->belongsTo('App\contentObject');
    //    }

    public function scopeFilterByContentPropertyId($query, $contentPropertyIds = [])
    {
        return $query->whereIn('content_values.content_property_id', $contentPropertyIds);
    }

    public function scopeFilterByLangId($query, $langIds = [])
    {
        return $query->whereIn('content_values.language_id', $langIds);
    }

    public function scopeFilterByContentObjectId($query, $contentObjectIds = [])
    {
        return $query->whereIn('content_values.content_object_id', $contentObjectIds);

    }

    public function scopeFilterByContentId($query, $contentIds = [])
    {
        return $query->whereIn('content_values.content_id', $contentIds);
    }

    public function scopeFilterByContentObjectTypeId($query, $contentObjectTypeIds = [])
    {
        return $query->whereIn('content_objects.content_object_types_id', $contentObjectTypeIds);

    }

    public function scopeFilterByDataTypeId($query, $dataTypeIds = [])
    {
        return $query->whereIn('content_properties.data_type_id', $dataTypeIds);
    }

    public function scopeFilterBySiteId($query, $siteIds = [])
    {
        return $query->whereIn('contents.site_id', $siteIds);
    }

    public static function getContentValueForExportFile()
    {
        return static::select('content_values.id', 'contents.name AS content_name', 'content_values.value AS content_value')
            ->leftJoin('content_objects', 'content_objects.id', '=', 'content_values.content_object_id')
            ->leftJoin('content_properties', 'content_properties.id', '=', 'content_values.content_property_id')
            ->leftJoin('contents', 'contents.id', '=', 'content_values.content_id')
            ->leftJoin('languages', 'languages.id', '=', 'content_values.language_id');
    }

}

?>