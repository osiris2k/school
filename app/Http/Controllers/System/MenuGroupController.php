<?php namespace App\Http\Controllers\System;

use App\Hotel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\MenuGroup;
use App\Menu;
use \Auth;
use App\AjaxResponse;

use Illuminate\Http\Request;

class MenuGroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Menu Group Management';
        $this->data['tag_first_menu'] = 'menu-objects';
    }

    public function rules($id = false, $site_id = false)
    {
        if ($id) {
            return [
                'name' => 'required|unique:menu_groups,name,' . $id . ',id,site_id,' . $site_id
            ];

        } else {
            return [
                'name' => 'required|unique:menu_groups,name,0,id,site_id,' . $site_id
            ];

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $this->data['objs'] = MenuGroup::all();

        return view('system.menu-group', $this->data);
    }

    public function getAjax()
    {
        $user = Auth::user();
        $col = array('id', 'name');

        if ($user->role_id == 1 || $user->role_id == 4) {

            $menu_groups = MenuGroup::with('site')->get();

        } else {

            $siteIds = $user->sites()->get()->lists('id');

            $menu_groups = MenuGroup::whereIn('site_id', $siteIds)->get();
        }

        $return['data'] = array();
        foreach ($menu_groups as $menu_group) {
            $tmp = array();
            $tmp['id'] = $menu_group->id;
            $tmp['name'] = $menu_group->name;
            $tmp['site_name'] = $menu_group->site->name;
            /** Now use 1-1 */
            $tmp['hotel_name'] = ($menu_group->hotel->first()) ? $menu_group->hotel->first()->name : '-';

            $return['data'][] = $tmp;
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

        $this->data['title'] = "Menu Group";

        if ($user->role_id == 1 || $user->role_id == 4) {

            $this->data['sites'] = \App\Site::whereActive(1)->get();

        } else {

            $sites = $user->sites()->get();

            $this->data['sites'] = $sites;
        }

        $this->data['hotels'] = Hotel::all();

        $this->data['url'] = 'system/menu-groups';
        $this->data['method'] = 'post';

        return view('system.forms.menu-group', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = \Request::all();
        $validate = \Validator::make($input, $this->rules(false, $input['site_id']));
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $menu_group = new MenuGroup();
        $menu_group->name = $input['name'];
        $menu_group->site_id = $input['site_id'];
        $menu_group->created_by = Auth::user()->id;
        $menu_group->save();

        if (isset($input['hotel_id']) && !empty($input['hotel_id'])) {
            $menu_group->hotel()->sync([$input['hotel_id']]);
        }

        $message = '';
        \Session::flash('response', $message);

        return redirect('system/menu-groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // echo 'test';
        $this->data['menu_group'] = MenuGroup::find($id);
        $this->data['menus'] = Menu::where('menu_group_id', '=', $id)->orderBy('priority', 'ASC')->orderBy('updated_at', 'Desc')->get();

        return view('system.lists.menu', $this->data);
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
        $this->data['obj'] = MenuGroup::find($id);

        if ($user->role_id == 1) {
            $this->data['sites'] = \App\Site::whereActive(1)->get();
        } else {
            $sites = $user->sites()->get();
            $this->data['sites'] = $sites;
        }

        $this->data['hotels'] = Hotel::all();

        $this->data['url'] = 'system/menu-groups/' . $id;
        $this->data['method'] = 'put';

        return view('system.forms.menu-group', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $input = \Request::all();
        $validate = \Validator::make($input, $this->rules($id, $input['site_id']));
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $menu_group = MenuGroup::find($id);
        $menu_group->name = $input['name'];
        $menu_group->site_id = $input['site_id'];
        $menu_group->updated_by = Auth::user()->id;
        $menu_group->save();

        if (isset($input['hotel_id']) && !empty($input['hotel_id'])) {
            $menu_group->hotel()->sync([$input['hotel_id']]);
        }

        $message = '';
        \Session::flash('response', $message);

        return redirect('system/menu-groups');
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
            $obj = MenuGroup::findOrFail($id);
            $obj->updated_by = Auth::user()->id;
            $obj->save();
            if ($obj->delete()) {
                /** Delete relation */
                $obj->hotel()->detach();

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