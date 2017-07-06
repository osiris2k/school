<?php namespace App\Helpers;

use App\Content;
use App\ContentObject;
use App\ContentParent;
use App\ContentValue;
use App\Language;
use App\Libraries\ArrayLib;
use App\Menu;
use ChrisKonnertz\OpenGraph\OpenGraph;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Session;
use \Request;
use \Redirect;

class ViewHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'viewhelper';
    }

    // === site === //
    public static function getMainSite()
    {
        $site = \App\Site::where('main_site', '=', 1)->first();

        return $site;
    }

    public static function getSiteIDByName($site_name)
    {
        $id = \App\Site::where('name', '=', $site_name)->first()->id;

        return $id;
    }

    public static function getMenuBySiteId($site_id)
    {
        $menu_group = \App\MenuGroup::with(['menus' => function ($query) use ($language_id) {
            $query->orderBy('priority', 'asc');
            $query->with(['Languages' => function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            }]);
        }])->where('site_id', '=', $site_id)->get();

        return $menu_group;
    }

    public static function getMenuByGroupName($name, $site_id, $language_id = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');

        /** If enable Hotel Feature and pass hotel argument into this function */
        $specificContentId = [];
        if (HotelHelper::isEnable() && $hotel !== null) {
            $specificContentId = $hotel->contents->lists('id');
        }

        $menu_group = \App\MenuGroup::with(['menus' => function ($query) use ($language_id, $specificContentId) {
            /** Get Menu by Menu, Contents, Hotels relation */
            if (count($specificContentId) > 0) {
                $query->whereIn('content_id', $specificContentId);
            }

            $query->orderBy('priority', 'asc');
            $query->where('active', '=', 1);
//            $query->where('parent_menu_id', '=', 0);
            $query->with(['Languages' => function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            }]);
        }])
            ->where('name', '=', $name)
            ->where('site_id', '=', $site_id)
            ->first();

        $menus = array();
        foreach ($menu_group->menus as $menu) {
            $obj = json_decode(json_encode($menu), false);
            if (isset($menu->Languages[0]) && trim($menu->Languages[0]->pivot->label) != '') {
                $obj->label = $menu->Languages[0]->pivot->label;
                $obj->detail = $menu->Languages[0]->pivot->detail;
            } else {
                if ($menu->content == null) {
                    if ($menu->Languages->count() == 0) {
                        $initial_language = \App\Language::whereInitial(1)->first();
                        $lbl = \App\LanguageMenu::where('menu_id', '=', $menu->id)->where('language_id', '=', $initial_language->id)->first();
                        $obj->label = $lbl->label;
                    } else {
                        $obj->label = $menu->Languages[0]->pivot->label;
                    }
                } else {

                    $default_label = $menu->content->contentProperties()
                        ->where('variable_name', '=', 'title')
                        ->where('language_id', '=', $language_id)
                        ->first();

                    if ($default_label) {
                        $obj->label = $default_label->pivot->value;
                    } else {

                        //in case of no content is prefered language
                        $initial_language = \App\Language::whereInitial(1)->first();
                        $default_label = $menu->content->contentProperties()
                            ->where('variable_name', '=', 'title')
                            ->where('language_id', '=', $initial_language->id)
                            ->first();
//                        dd($obj, $default_label, $initial_language->id);

                        if ($default_label) {

                            $obj->label = $default_label->pivot->value;
                        } else {
                            $obj->label = $menu->Languages()->where('language_id', '=', 1)->first()->pivot->label;
                        }
                    }
                }
            }

            $obj->children = self::getSubMenu($obj->id,$language_id);
            $menus[] = $obj;
        }
        $return = new \stdClass();
        $return->menus = $menus;

        return $return;
    }

    public static function getSubMenu($parent_id,$language_id = null)
    {
        $sub_menu = Menu::where('parent_menu_id', '=', $parent_id)
            ->where('active', '=', 1)
            ->orderBy('priority', 'asc')
            ->get();
        $return = [];
        if(count($sub_menu)){
            foreach ($sub_menu as $item){
                $item->label = $item->Languages()->where('language_id', '=', $language_id)->first()->pivot->label;
                $return[] = $item;
            }
        }
        return $return;
    }

    public static function getBreadcrumb($content_id, $language_id = false, $chain = array())
    {
        if ($language_id === false) $language_id = app('user_current_language');

        $content = \App\Content::find($content_id);
        $obj['content_id'] = $content->id;
        $obj['slug'] = $content->slug;
        $content_values = \App\Content::find($content->id)
            ->contentProperties()
            ->whereVariableName('title')
            ->whereLanguageId($language_id)->first();

        $obj['caption'] = isset($content_values->pivot->value) ? $content_values->pivot->value : '';

        array_push($chain, $obj);

        if (isset($content->parent_content_id) && $content->parent_content_id > 0) {
            return self::getBreadcrumb($content->parent_content_id, $language_id, $chain);
        } else {
            return array_reverse($chain);
        }

        return null;
    }


    public static function getSubSite($id)
    {
        $sub_sites = \App\Site::where('parent_id', '=', $id)->get();
        $sub_sites_information = array();
        foreach ($sub_sites as $sub_site) {
            $sub_sites_information[] = self::getSiteInformation($sub_site->id);
        }

        return $sub_sites_information;
    }

    public static function getSiteInformation($id)
    {
        $obj = \App\Site::find($id);
        $properties = $obj->siteProperties;
        $value = array();
        foreach ($properties as $priority) {
            $value[$priority->variable_name] = CmsHelper::getValueByPropertyID($id, $priority->id, 'site');
            if ($priority->data_type_id == 10) {
                $value[$priority->variable_name] = json_decode($value[$priority->variable_name]);
            }
        }

        return $value;
    }

    // ===  end site === //

    // === content === //
    private static function getContentByInitialLanguage($queryLang, $contentObj)
    {
        $contentCurrentLang = $contentObj->contentProperties()->where('language_id', '=', $queryLang)->get();
        if (count($contentCurrentLang) == 0) {
            $initialLangId = Language::where('initial', '=', 1)->first()->id;

            $contentCurrentLang = $contentObj->contentProperties()->where('language_id', '=', $initialLangId)->get();
        }

        return $contentCurrentLang;
    }

    public static function getContentBySlug($slug, $site_id, $language_id = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');

        $obj = \App\Content::where('slug', '=', $slug)->where('site_id', '=', $site_id)->whereActive(1);

        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $obj = $obj->whereHas('hotel', function ($query) use ($hotel) {
                $query->where('hotels_contents.hotel_id', '=', $hotel->id);
            });
        }

        $obj = $obj->first();

        if ($obj) {
//            $tmps = $obj->contentProperties()->where('language_id', '=', $language_id)->get();
            $tmps = self::getContentByInitialLanguage($language_id, $obj);
            $pack = ViewHelper::packArray($tmps, $language_id);
            $pack['id'] = $obj->id;
            $pack['slug'] = $slug;
            $pack['active'] = $obj->active;
            $pack['template'] = str_replace(" ", "_", $obj->contentObject->name);
            $pack['parents_contents'] = ViewHelper::getParentContent( $obj->id, $language_id, $hotel );
            $pack['sub_content'] = ViewHelper::getSubContent($obj->id, $language_id, $hotel);

            return $pack;
        }
    }

    public static function getContentURI($content_id, $language_id = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');

        $content = \App\Content::with('site')->find($content_id);

        if ($content->contentObject->name == 'homepage') {
            /** If is Homepage no slug */
            $slug = '';
        } else {
            $slug = $content->slug;
        }

        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $isHotelHomepage = explode('_', $content->contentObject->name);
            if (in_array('homepage', $isHotelHomepage)) {
                $slug = $hotel->slug;
            } else {
                $slug = $hotel->slug . '/' . $slug;
            }
        }

        $link = '';
        if (!$content->site->main_site) {
            $link .= '/' . $content->site->name;
        }
        $link .= '/' . $slug;
        $language = \App\Language::find($language_id);

        if (!session('GEOIP_LANG_ID')) {
            if (!$language->initial) {
                $link = ($content->contentObject->name == 'homepage') ? '/' . $language->name . '' : '/' . $language->name . $link;
            }
        } else {
            if ($language_id != session('GEOIP_LANG_ID')) {
                $link = '/' . $language->name . $link;
            }
        }

        return $link;
    }

    public static function getContentByContentObject($obj_name, $site_id, $language_id = false, $options = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');
        $content_object = \App\ContentObject::where('name', '=', $obj_name)->first();
        $content_object_id = 0;
        if($content_object){
            $content_object_id = $content_object->id;
        }
        $contents = \App\Content::where('content_object_id', '=', $content_object_id)->where('contents.site_id', '=', $site_id);
        if (isset($options['limit'])) {
            $contents = $contents->limit($options['limit']);
        }
        if (isset($option['random'])) {
            if ($option['random'])
                $contents = $contents->random($option['random']);
        }
        if (isset($options['except_id'])) {
            $excepts = $options['except_id'];
            foreach ($excepts as $id) {
                $contents = $contents->where('contents.id', '<>', $id);
            }
        }
        if (isset($options['where_self'])) {
            $options['where_self'];
            $contents = $contents->where($options['where_self']['condition'],
                $options['where_self']['expression'],
                $options['where_self']['value']);
        }

        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $contents = $contents->whereHas('hotel', function ($query) use ($hotel) {
                $query->where('hotels_contents.hotel_id', '=', $hotel->id);
            });
        }

        $contents = $contents->where('contents.active', 1)->orderBy('display_order')->get();

        $content = array();
        $i = 0;
        foreach ($contents as $sub_content) {
            $slug = \App\Content::find($sub_content->id)->slug;
            $obj = \App\Content::find($sub_content->id);
//            $tmps = \App\Content::find($sub_content->id)->contentProperties()->where('language_id', '=', $language_id)->get();
            $tmps = self::getContentByInitialLanguage($language_id, $obj);
            $pack = ViewHelper::packArray($tmps, $language_id);
            $pack['id'] = $sub_content->id;
            $pack['active'] = $sub_content->active;
            $pack['slug'] = $slug;
            $content[] = $pack;
        }
        $contents = $content;

        return $contents;
    }

    public static function getContentByObjectId($id, $site_id, $language_id = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');
        $contents = \App\Content::where('content_object_id', '=', $id);

        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $contents = $contents->whereHas('hotel', function ($query) use ($hotel) {
                $query->where('hotels_contents.hotel_id', '=', $hotel->id);
            });
        }

        $contents = $contents->get();

        $content = array();
        $i = 0;
        foreach ($contents as $sub_content) {
            $slug = \App\Content::find($sub_content->id)->slug;
            $tmps = \App\Content::find($sub_content->id)->contentProperties()->where('language_id', '=', $language_id)->get();
            $obj = \App\Content::find($sub_content->id);
//            $tmps = \App\Content::find($sub_content->id)->contentProperties()->where('language_id', '=', $language_id)->get();
            $tmps = self::getContentByInitialLanguage($language_id, $obj);
            $pack = ViewHelper::packArray($tmps, $language_id);
            $pack['id'] = $sub_content->id;
            $pack['slug'] = $slug;
            $pack['active'] = $sub_content->active;
            $content[] = $pack;
        }
        $contents = $content;

        return $contents;
    }

    public static function getContentById($id, $language_id = false, $hotel = null)
    {
        if (!$language_id)
            $language_id = app('user_current_language');

        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $content = Content::where('id', '=', $id)->whereHas('hotel', function ($query) use ($hotel) {
                $query->where('hotels_contents.hotel_id', '=', $hotel->id);
            });
        } else {
            $content = \App\Content::find($id);
        }

        if($content !== null && $content->active !== 0) {
//        $tmps = $content->contentProperties()->where('language_id', '=', $language_id)->get();
            $tmps = self::getContentByInitialLanguage($language_id, $content);
            $pack = ViewHelper::packArray($tmps, $language_id);
            $pack['slug'] = $content->slug;
            $pack['id'] = $content->id;
            $pack['template'] = $content->contentObject->name;
            $pack['active'] = $content->active;
            $pack['sub_content'] = self::getSubContent($content->id, $language_id, $hotel);
            $content = $pack;
            return $content;
        }else{
            return null;
        }
    }

    public static function getContentObjectByContentId($id)
    {
        $content_object_id = ContentValue::whereContentId($id)->first()->content_object_id;

        return ContentObject::find($content_object_id);
    }

    public static function getSubContent( $parent_id, $language_id = false, $hotel = null ) {

        $parentContent = Content::find( $parent_id )->toArray();

        if ( ! $language_id ) {
            $language_id = app( 'user_current_language' );
        }

        /** Hotel feature condition */
        if ( HotelHelper::isEnable() && $hotel !== null ) {
            $sub_contents = \App\Content::whereHas( 'contentChildren', function ( $query ) use ( $parent_id ) {
                $query->where( 'parent_id', '=', $parent_id );
            } )
                ->whereHas( 'hotel', function ( $query ) use ( $hotel ) {
                    $query->where( 'hotels_contents.hotel_id', '=', $hotel->id );
                } )
                ->whereActive( 1 )
                ->orderBy( 'display_order' )
                ->get();
        } else {
            $sub_contents = \App\Content::whereHas( 'contentChildren', function ( $query ) use ( $parent_id ) {
                $query->where( 'parent_id', '=', $parent_id );
            } )
                ->whereActive( 1 )
                ->orderBy( 'display_order' )
                ->get();
        }

        $content = array();
        $i       = 0;
        foreach ( $sub_contents as $sub_content ) {
            $slug = \App\Content::find( $sub_content->id )->slug;
            $obj  = \App\Content::find( $sub_content->id );
//            $tmps = \App\Content::find($sub_content->id)->contentProperties()->where('language_id', '=', $language_id)->get();
            $tmps                                           = self::getContentByInitialLanguage( $language_id, $obj );
            $pack                                           = ViewHelper::packArray( $tmps, $language_id );
            $pack['parent_content']                         = $parentContent;
            $pack['id']                                     = $sub_content->id;
            $pack['active']                                 = $sub_content->active;
            $pack['slug']                                   = $slug;
            $pack['content_object']                         = $sub_content->ContentObject->name;
            $content[ $sub_content->ContentObject->name ][] = ArrayLib::SortByKeyValue( $pack, 'display_order', SORT_ASC );
            $content['mix'][]                               = $pack;
        }
        if ( array_key_exists( 'mix', $content ) ) {
            $content['mix'] = ArrayLib::SortByKeyValue( $content['mix'], 'display_order', SORT_ASC );
        }
        $sub_contents = $content;

        return $sub_contents;
    }

    public static function getParentContent( $childId, $langaugeId = false, $hotel = null ) {
        $returnArray = [ ];

        if ( ! $langaugeId ) {
            $langaugeId = app( 'user_current_language' );
        }

        /** Get Parent Ids */
        $parentIds = ContentParent::where( 'content_id', '=', $childId )->lists( 'parent_id' );

        if ( count( $parentIds ) === 0 || $childId === 0) {
            return $returnArray;
        }

        /** Hotel feature condition */
        if ( HotelHelper::isEnable() && $hotel !== null ) {
            $parentContent = Content::whereIn( 'id', $parentIds )
                ->whereHas( 'hotel', function ( $query ) use ( $hotel ) {
                    $query->where( 'hotels_contents.hotel_id', '=', $hotel->id );
                } )
                ->where( 'active', '=', 1 )
                ->get();
        } else {
            $parentContent = Content::whereIn( 'id', $parentIds )
                ->where( 'active', '=', 1 )
                ->get();
        }

        foreach ( $parentContent as $parentData ) {
            $tmps                = self::getContentByInitialLanguage( $langaugeId, $parentData );
            $pack                = ViewHelper::packArray( $tmps, $langaugeId );
            $pack['slug']        = $parentData->slug;
            $pack['id']          = $parentData->id;
            $pack['active']      = $parentData->active;
            $pack['template'] = $parentData->contentObject->name;
            $pack['sub_content'] = self::getSubContent( $parentData->id, $langaugeId, $hotel );
            $returnArray[]       = $pack;
        }

        return $returnArray;
    }

    public static function getParentContentByObjectAndId($objectName, $childId, $langaugeId = false, $hotel = null)
    {
        $returnArray = [];
        if (!$langaugeId) {
            $langaugeId = app('user_current_language');
        }
        $content_object = \App\ContentObject::where('name', '=', $objectName)->first();
        $content_object_id = 0;
        if ($content_object) {
            $content_object_id = $content_object->id;
        }
        /** Get Parent Ids */
        $parentIds = ContentParent::where('content_id', '=', $childId)->lists('parent_id');

        if (count($parentIds) === 0 || $childId === 0) {
            return $returnArray;
        }
        /** Hotel feature condition */
        if (HotelHelper::isEnable() && $hotel !== null) {
            $parentContent = Content::whereIn('id', $parentIds)
                ->whereHas('hotel', function ($query) use ($hotel) {
                    $query->where('hotels_contents.hotel_id', '=', $hotel->id);
                })
                ->where('active', '=', 1)
                ->get();
        } else {
            $parentContent = Content::whereIn('id', $parentIds)
                ->where('active', '=', 1)
                ->where('content_object_id', '=', $content_object_id)
                ->get();

        }
        foreach ($parentContent as $key=>$parentData) {
            if($parentData->contentObject->name == $objectName) {
                $tmps = self::getContentByInitialLanguage($langaugeId, $parentData);
                $pack = ViewHelper::packArray($tmps, $langaugeId);
                $pack['slug'] = $parentData->slug;
                $pack['id'] = $parentData->id;
                $pack['active']      = $parentData->active;
                $pack['template'] = $parentData->contentObject->name;
                $returnArray[] = $pack;
            }
        }
        $returnData  = array_shift($returnArray);
        return $returnData;
    }


    // === end content === //

    // === form === //
    public static function genFormByName($name, $language_id = false)
    {
        if (!$language_id)
            $language_id = app('user_current_language');
        $data = new \stdClass;
        // $form_object 			= \App\FormObject::where('name','=',$name)->first();
        $form_object = \App\FormObject::whereName($name)->with(['formProperties.formPropertyLabels' => function ($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }])->first();
        $data->properties = array();
        foreach ($form_object->formProperties as $property) {
            $tmp = $property;
            if (isset($property->formPropertyLabels[0])) {
                $tmp->label = $property->formPropertyLabels[0]->label;
                $tmp->label_tooltip = $property->formPropertyLabels[0]->tooltip;
            }
            $data->properties[] = $tmp;
        }
        $data->title = 'Title';
        $data->action = 'form/' . $form_object->id;
        $data->method = 'post';
        $data->site_id = $form_object->site_id;

        return $data;
    }

    public static function getFormByNameAndSiteId($name, $site_id = 0, $language_id = 0)
    {
        if (!empty($language_id)) {
            $language_id = app('user_current_language');
        }
        $data = new \stdClass;
        // $form_object 			= \App\FormObject::where('name','=',$name)->first();
        $form_object = \App\FormObject::whereName($name)->whereSiteId($site_id)->with(['formProperties.formPropertyLabels' => function ($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }])->first();
        $data->properties = array();
        if (!empty($form_object)) {
            foreach ($form_object->formProperties as $property) {
                $tmp = $property;
                if (isset($property->formPropertyLabels[0])) {
                    $tmp->label = $property->formPropertyLabels[0]->label;
                    $tmp->label_tooltip = $property->formPropertyLabels[0]->tooltip;
                }
                $data->properties[] = $tmp;
                $data->action = 'form/' . $form_object->id;
                $data->site_id = $form_object->site_id;
            }
        }

        $data->title = 'Title';
        $data->method = 'post';

        return $data;
    }

    // === end form === //

    public static function getInitialLanguage()
    {
        return Language::where('initial', '=', 1)->first();
    }

    // === other === //

    public static function packArray($tmps, $language_id = false)
    {

        if (!$language_id)
            $language_id = app('user_current_language');

        $return = array();
        foreach ($tmps as $tmp) {

            if ($tmp->data_type_id == 10 || $tmp->data_type_id == 11) {
                $init_value = \App\ContentValue::where('content_id', '=', $tmp->pivot->content_id)
                    ->where('content_property_id', '=', $tmp->pivot->content_property_id)
                    ->where('language_id', '=', $tmp->pivot->language_id)
                    ->first();


                if (count(json_decode($init_value->value)) === 0) {
                    $init_value = \App\ContentValue::where('content_id', '=', $tmp->pivot->content_id)
                        ->where('content_property_id', '=', $tmp->pivot->content_property_id)
                        ->where('language_id', '=', self::getInitialLanguage()->id)
                        ->first();
                }

                $return[$tmp->variable_name] = json_decode($init_value->value);
            } else {
                $init_value = \App\ContentValue::where('content_id', '=', $tmp->pivot->content_id)
                    ->where('content_property_id', '=', $tmp->pivot->content_property_id)
                    ->where('language_id', '=', $tmp->pivot->language_id)
                    ->first();

                $return[$tmp->variable_name] = $init_value->value;
            }

            $return[$tmp->variable_name . '_option'] = $tmp->options;
        }

        return $return;
    }

    public static function getAllContentBySiteId($site_id, $parent_content_id = 0, $language_id = false)
    {
        if (!$language_id)
            $language_id = app('user_current_language');

        $contents = \App\Content::where('parent_content_id', '=', $parent_content_id)
            ->where('site_id', '=', $site_id)
            ->orderBy('name', 'asc')
            ->get();

        $packages = array();

        foreach ($contents as $content) {
//            $pack = ViewHelper::packArray($content->contentProperties()->where('language_id', '=', $language_id)->get(), $language_id);
            $pack = ViewHelper::packArray(self::getContentByInitialLanguage($language_id, $content), $language_id);
            $pack['id'] = $content->id;
            $pack['content_object_id'] = $content->content_object_id;
            $pack['name'] = $content->name;
            $pack['slug'] = $content->slug;
            $pack['active'] = $content->active;
            $pack['content_object_name'] = \App\ContentObject::where('id', '=', $content->content_object_id)->first()->name;
            $pack['updated_at'] = $content->updated_at->toDateTimeString();
            $packages[] = $pack;
        }

        return $packages;
    }

    //====== Translation ====== //
    public static function getTranslation($key, $language_id = false)
    {
        if (!$language_id)
            $language_id = app('user_current_language');
        $translation = \App\Translation::with(['Languages' => function ($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }])->where('key', '=', $key)->first();
        if (isset($translation->Languages)) {
            $tmps = $translation->Languages;
            if (sizeof($tmps) == 0) {
                $languages = CmsHelper::getLanguages();
                foreach ($languages as $language) { // get priority languages
                    $language_id = $language->id;
                    $translation = \App\Translation::with(['Languages' => function ($query) use ($language_id) {
                        $query->where('language_id', '=', $language_id);
                    }])->where('key', '=', $key)->first();
                    $tmps = $translation->Languages;
                    if (sizeof($tmps) != 0) {
                        return $tmps[0]->pivot->values;
                    }
                }

                return $key;
            } else {
                return $tmps[0]->pivot->values;
            }
        } else {
            return $key;
        }
    }
    // ====== End Translation ====== //

    // ==== Begin Image Helper ==== //
    public static function getImageAlt($image)
    {
        if (isset($image->alt) && $image->alt) {
            return $image->alt;
        } else {
            return last(explode('/', $image->image));
        }
    }

    public static function getImageTitle($image)
    {
        if (isset($image->title) && $image->title) {
            return $image->title;
        } else {
            return last(explode('/', $image->image));
        }
    }

    public static function getImageCaption($image)
    {
        if (isset($image->caption) && $image->caption) {
            return $image->caption;
        } else {
            return last(explode('/', $image->image));
        }
    }
    // ==== End Image Helper === //

    // === end other === //

    // === Begin Sitemap helper === //
    /**
     * Generate sitemap data
     *
     * @param int $site_id
     * @param int $parent_id
     *
     * @return array $retval
     */
    public static function genSiteMap($site_id, $parent_id = 0)
    {
        $retval = array();
        $contents = self::getAllContentBySiteId($site_id, $parent_id);
        foreach ($contents as $content) {
            array_push($retval, array(
                'content_id'      => $content['id'],
                'content_url'     => url(self::getContentURI($content['id'])),
                'content_lastmod' => $content['updated_at'],
                'content'         => $content,
                'children'        => self::genSiteMap($site_id, $content['id'])
            ));
        }

        return $retval;
    }

    /**
     * Render HTML site map
     *
     * @param array $data
     * @param array $noLink
     * @param array $except
     * @param array $achorLink
     *
     * @return string
     */
    public static function renderHTMLSiteMap($data, $noLink = array(), $except = array(), $anchorLink = array())
    {
        $retval = '';
        $groupArr = [];
        $contentArr = [];
        foreach ($data as $content) {
            $title = array_key_exists('title',$content['content']) ? $content['content']['title'] : '';

            if ($content['content']['content_object_name'] == 'sitemap' || (in_array($content['content']['content_object_name'], $except) && !in_array($content['content']['content_object_name'], $except)))
                continue;

            if (in_array($content['content']['content_object_name'], $noLink)) {
                $retval .= '<li><a style="color:#f4511a">' . $content['content']['title'] . '</a></li>';
            } else if (array_key_exists(1, $anchorLink) && in_array($content['content']['content_object_name'], $anchorLink)) {
                $contentArr[] = $content;
                $retval .= '<li style="padding:5px;"><a href="' . $content['content_url'] . '" style="color:#777777">' . $content['content']['title'] . '</a></li>';
            }

            if (sizeof($content['children']) > 0) {
                $retval .= self::renderHTMLSiteMap($content['children'], $noLink, $except, $anchorLink);
            }

            $groupArr[$content['content']['content_object_name']][] = $retval;
        }
//        $finalSitemap = '<ul style="margin-top:20px;color:#f4511a">';
//        foreach($anchorLink as $mainGroup){
//            $finalSitemap .='<li><strong>'.strtoupper($mainGroup).'</strong><ul style="margin-bottom:20px; color:#f4511a">'.implode('',$groupArr[$mainGroup]).'</ul></li>';
//        }
//        $finalSitemap .='</ul>';
//        return $finalSitemap;
        return '<ul style="list-style: circle">'.$retval.'</ul>';
    }

    /**
     * Render XML sitemap
     *
     * @param   array $data
     * @param   array $except
     *
     * @return  string
     *
     * @reference http://www.sitemaps.org/protocol.html
     */
    public static function renderXMLSiteMap($data, $except = array(), $templatePoint = [], $languages = [])
    {
        $retval = '';
        foreach ($data as $content) {
            if ($content['content']['content_object_name'] == 'sitemap'
                || in_array($content['content']['content_object_name'], $except)
            )
                continue;

            /**
             * Sitemap priority and changefreq
             */
            $templateName = $content['content']['content_object_name'];
            $priority = 0.5;
            $changeFreq = 'weekly';

            if (array_key_exists($templateName, $templatePoint)) {
                $priority = $templatePoint[$templateName]['priority'];
                $changeFreq = $templatePoint[$templateName]['change_freq'];
            }

            foreach ($languages as $lang) {
                $contentUrl = $content['content_url'];
                if ($lang->initial != 1) {
                    $contentUrl = url('') . '/' . $lang->name . '/' . $content['content']['slug'];
                }
                $retval .= '<url>' .
                    '<loc><![CDATA[' . $contentUrl . ']]></loc>' .
                    '<lastmod><![CDATA[' . $content['content_lastmod'] . ' ]]></lastmod>' .
                    '<changefreq><![CDATA[' . $changeFreq . ']]></changefreq>' .
                    '<priority><![CDATA[' . $priority . ']]></priority>' .
                    '</url>';
            }

            if (sizeof($content['children']) > 0) {
                $retval .= self::renderXMLSiteMap($content['children'], $except, $templatePoint, $languages);
            }
        }

        return $retval;
    }

    // === End Sitemap helper === //
    public static function checkIntroPage()
    {
        $intro = public_path() . '/intro/' . env('INTRO', '') . '/index.html';
        if (file_exists($intro)) {
            $found = Session::has('intro');

            if (Request::path() != '/' || $found) {
                return false;
            } else if (!$found && Request::session()->token() == Request::input('intro')) {
                Session::push('intro', Request::input('intro'));

                return Redirect::to('/');
            } else {
                return str_ireplace('##TOKEN##', csrf_token(), file_get_contents($intro));
            }
        } else {
            return false;
        }
    }

    /**
     * Validate Helper
     */
    public static function validateFormat($validateType, $validateValue)
    {
        switch ($validateType) {
            case'EMAIL':
                return preg_match('/\A[^@]+@[^@]+\z/', $validateValue);
                break;
        }

        return false;
    }

    // convert Date
    public static function convertToFullDate($date){
        $dates = explode('/',$date);
        return $dates[2].' '.date('F',mktime(0, 0, 0, $dates[1], 10)).' '.$dates[0];
    }

    public static function tokenTruncate($string, $your_desired_width) {
        $parts = preg_split('/([\s\n\r]+)/u', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $your_desired_width) { break; }
        }

        return implode(array_slice($parts, 0, $last_part));
    }

    public static function checkValue($data,$field = null){
        if(isset($data[$field]) && !empty($data[$field])){
            return $data[$field];
        }else{
            return '';
        }
    }
}

?>