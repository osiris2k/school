<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Menu;
use App\MenuGroup;
use App\Content;
use App\LanguageMenu;
use App\Language;
use App\SiteLanguages;
use \Auth;
use App\AjaxResponse;
use App\Helpers\CmsHelper;

use Request;

class MenuController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Menu Management';
        $this->data['tag_first_menu'] = 'menu-objects';
    }

    public function rules($id = false)
    {
        if ($id) {
            $rule = array('menu_title' => 'required|unique:menus,id,' . $id,
                'menu_group_id' => 'required', 'content_id' => 'required');
        } else {
            $rule = array('menu_title' => 'required|unique:menus',
                'menu_group_id' => 'required', 'content_id' => 'required');
        }
        return $rule;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->data['objs'] = Menu::orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();
        return view('system.menu', $this->data);
    }

    public function getAjax()
    {
        $user = Auth::user();
        $user_site = $user->sites()->get()->lists('id');

        $col = array('id', 'menu_title');

        if ($user->role_id == 1 || $user->role_id == 4) {

            $menus = Menu::with('menuGroup')->get();

        } else {
            $menus = Menu::with(['menuGroup' => function ($query) use ($user_site) {
                $query->whereIn('site_id', $user_site);
            }])->get();
        }
        $return['data'] = [];
        foreach ($menus as $key=>$menu) {
            if ($menu->menuGroup != null) {
                if ($menu->parent_menu_id != '0') {
                    $parentMenu = Menu::find($menu->parent_menu_id);
                    $temp['parent_menu_title'] = $parentMenu !== null ? $parentMenu->menu_title : '';
                } else {
                    $temp['parent_menu_title'] = '';
                }
                $temp['id'] = $menu->id;
                $temp['menu_title'] = $menu->menu_title;
                $temp['menu_group'] = ucfirst(str_replace('_',' ',$menu->menuGroup->name));
                $temp['site'] = $menu->menuGroup->site->name;
                $temp['active'] = $menu->active;
                $return['data'][] = $temp;
            }
        }
        return $return;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $user = Auth::user();

        $siteIds = $user->sites()->get()->lists('id');

        $this->data['title'] = "Menu";
        $this->data['languages'] = Language::whereStatus(1)->orderBy('priority')->get();

        if ($user->role_id == 1 || $user->role_id == 4) {

            $this->data['groups'] = MenuGroup::all();
            $menu = Menu::all();
            $this->data['contents'] = Content::all();
            $this->data['languages'] = Language::all();

        } else {

            $this->data['groups'] = MenuGroup::whereIn('site_id', $siteIds)->get();
            $this->data['contents'] = Content::whereIn('site_id', $siteIds)->get();
            $menu = Menu::with(['menuGroup' => function ($query) use ($siteIds) {
                $query->whereIn('site_id', $siteIds);
            }])->get();
            // $site_languages = SiteLanguages::whereIn('site_id',$siteIds)->get()->lists('language_id');

            // if(count($site_id) != 0){
            // 	$this->data['languages'] = Language::whereIn('id',$site_languages)->get();
            // }else{
            // 	$this->data['languages'] = Language::whereInitial('1')->whereStatus(1)->get();
            // }


        }


        $this->data['menus'] = $menu;

        $this->data['url'] = 'system/menus';


        $this->data['method'] = 'post';
        return view('system.forms.menu', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = \Request::all();
        $validate = \Validator::make($input, $this->rules());
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $menu = new Menu();
        $menu->menu_group_id = $input['menu_group_id'];
        $menu->content_id = $input['content_id'] or 0;
        $menu->target = $input['target'];
        $menu->parent_menu_id = $input['parent_menu_id'];
        $menu->priority = 100;
        $menu->external_link = $input['external_link'];
        $menu->image_src = $input['image_src'];
        $menu->created_by = Auth::user()->id;
        $menu->menu_title = $input['menu_title'];
        $menu->class = $input['class'];
        if (array_key_exists('active', $input)) {
            $menu->active = $input['active'];
        } else {
            $menu->active = 0;
        }
        $menu->save();
        $language_menu = new LanguageMenu();
        $language_menu->label = $input['menu_language_title'];
        $language_menu->detail = $input['menu_language_detail'];
        $language_menu->language_id = $input['language_id'];
        $language_menu->menu_id = $menu->id;
        $language_menu->save();
        $message = 'Save data success.';
        \Session::flash('message', $message);

        if($input['save_type']=='exit'){
            return redirect('system/menus');
        }else {
            return redirect('system/menus/' . $menu->id . '/edit');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = Auth::user();

        $user_site = $user->sites()->get()->lists('id');

        $this->data['title'] = "Menu";

        $user = Auth::user();

        if ($user->role_id == 1 || $user->role_id == 4) {

            $this->data['groups'] = MenuGroup::all();
            $this->data['contents'] = Content::all();
            $this->data['menus'] = Menu::where('id', '<>', $id)->get();

        } else {
            $this->data['groups'] = MenuGroup::whereIn('site_id', $user_site)->get();
            $this->data['contents'] = Content::whereIn('site_id', $user_site)->get();
            $this->data['menus'] = Menu::with(['menuGroup' => function ($query) use ($user_site) {
                $query->whereIn('site_id', $user_site);
            }])->get();
        }

        $this->data['url'] = 'system/menus/' . $id;
        $this->data['method'] = 'put';
        $this->data['obj'] = Menu::with('languages')->where('id', '=', $id)->first();
        $tmps = $this->data['obj']->languages;
        $arry_tmp = array();
        foreach ($tmps as $tmp) {
            $arry_tmp[$tmp->pivot->language_id] = new \stdClass();
            $arry_tmp[$tmp->pivot->language_id]->label = $tmp->pivot->label;
            $arry_tmp[$tmp->pivot->language_id]->detail = $tmp->pivot->detail;
            $arry_tmp[$tmp->pivot->language_id]->language_id = $tmp->pivot->language_id;
        }
        $menu = Menu::find($id);
        $menu_group = MenuGroup::find($menu->menu_group_id);

        $this->data['language_menu'] = $arry_tmp;
        $this->data['languages'] = Language::whereStatus(1)->orderBy('priority')->get();
        //$this->data['languages']      = CmsHelper::getSiteLanguages($menu_group->site_id);

        return view('system.forms.menu', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        // echo $id;
        $input = \Request::all();
        $validate = \Validator::make($input, $this->rules($id));
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $menu = Menu::find($id);
        $menu->menu_group_id = $input['menu_group_id'];
        $menu->content_id = $input['content_id'];
        $menu->target = $input['target'];
        $menu->parent_menu_id = $input['parent_menu_id'];
        $menu->external_link = $input['external_link'];
        $menu->image_src = $input['image_src'];
        $menu->menu_title = $input['menu_title'];
        $menu->class = $input['class'];
        if (array_key_exists('active', $input)) {
            $menu->active = $input['active'];
        } else {
            $menu->active = 0;
        }
        $menu->updated_by = Auth::user()->id;
        $menu->save();
        $language_menu = LanguageMenu::where('menu_id', '=', $menu->id)->where('language_id', '=', $input['language_id'])->first();

        if ($language_menu) {
            $language_menu->label = $input['menu_language_title'];
            $language_menu->detail = $input['menu_language_detail'];
            $language_menu->language_id = $input['language_id'];
            $language_menu->save();
        } else {
            $language_menu = new LanguageMenu();
            $language_menu->label = $input['menu_language_title'];
            $language_menu->detail = $input['menu_language_detail'];
            $language_menu->language_id = $input['language_id'];
            $language_menu->menu_id = $menu->id;
            $language_menu->save();
        }
        $message = 'Save data success.';
        \Session::flash('message', $message);
        if($input['save_type']=='exit'){
            return redirect('system/menus/');
        }else {
            return redirect('system/menus/' . $menu->id . '/edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $obj = Menu::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            if ($obj->delete()) {
                $return = new AjaxResponse(200, 'Delete Success');
                $return->setData(array('delete' => 'tr'));
            } else {
                $return = new AjaxResponse(503, 'Unable to delete');
            }
        } catch (ModelNotFoundException $e) {
            $return = new AjaxResponse(404, 'Object is not found', $e->getMessage());
        }
        return $return->getJson();
    }


}
