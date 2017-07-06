<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{

    use SoftDeletes;

    public function siteProperties()
    {
        return $this->belongsToMany('App\SiteProperty', 'site_values')->withPivot('value');
    }

    public function menuGroups()
    {
        return $this->hasMany('App\MenuGroup');
    }

    public function menus()
    {
        return $this->hasMany('App\menu');
    }

    public function contents()
    {
        return $this->hasMany('App\Content');
    }

    public function siteLanguages()
    {
        return $this->belongsToMany('App\Language', 'site_languages', 'site_id', 'language_id');
    }

    public function userSite()
    {
        return $this->hasMany('App\UserSite');
    }

    public function scopeOrderData($query, $orderKey = 'created_at', $orderType = 'ASC')
    {
        return $query->orderBy($orderKey, $orderType);
    }
}

?>