<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{

    use SoftDeletes;

    public function contentValues()
    {
        return $this->hasMany('App\ContentValue');
    }

    public function contentObject()
    {
        return $this->belongsTo('App\ContentObject');
    }

	public function contentParents()
	{
		return $this->belongsToMany('\App\Content', 'contents_parents', 'content_id', 'parent_id')->withPivot('content_id', 'parent_id')->withTimestamps();
	}

	public function contentChildren()
	{
		return $this->belongsToMany('\App\Content', 'contents_parents', 'content_id', 'parent_id')->withPivot('content_id', 'parent_id')->withTimestamps();
	}

    public function menu()
    {
        return $this->belongsTo('App\Menu');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Relation with hotels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsToMany('App\Hotel', 'hotels_contents')->withPivot('hotel_id', 'content_id', 'is_homepage_content')->withTimestamps();

    }

    public function contentProperties()
    {
        return $this->belongsToMany('App\ContentProperty', 'content_values')->withPivot('value', 'language_id', 'content_object_id');
    }

    public function getParentDataAttribute()
    {
        return static::find($this->parent_content_id);
    }

    public function scopeOrderData($query, $orderKey = 'created_at', $orderType = 'ASC')
    {
        return $query->orderBy($orderKey, $orderType);
    }

    public static function getContentByContentObj($contentObjId = [])
    {
        if (session('USER_ACCESS_SITE_DATA') === 'ALL') {
            return static::whereIn('content_object_id', $contentObjId);
        } else {
            return static::whereIn('content_object_id', $contentObjId)->whereIn('site_id', session('USER_ACCESS_SITE_DATA'));
        }
    }

    public function scopeFilterByContentId($query, $contentId, $condition = '=')
    {
        return $query->where('contents.id', $condition, $contentId);

    }

    public function scopeFilterByContentObjectTypeId($query, $contentObjectTypeId = [])
    {
        return $query->whereHas('contentObject.content_object_type', function ($query) use ($contentObjectTypeId) {
            $query->whereIn('content_object_types.id', $contentObjectTypeId);
        });
    }

    public function scopeFilterBySiteId($query, $siteIds = [])
    {
        return $query->whereIn('contents.site_id', $siteIds);
    }

    public function scopeFilterByContentObjectId($query, $contentObjIds = [])
    {
        return $query->whereIn('contents.content_object_id', $contentObjIds);
    }

    public static function getContentWithParent()
    {
        return static::selectRaw(self::getContentQuery())
            ->leftJoin('sites', 'contents.site_id', '=', 'sites.id')
            ->leftJoin('content_objects', function ($join) {
                $join->on('content_objects.id', '=', 'contents.content_object_id');
            })
            ->leftJoin('contents as parent', function ($join) {
                $join->on('contents.parent_content_id', '=', 'parent.id')
                    ->where('parent.parent_content_id', '!=', 0);
            })
            ->leftJoin('contents as parent2', function ($join) {
                $join->on('contents.parent_content_id', '=', 'parent2.id')
                    ->where('parent2.parent_content_id', '!=', 0);
            })
            ->orderBy('contents.site_id', 'ASC')
            ->orderBy('order_name', 'ASC')
            ->orderBy('contents.name', 'ASC');
    }

    public static function getContentQuery()
    {
	    // TODO Edit query to support multi parent content

        return <<<QUERY_STR
            contents.id, contents.content_object_id, contents.slug, contents.name, contents.active,contents.parent_content_id,
            (SELECT content_parent.name FROM contents content_parent WHERE content_parent.id = contents.parent_content_id) AS content_parent_name,
            sites.id AS site_id, sites.name AS site_name,
            content_objects.name as content_object_name,
            CONCAT(IF(parent2.name IS NULL,
                IF(parent.name IS NULL,
                    IF(parent.parent_content_id IS NULL
                            AND parent2.parent_content_id IS NULL
                            AND contents.parent_content_id != 0,
                        CONCAT((
                                SELECT
                                    contents_sub_level_2.name
                                FROM
                                    contents AS contents_sub_level_2
                                WHERE
                                    contents_sub_level_2.id = contents.parent_content_id),
                                '.'),
                        ''),
                    CONCAT(parent.name, '.')),
                CONCAT((
                        SELECT
                            CONCAT(name, '.') AS menu_name
                        FROM
                            contents
                        WHERE
                            id = parent2.parent_content_id),
                        CONCAT(parent2.name, '.'))),
            contents.name) order_name,
            IF(parent2.id = contents.parent_content_id, 3, IF(parent.parent_content_id IS NULL
                AND parent2.parent_content_id IS NULL
                AND contents.parent_content_id != 0, 2, 1)) level
QUERY_STR;
    }

}

?>