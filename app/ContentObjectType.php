<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentObjectType extends Model
{

    protected $table = 'content_object_types';

    protected $primaryKey = 'id';

    protected $fill = ['*'];

    public function content_objects()
    {
        return $this->hasMany('App\ContentObject', 'content_object_types_id', 'id');
    }

    public static function getAllContentObjectType()
    {
        return static::where('active', 1)->get();
    }

    public function scopeOrderData($query, $orderKey = 'created_at', $orderType = 'ASC')
    {
        return $query->orderBy($orderKey, $orderType);
    }

}
