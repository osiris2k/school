<?php namespace App\Libraries;

use App\Country;
use App\Language;

class GeoIpLib
{
    /**
     * Check GEO IP setting is existed
     *
     * @return bool
     */
    public static function checkGeoIpSetting()
    {
        if (env('GEOIP_URL') && env('GEOIP_APP_ID') && env('GEOIP_APP_TOKEN')) {
            return true;
        }

        self::forgetGeoIpSession();

        return false;
    }

    /**
     * Get GEO Ip data from QUO API
     *
     * @param $url
     * @param $appid
     * @param $apptoken
     *
     * @return JSON
     */
    public static function getGeoipInfo($url, $appid, $apptoken)
    {

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !is_null($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddr = array_values(array_filter(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
            $ipaddr = end($ipaddr);

        } else if (isset($_SERVER['REMOTE_ADDR']) && !is_null($_SERVER['REMOTE_ADDR'])) {
            $ipaddr = $_SERVER['REMOTE_ADDR'];

        } else if (isset($_SERVER['HTTP_CLIENT_IP']) && !is_null($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
        }

        $QUOAPIHeader = array(
            'Quoapi-Appid: ' . $appid,
            'Quoapi-Apptoken: ' . $apptoken,
        );

        $ch = curl_init();
//        $ipaddr = '27.55.16.0';//Thailand
        //$ipaddr = '46.36.198.121';//United Kingdom
//	    $ipaddr = '2.160.0.0';//Germany
//        $ipaddr = '62.4.128.0';//Belgium
//        $ipaddr = '1.21.0.0';//Japan
//        $ipaddr = '3.0.0.0';//United State

        curl_setopt($ch, CURLOPT_URL, $url . '?ipaddr=' . $ipaddr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $QUOAPIHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        //curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Set site language data
     *
     * @param $siteData
     * @return \App\Language eloquent object
     */
    public static function getProperLanguage($siteData)
    {
        $result = self::getGeoipInfo(env('GEOIP_URL'), env('GEOIP_APP_ID'), env('GEOIP_APP_TOKEN'));
        $data = json_decode($result, true);

        if ($data['status'] == 'success') {
            $country = $data["data"]["name"];
            $countryCode = $data["data"]["code"];
            $continent = $data["data"]["continent_name"];
            $continentCode = $data["data"]["continent_code"];
        } else {
            $country = '';
            $countryCode = '';
            $continent = '';
            $continentCode = '';
        }

        $languageCode = [
            'continent_code' => $continentCode,
            'continent'      => $continent,
            'country_code'   => $countryCode,
            'country'        => $country
        ];

        return self::isValidLanguage($languageCode, $siteData);
    }


    /**
     * Validate GEO Ip Country and Language with languages data in database.
     * If GEO Ip lang exist return GEO Ip lang data
     * Else return initial language
     *
     * @param $languageData
     * @param $siteData
     * @return \App\Language eloquent object
     */
    public static function isValidLanguage($languageData, $siteData)
    {
        $lang = Language::whereHas('countries', function ($query) use ($languageData) {
            $query->where('country_code', '=', $languageData['country_code']);
        })
            ->join('site_languages', function ($join) use ($siteData) {
                $join->on('site_languages.language_id', '=', 'languages.id')
                    ->where('site_languages.site_id', '=', $siteData->id);
            })
            ->orderBy('priority', 'desc')
            ->select('languages.*')
            ->first();
        if (!$lang) {
            self::forgetGeoIpSession();
            $lang = Language::where('initial', '=', 1)->first();
        }else{
            self::initGeoIpSession($lang);
        }

        return $lang;
    }

    /**
     * Forget GEO IP session
     */
    public static function forgetGeoIpSession()
    {
        session()->forget(['GEOIP_LANG_ID', 'GEOIP_NAME', 'GEOIP_INITIAL']);
    }

    /**
     * Init GEO IP session
     *
     * @param $lang
     */
    public static function initGeoIpSession($lang)
    {
        session()->put([
            'GEOIP_LANG_ID' => $lang->id,
            'GEOIP_NAME'    => $lang->name,
            'GEOIP_INITIAL' => $lang->initial
        ]);
    }

}