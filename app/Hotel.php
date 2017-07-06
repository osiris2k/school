<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Hotel
 *
 * @package App
 */
class Hotel extends Model
{
    protected $table = 'hotels';

    /**
     * Relation with hotel_values table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotelValues()
    {
        return $this->hasMany('\App\HotelValue');
    }

    /**
     * Relation with hotel_property table at hotel_values table
     *
     * @return $this
     */
    public function hotelProperties()
    {
        return $this->belongsToMany('App\HotelProperty', 'hotel_values')->withPivot('value');
    }

    /**
     * Relation with menu_groups table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menuGroups()
    {
        return $this->belongsToMany('App\MenuGroup', 'hotels_menu_groups')->withPivot('hotel_id', 'menu_group_id')->withTimestamps();
    }

    /**
     * Relation with contents table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents()
    {
        return $this->belongsToMany('App\Content', 'hotels_contents')->withPivot('hotel_id', 'content_id', 'is_homepage_content')->withTimestamps();

    }

    /**
     * Relation with form_objects table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function form_objects()
    {
        return $this->belongsToMany('App\Content', 'hotels_form_objects')->withPivot('hotel_id', 'form_object_id')->withTimestamps();

    }

    /**
     * Return Hotel data
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function listData()
    {
        return Hotel::all();
    }

    /**
     * Custom where query
     *
     * @param        $query
     * @param        $filterColumn
     * @param string $expression
     * @param        $value
     * @return mixed
     */
    public function scopeCustomWhere($query, $filterColumn, $expression = '=', $value)
    {
        return $query->where($filterColumn, $expression, $value);
    }
}


