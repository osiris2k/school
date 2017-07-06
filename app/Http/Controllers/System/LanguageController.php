<?php namespace App\Http\Controllers\System;

use App\Country;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Language;
use App\Libraries\CoutriesLib;
use App\Libraries\GeoIpLib;
use \Auth;
use App\AjaxResponse;

use Request;

class LanguageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Language Management';
        $this->data['tag_first_menu'] = 'language';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function rules()
    {
        return ['name' => 'required', 'label' => 'required','locale' => 'required'];
    }

    public function index()
    {
        $this->data['objs'] = Language::orderBy('priority', 'ASC')->get();

        return view('system.language', $this->data);
    }

    public function reorder()
    {
        $this->data['page_header'] = 'Re-order Languages';
        $this->data['language_items'] = Language::orderBy('priority', 'ASC')->get();
        //dd($this->data);
        return view('system.reorder_language', $this->data);
    }


    public function getAjax()
    {
        $col = array('id', 'name','label','priority');
        $return['data'] = Language::select($col)->orderBy('priority')->get()->toArray();

        return $return;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->data['action'] = 'system/languages';
        $this->data['method'] = 'post';

        // Check GEOIP config before retrieve country data
        if (GeoIpLib::checkGeoIpSetting()) {
            $this->data['continents'] = Country::getCountryByContinentKey();
        }

        return view('system.forms.language', $this->data);
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
        $language = new \App\Language();
        $language->name = $input['name'];
        $language->label = $input['label'];
        $language->locale = $input['locale'];
        $language->region_code = $input['region_code'];
        $language->initial = $input['initial'];
        $language->priority = $input['priority'];
        $language->created_by = Auth::user()->id;
        $language->save();

        // Add language and country to languages_countries table
        if (isset($input['countries'])) {
            $language->countries()->sync($input['countries'], ['language_id' => $language->id]);
        }

        $message = '';
        \Session::flash('response', $message);

        return redirect('system/languages');
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
        $this->data['obj'] = Language::find($id);
        $this->data['action'] = 'system/languages/' . $id;
        $this->data['method'] = 'put';

        // Check GEOIP config before retrieve country data
        if (GeoIpLib::checkGeoIpSetting()) {
            $this->data['continents'] = Country::getCountryByContinentKey();
            $this->data['selected_country'] = ($this->data['obj']->countries) ?
                $this->data['obj']->countries->lists('id') : null;
        }

        return view('system.forms.language', $this->data);
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

        $validate = \Validator::make($input, $this->rules());
        if ($validate->fails()) {
            return redirect()->back()->withInput()->withErrors($validate->errors());
        }
        $language = Language::find($id);
        $language->name = $input['name'];
        $language->initial = $input['initial'];
        $language->status = $input['status'];
        $language->label = $input['label'];
        $language->locale = $input['locale'];
        $language->region_code = $input['region_code'];
        if ($input['initial'] != 1) {
            /* check is current one is initial language
                if yes, make a secondary language (order by priority) as a initial instead
            */
            $initial_lang = Language::whereInitial(1)->first();
            if ($initial_lang->id == $id) {
                $secondary_lang = Language::whereInitial(0)->orderBy('priority', 'desc')->first();
                $secondary_lang->initial = 1;
                $secondary_lang->save();
            }
        } else { //if this was set as initial language make the rest as not initial
            Language::where('initial', '=', 1)
                ->where('id', '!=', $language->id)->update(array('initial' => 0));
        }
        $language->priority = $input['priority'];
        $language->updated_by = Auth::user()->id;
        $language->save();

        // Add language and country to languages_countries table
        $language->countries()->sync((isset($input['countries'])) ? $input['countries'] : [], ['language_id' => $language->id]);

        $message = '';
        \Session::flash('response', $message);

        return redirect('system/languages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $count = Language::count();
        if ($count > 1) {
            try {
                $obj = Language::findOrFail($id);
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
        } else {
            $return = new AjaxResponse(503, 'Unable to delete');
        }

        return $return->getJson();
    }
}
