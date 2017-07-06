<?php namespace App\Helpers;

use App\Hotel;
use App\HotelValue;
use App\Libraries\GeoIpLib;
use Illuminate\Support\Facades\Facade;
use App\Redirecturl;
use App\Site;

class CmsHelper extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'cmshelper';
    }

    public static function createSlug($string)
    {
        $slug = preg_replace('#[^-ก-๙a-zA-Z0-9]#u', '-', trim($string));
        $slug = strtolower($slug);

        return $slug;
    }

    public static function createKey($string)
    {
        // $slug = self::createSlug($string);
        $slug = preg_replace('#[^-ก-๙a-zA-Z0-9]#u', '_', trim($string));
        $slug = strtolower($slug);

        return $slug;
    }

    public static function getProperty($id)
    {
        return \App\ContentProperty::findOrFail($id);
    }

    // for render system
    public static function getValueByPropertyID($object_id,
                                                $property_id, $type = false, $options = false)
    {
        $value = '';
        if ($type == 'content') {
            $obj = \App\ContentValue::where('content_id', '=', $object_id)
                ->where('content_property_id', '=', $property_id);
            if (isset($options['language_id'])) {
                $obj = $obj->where('language_id', '=', $options['language_id']);
            }
            $return = $obj->first();
            if (!$return) {
                $tmp_languages = CmsHelper::getLanguages();
                foreach ($tmp_languages as $tmp_language) {
                    $obj = \App\ContentValue::where('content_id', '=', $object_id)->where('content_property_id', '=', $property_id)->where('language_id', '=', $tmp_language->id);
                    $return = $obj->first();
                    if ($return) {
                        break;
                    }
                }
            }
        } else if ($type == 'site') {
            $return = \App\SiteValue::where('site_id', '=', $object_id)
                ->where('site_property_id', '=', $property_id)
                ->first();
        } else if ($type == 'hotel') {
            /** Get hotel value*/
            $obj = HotelValue::where('hotel_id', '=', $object_id)
                ->where('hotel_property_id', '=', $property_id);

            if (isset($options['language_id'])) {
                $obj = $obj->where('language_id', '=', $options['language_id']);
            }

            $return = $obj->first();

            if (!$return) {
                $tmp_languages = CmsHelper::getLanguages();
                foreach ($tmp_languages as $tmp_language) {
                    $obj = HotelValue::where('hotel_id', '=', $object_id)
                        ->where('hotel_id', '=', $property_id)
                        ->where('language_id', '=', $tmp_language->id);
                    $return = $obj->first();
                    if ($return) {
                        break;
                    }
                }
            }
        }
        if ($return)
            $value = $return->value;

        return $value;
    }

    public static function getDataByPropertyID($property_id, $contentId = null)
    {
        if ($contentId) {
            $return = \App\ContentValue::where('content_property_id', '=', $property_id)
                ->where('content_id', '=', $contentId)
                ->first();
        } else {
            $return = \App\ContentValue::where('content_property_id', '=', $property_id)->first();
        }

        $value = '';
        if ($return)
            $value = $return->value;

        return $value;
    }

    // public static function genFormByName($name,$language_id)
    // {
    // 	$data = array();
    // 	$form_object 			= \App\FormObject::where('name','=',$name)->first();
    // 	$data['properties']     = \App\FormProperty::with(['formPropertyLabels' => function($query) use($language_id) {
    // 		$query->where('language_id','=',$language_id);
    // 	}])->where('form_object_id','=',$form_object->id)->get();
    // 	$data['title']          = 'Title';
    // 	$data['action']         = 'form/'.$form_object->id;
    // 	$data['method']         = 'post';
    // 	return $data;
    // }


    public static function getAllPages()
    {
        $return = \App\Content::all();
        $value = '';
        if ($return)
            $value = $return;

        return $value;
    }

    public static function getPagesByContentObjectList($list)
    {
        $arry_list = [];
        foreach ($list as $key=>$item) {
            $contents = self::getPagesByContentObjectName($item);
            foreach ($contents->contents as $content) {
                $templates = $contents->name;
                $content->template = $templates;
                $arry_list[] =  $content;
            }
        }
        return $arry_list;
    }

    public static function getPagesByContentObjectName($name)
    {
        $co = \App\ContentObject::whereName($name)->first();
        if ($co == null) {
            return [];
        }
        return $co;
    }

    public static function getContentObjects()
    {
        $return = \App\ContentObject::orderBy('name')->get();

        return $return;
    }

    public static function getFormObjects()
    {
        $return = \App\FormObject::orderBy('name')->get();

        return $return;
    }

    public static function getLabelValue($language_id, $form_property_id)
    {
        $return = \App\FormPropertyLabel::where('language_id', '=', $language_id)->where('form_property_id', '=', $form_property_id)->first();

        return $return;
    }

    // === language === //
    public static function getInitLanguage($siteData = null)
    {
        if ($siteData != null) {
            //Override initial language by GEO IP data
            if (GeoIpLib::checkGeoIpSetting()) {
                $return = GeoIpLib::getProperLanguage($siteData);
            } else {
                $return = \App\Language::where('initial', '=', 1)->first();
            }
        } else {
            $return = \App\Language::where('initial', '=', 1)->first();
        }

        return $return;
    }

    public static function getLanguages()
    {
        $return = \App\Language::orderBy('initial', 'desc')->orderBy('priority', 'asc')->where('status', '=', '1')->get();

        return $return;
    }

    public static function getSiteLanguages($site_id, $excludes = [])
    {
        $langs = \App\Site::whereId($site_id)->first()->siteLanguages()->where('status', '=', '1')->orderBy('priority', 'asc');

        if (count($excludes) > 0) {
            $langs->whereNotIn('languages.id', $excludes);
        }

        $langs = $langs->get();
        if ($langs->count() == 0) {
            $langs = \App\Language::whereInitial('1')->whereStatus(1)->get();
        }
        return $langs;
    }
    // === end language === //

    //====== Translation ====== //
    public static function getAllTranslation($language_id)
    {
        $tmps = [];
        $translations = \App\Translation::with(['Languages' => function ($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }])->get();

        foreach ($translations as $translation) {
            if (sizeof($translation->Languages) > 0) {
                $tmps[$translation->key] = $translation->Languages[0]->pivot->values;
            } else {
                $tmps[$translation->key] = ViewHelper::getTranslation($translation->key, $language_id);
            }
        }

        return $tmps;
    }

    public static function getRedirectUrl($path)
    {
        $found = Redirecturl::where('source_url', '=', $path)->first();
        if ($found != null) {
            return $found;
        }

        return null;
    }

    // ====== End Translation ====== //

    public static function checkContentTemplateExist($contentObject)
    {
        return file_exists(base_path('resources/views/templates/icfp/' . $contentObject . '.blade.php')) ? true : false;
    }

}

?>