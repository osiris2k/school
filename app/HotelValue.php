<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HotelValue
 *
 * @package App
 */
class HotelValue extends Model
{
    protected $table = 'hotel_values';

    /**
     * Relation with hotels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsTo('\App\Hotel');
    }

    /**
     * Relation with hotel_properties table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotelProperty()
    {
        return $this->belongsTo('\App\HotelProperty');
    }

    /**
     * Relation with languages table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Language', 'language_id', 'id');
    }
}


