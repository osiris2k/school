<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MenuGroup extends Model
{

    use SoftDeletes;

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function menus()
    {
        return $this->hasMany('App\Menu');
    }

    /**
     * Relation with hotels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsToMany('App\Hotel', 'hotels_menu_groups')->withPivot('hotel_id', 'menu_group_id')->withTimestamps();
    }
}

?>