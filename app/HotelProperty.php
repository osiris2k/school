<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Hotel Property
 *
 * @package App
 */
class HotelProperty extends Model
{
    protected $table = 'hotel_properties';

    /**
     * Relation with data_types table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dataType()
    {
        return $this->belongsTo('App\DataType');
    }

    /**
     * Relation with hotel table at hotel_values table
     *
     * @return $this
     */
    public function hotels()
    {
        return $this->belongsToMany('App\Hotel', 'hotel_values')->withPivot('value');
    }

    /**
     * Relation with hotel_values table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotelValues()
    {
        return $this->hasMany('App\HotelValue');
    }
}


