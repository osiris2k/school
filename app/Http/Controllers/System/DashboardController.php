<?php namespace App\Http\Controllers\System;

use App\ContentObjectType;
use App\Http\Controllers\Controller;
use App\Site;

class DashBoardController extends Controller
{
    // TODO cache position
    // TODO eager loading
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('auth.role');

        $this->data['page_herader'] = 'Dashboard';
        $this->data['tag_first_menu'] = 'dashboard';
    }

    public function getIndex()
    {
        if (session('USER_ACCESS_SITE_DATA') !== 'ALL') {
            $this->data['sites'] = Site::orderData('updated_at', 'desc')
                ->whereIn('id', session('USER_ACCESS_SITE_DATA'))
                ->limit(5)
                ->get();
        } else {
            $this->data['sites'] = Site::orderData('updated_at', 'desc')->limit(5)->get();
        }
        $this->data['content_obj_types'] = ContentObjectType::orderData('updated_at', 'desc')->get();

        return view('system.dashboard', $this->data);
    }
}