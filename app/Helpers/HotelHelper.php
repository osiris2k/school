<?php namespace App\Helpers;

use App\HotelContent;
use App\HotelValue;
use Auth;
use App\Content;
use App\Hotel;
use Illuminate\Support\Facades\Facade;

/**
 * Class HotelHelper
 *
 * @package App\Helpers
 */
class HotelHelper extends Facade
{

    /**
     * Check Hotel feature is enable or not
     *
     * @return bool
     */
    public static function isEnable()
    {
        return (config('hotel.ENABLE') == 'TRUE') ? true : false;
    }

    /**
     * List Hotel data
     *
     * @return mixed
     */
    public static function getHotelLists()
    {
        return (self::isEnable()) ? Hotel::customWhere('active', '=', 1)->get() : collect([]);
    }

    public static function reFormatHotelValues($hotel = null, $languageId = 0)
    {
        if ($languageId === 0) {
            $languageId = app('user_current_language');
        }

        $returnArray = [];

        if ($hotel === null && count($hotel->hotelVales) == 0) {
            return [];
        }

        foreach ($hotel->hotelValues as $valueItem) {
            if ($valueItem->hotelProperty->data_type_id == 10 || $valueItem->hotelProperty->data_type_id == 11) {
                $init_value = HotelValue::where('hotel_id', '=', $hotel->id)
                    ->where('hotel_property_id', '=', $valueItem->hotelProperty->id)
                    ->where('language_id', '=', $languageId)
                    ->first();


                if (count(json_decode($init_value->value)) === 0) {
                    $init_value = HotelValue::where('hotel_id', '=', $hotel->id)
                        ->where('hotel_property_id', '=', $valueItem->hotelProperty->id)
                        ->where('language_id', '=', ViewHelper::getInitialLanguage()->id)
                        ->first();
                }

                $returnArray[$valueItem->hotelProperty->variable_name] = json_decode($init_value->value);
            } else {
                $init_value = HotelValue::where('hotel_id', '=', $hotel->id)
                    ->where('hotel_property_id', '=', $valueItem->hotelProperty->id)
                    ->where('language_id', '=', $languageId)
                    ->first();

                $returnArray[$valueItem->hotelProperty->variable_name] = $init_value->value;
            }

            $returnArray[$valueItem->hotelProperty->variable_name . '_option'] = $valueItem->hotelProperty->options;
        }

        return $returnArray;
    }

    /**
     * List Hotel data for hotel homepage content
     */
    public static function getHomepageContent($exceptId = 0)
    {
        $user = Auth::user();
        $userSite = $user->sites()->get()->lists('id');

        $excludeIds = HotelContent::all()->lists('content_id');
        if ($exceptId != 0) {
            $unsetIndex = array_search($exceptId, $excludeIds);
            unset($excludeIds[$unsetIndex]);
        }

        $data = Content::getContentWithParent()
            ->filterByContentObjectTypeId([config('content.CONTENT_TYPE_ID')]);

        if ($user->role_id != 1 && $user->role_id != 4) {
            $data = $data->filterBySiteId($userSite);
        }

        return $data->whereNotIn('contents.id', $excludeIds)->get();
    }
}