<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormObject extends Model
{

    use SoftDeletes;

    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public function formProperties()
    {
        return $this->hasMany('App\FormProperty');
    }

    /**
     * Relation with hotels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsToMany('App\Hotel', 'hotels_form_objects')->withPivot('hotel_id', 'form_object_id')->withTimestamps();
    }
}

?>