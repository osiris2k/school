<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $table = 'countries';

    protected $primaryKey = 'id';

    protected $fill = ['*'];

    /**
     * Define Language relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany('App\Language', 'languages_countries', 'country_id', 'language_id');
    }

    /**
     * Generate Country list by continent
     *
     * @return array
     */
    public static function getCountryByContinentKey()
    {
        $returnData = array();
        foreach (self::all() as $countryIndex => $country) {
            $returnData[$country->continent_name][] = [
                'country_id'   => $country->id,
                'country_code' => $country->country_code,
                'country_name' => $country->country_name
            ];
        }

        return $returnData;
    }

}
