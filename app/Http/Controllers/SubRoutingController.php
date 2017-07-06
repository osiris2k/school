<?php namespace App\Http\Controllers;

use App\Content;
use App\Helpers\HotelHelper;
use App\Helpers\ViewHelper;
use App\Hotel;
use \App\Site as Site;
use \App\Language as Language;
use \App\Helpers\CmsHelper;

use \Request;
use \Redirect;

class SubRoutingController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        if ($intro = ViewHelper::checkIntroPage()) {
            return $intro;
        }

        // echo $account;
        $parent = "";
        $segments = Request::path();
        $default = $this->setSubDomain();
        $site_id = $default['site']->id;
        $page = $default['page'];
        $lang = $default['lang'];
        $template = $default['site']->template;

        $path = str_replace(Request::root(), '', Request::fullUrl());
        $found = CmsHelper::getRedirectUrl($path);
        if ($found != null && ($found->site_id == 0 || $found->site_id == $site_id)) {
            return Redirect::to($found->destination_url, $found->type);
        }

        /**
         * Hotel Property Condition
         */
        $data['hotel'] = null;
        $data['hotel_values'] = null;
        if (HotelHelper::isEnable()) {
            /**
             * Enable Hotel feature
             *
             * URI Priority
             * 1. Language
             * 2. Hotel
             * 3. Content
             */
            if ($segments != "/") {
                $uri = explode('/', $segments); // check segment url

                /** Uri is equal to 1 */
                if (count($uri) == 1) {
                    if ($this->checkLanguage(end($uri), $site_id)) {
                        /** check last segment is language */
                        $lang = end($uri);
                    } else if ($this->checkHotel(end($uri))) {
                        /** check is hotel */
                        $data['hotel'] = $this->getHotel(end($uri));
                        $hotelPage = $this->getHotelHomePage($data['hotel']);
                        $page = $hotelPage['page_slug'];
                    } else if ($this->checkPage(end($uri))['check']) {
                        /** last segment is page */
                        $page_return = $this->checkPage(end($uri));
                        $page = $page_return['page_slug'];
                        $parent = $page_return['parent'];
                    }
                } else {
                    /** Uri is more than 1 */

                    $tmp_uri = $uri;
                    $i = 0;
                    while (sizeof($tmp_uri) > 0) {
                        $part_uri = array_shift($tmp_uri);
                        if ($this->checkLanguage($part_uri, $site_id)) {
                            $lang = $part_uri;
                        } else if ($this->isLanguage($part_uri)) {
                            return \Redirect::to('/');
                        } else if ($this->checkHotel($part_uri)) {
                            /** check is hotel */
                            $data['hotel'] = $this->getHotel($part_uri);
                        }

                        $i++;
                    }

                    /** Check Hotel first because if check page first its will abort(404) */
                    if ($this->checkHotel(end($uri))) {
                        /** check last segment is hotel */
                        $data['hotel'] = $this->getHotel(end($uri));
                        $hotelPage = $this->getHotelHomePage($data['hotel']);
                        $page = $hotelPage['page_slug'];
                    } else if ($this->checkPage(end($uri))) {
                        /** check last segment is page */
                        $page_return = $this->checkPage(end($uri));
                        if ($page_return['check']) {
                            $page = $page_return['page_slug'];
                            $parent = $page_return['parent'];
                        } else {
                            $page = 'Homepage';
                        }
                    }
                }
            }
        } else {
            /** Disable hotel feature */
            if ($segments != "/") {
                $uri = explode('/', $segments); // check segment url
                if (sizeof($uri) == 1) {
                    if ($this->checkLanguage(end($uri), $site_id)) // check last segment is language
                    {
                        $lang = end($uri);
                    } else if ($this->checkPage(end($uri))['check']) { // last segment is page
                        $page_return = $this->checkPage(end($uri));
                        $page = $page_return['page_slug'];
                        $parent = $page_return['parent'];
                    }
                } else {
                    $tmp_uri = $uri;
                    $i = 0;
                    while (sizeof($tmp_uri) > 0) {
                        $part_uri = array_shift($tmp_uri);
                        if ($this->checkLanguage($part_uri, $site_id)) {
                            $lang = $part_uri;
                        } else if ($this->isLanguage($part_uri)) {
                            return \Redirect::to('/');
                        }

                        $i++;
                    }
                    if ($this->checkPage(end($uri))) // check last segment is page
                    {
                        $page_return = $this->checkPage(end($uri));
                        if ($page_return['check']) {
                            $page = $page_return['page_slug'];
                            $parent = $page_return['parent'];
                        } else {
                            $page = 'Homepage';
                        }
                    }
                }
            }
        }

        if (isset($page_return['page_slug']) && $page_return['page_slug'] == 'homepage') {
            if ($default['lang'] == $lang) {
                return \Redirect::to('/');
            }
        }

        // make global variable
        $data['language_id'] = Language::where('name', '=', $lang)->first()->id;
        $data['language_name'] = $lang;
        //$data['language_all'] = CmsHelper::getLanguages();

        \App::singleton('user_current_language', function () use ($data, $default) {
            // Override user_current_language by GEO Ip data
            $language_id = $data['language_id'];

            return $language_id;
        });

        \View::share('user_current_language', app('user_current_language'));
        // end singleton

        $data['site_id'] = $site_id;
        $data['site'] = \App\Helpers\ViewHelper::getSiteInformation($data['site_id']);

        if (HotelHelper::isEnable() && $data['hotel'] !== null) {
            /** Reformat Hotel Value */
            $data['hotel_values'] = HotelHelper::reFormatHotelValues($data['hotel'], $data['language_id']);

            /** If enable Hotel feature */
            $obj = \App\Content::where('slug', '=', $page)->whereHas('hotel', function ($query) use ($data) {
                $query->where('hotels_contents.hotel_id', '=', $data['hotel']->id);
            })
                ->whereActive(1)
                ->first();
            $content = \App\Helpers\ViewHelper::getContentBySlug($page, $data['site_id'], $data['language_id'], $data['hotel']);
        } else {
            $obj = \App\Content::where('slug', '=', $page)->whereActive(1)->first();
            $content = \App\Helpers\ViewHelper::getContentBySlug($page, $data['site_id'], $data['language_id']);
        }

        if ($obj) {
            $data['current_content_id'] = $obj->id;
            $data['translations'] = CmsHelper::getAllTranslation($data['language_id']);

            if ($content == null) {
                //echo $page.' content is not found';
            } else {
                $page = $obj->contentObject->name;
                //$data[$page] = $content;
                $data['currentPage'] = $data[$page] = $content;
                $view_path = 'templates.' . $template . '.' . $page;

                /** Override $view_path for hotel */
                if ($data['hotel'] !== null) {
                    $templatePath = explode('_', $page);
                    $view_path = 'templates.' . $templatePath[0] . '.' . $page;
                }

                return view($view_path, $data);
            }
        } else {

            /** Abort 404 not found if not match any content */
            abort(404);

            /*$view_path = $page;

            return view($view_path);*/
        }

    }

    public function countLanguage()
    {
        $obj = \App\Language::all();
        $count = $obj->count();
        if ($count > 1) {
            return true;
        }

        return false;
    }

    public function getLanguages()
    {
        $languages = \App\Language::all();
        $array = array();
        foreach ($languages as $language) {
            $array[] = $language->name;
        }

        return $array;
    }

    public function isLanguage($language)
    {
        return in_array($language, $this->getLanguages());
    }

    public function checkLanguage($language, $site_id)
    {
        //return in_array($language, $this->getLanguages());

        $found = CmsHelper::getSiteLanguages($site_id);
        $langs = $found->lists('name');

        return in_array($language, $langs);
    }

    public function checkMultiSites()
    {
        return true;
    }

    public function checkSite($site)
    {
        $obj = \App\Site::where('name', '=', $site)->get();
        $count = $obj->count();
        if ($count >= 1) {
            return true;
        }

        return false;
    }

    public function countSite()
    {
        return 1;
    }

    public function checkPage($page_slug)
    {
        $return['check'] = false;
        $col = array('slug', 'parent_content_id');
        $content = \App\Content::select($col)->where('slug', '=', $page_slug)->first();
        if ($content) {
            $return['check'] = true;
            $parent = $content->parent_content_id;
            $return['page_slug'] = $page_slug;
            $return['parent'] = $parent;
        } else {
            \Log::info('404: ' . $page_slug);
            abort(404);
        }

        return $return;
    }

    public function setSubDomain()
    {
        $route = \Route::getCurrentRoute();
        $subdomain = $route->getParameter('subdomain');
        $default['page'] = "homepage";
        $default['parent'] = "";
        $default['site'] = Site::where('name', '=', $subdomain)->first();
        // dd($default['site']);
        $language = CmsHelper::getInitLanguage($default['site']);
        $default['lang'] = $language->name;

        return $default;
    }

    public function getHotelHomePage($hotel)
    {
        $return['check'] = false;

        $content = Content::whereHas('hotel', function ($query) use ($hotel) {
            $query->where('hotels_contents.hotel_id', '=', $hotel->id)
                ->where('hotels_contents.is_homepage_content', '=', 1);
        })
            ->first();

        if ($content) {
            $return['check'] = true;
            $return['page_slug'] = $content->slug;
        } else {
            \Log::info('404: Hotel homepage not found ');
            abort(404);
        }

        return $return;
    }

    public function getHotel($hotelSlug)
    {
        return Hotel::where('slug', '=', $hotelSlug)->first();
    }

    public function checkHotel($hotel_slug)
    {
        $return['check'] = false;
        $hotel = \App\Hotel::where('slug', '=', $hotel_slug)->first();

        if ($hotel) {
            return true;
        }

        return false;
    }

}
