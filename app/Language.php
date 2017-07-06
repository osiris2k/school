<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{

    use SoftDeletes;

    /**
     * Define Country relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function countries()
    {
        return $this->belongsToMany('App\Country', 'languages_countries', 'language_id', 'country_id');
    }

    public function formProperties()
    {
        return $this->hasMany('App\FormProperty');
    }

    public function ContentValues()
    {
        return $this->hasMany('App\ContentValue', 'language_id', 'id');
    }

    public function Translations()
    {
        return $this->belongsToMany('App\Language', 'language_translation')->withPivot('values', 'language_id');
    }

    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'language_menus')->withPivot('label', 'language_id');
    }

    public function siteLanguages()
    {
        return $this->hasMany('App\SiteLanguages');
    }

    public function HotelValues()
    {
        return $this->hasMany('App\HotelValue', 'language_id', 'id');
    }

}

?>