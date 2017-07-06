<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentObject extends Model
{

    use SoftDeletes;

    public function contents()
    {
        return $this->hasMany('App\Content');
    }

    // public function contentValues()
    // {
    // 	return $this->hasMany('App\ContentValue');
    // }

    public function contentProperties()
    {
        return $this->hasMany('App\ContentProperty');
    }

    public function content_object_type()
    {
        return $this->belongsTo('App\ContentObjectType', 'content_object_types_id', 'id');
    }

    public function scopeOrderData($query, $orderKey = 'created_at', $orderType = 'ASC')
    {
        return $query->orderBy($orderKey, $orderType);
    }

    public function scopeFilterByContentObjectType($query, $contentObjTypeId = [])
    {
        return $query->whereIn('content_objects.content_object_types_id', $contentObjTypeId);
    }


}

?>