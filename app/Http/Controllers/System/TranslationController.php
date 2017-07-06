<?php namespace App\Http\Controllers\System;

use App\Helpers\CmsHelper;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Translation;
use App\Language;
use App\LanguageTranslation;

use App\AjaxResponse;

use App\Upload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class TranslationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Translation Management';
        $this->data['tag_first_menu'] = 'translation';
        $this->data['title'] = 'translation';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function rules($id = false)
    {

        $rule = array('key' => 'required|unique:translations,key,' . $id,
            'type' => 'required');

        return $rule;
    }

    public function index()
    {
        $this->data['languages'] = Language::all();
        $this->data['objs'] = Translation::all();
        return view('system.translation', $this->data);
    }

    public function getAjax()
    {
        $col = array('id', 'key', 'type');
        $return['data'] = Translation::select($col)->get()->toArray();
        return $return;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->data['action'] = 'system/translations';
        $this->data['method'] = 'post';
        $this->data['languages'] = Language::where('initial', '=', 1)->get();
        return view('system.forms.translation', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = \Request::all();
        $validate = Validator::make($input, $this->rules());
        $input['key'] = CmsHelper::createKey($input['key']);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $translation = new Translation();
        $translation->key = $input['key'];
        $translation->type = $input['type'];
        $translation->created_by = Auth::user()->id;
        $translation->save();

        $language = $input['language_id'];
        $translation->Languages()->sync([$language => ['values' => $input['value']]]);
        $message = '';
        \Session::flash('response', $message);
        return redirect('system/translations');
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
        $tmps = Translation::find($id)->languages;
        $value = array();
        foreach ($tmps as $tmp) {
            $value[$tmp->name] = $tmp->pivot->values;
        }
        $this->data['obj'] = Translation::find($id);
        $this->data['action'] = 'system/translations/' . $id;
        $this->data['method'] = 'put';
        $this->data['languages'] = Language::all();
        $this->data['value'] = $value;
        return view('system.forms.translation', $this->data);
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
        $input['key'] = \App\Helpers\CmsHelper::createKey($input['key']);
        $translation = Translation::find($id);
        $language = $input['language_id'];
        $tmp = LanguageTranslation::where('translation_id', '=', $id)->where('language_id', '=', $language)->first();
        if ($tmp) {
            // $translation->Languages()->sync([$language => ['values' => $input['value']]]);
            $translation->key = $input['key'];
            $translation->type = $input['type'];
            $translation->updated_by = Auth::user()->id;
            $translation->save();
            $tmp->language_id = $language;
            $tmp->translation_id = $id;
            $tmp->values = $input['value'];
            $tmp->save();

        } else {
            $language_translation = new LanguageTranslation();
            $language_translation->language_id = $language;
            $language_translation->translation_id = $id;
            $language_translation->values = $input['value'];
            $language_translation->save();
        }

        $message = '';
        \Session::flash('response', $message);
        return redirect('system/translations');
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
            $obj = Translation::findOrFail($id);
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

    public function getExport()
    {

        $this->data['title'] = 'Export translations';
        $this->data['languages'] = Language::whereStatus(1)->get();
        $this->data['action'] = route('post_export_translation');
        $this->data['method'] = 'post';

        return view('system.export.export_translations', $this->data);
    }

    private function getTranslationsValue($key,$language_id)
    {
        return LanguageTranslation::getTranslationsValuesForExportFile()
            ->filterByKey($key)
            ->filterByLanguageId($language_id)
            ->first();
    }

    private function reformatExportTranslationData($translation,$key,$language,$exportArray){
        if(!empty($translation)){
            $exportArray[$language->name][] = [
                'key' => $translation->key,
                'values' => $translation->values
            ];
        }else{
            $exportArray[$language->name][] = [
                'key' => $key,
                'values' => ''
            ];
        }
        return $exportArray;
    }

    public function postExport()
    {
        $file_name = 'translation' . "-" . date('Y_m_d_h_i_s');
        $file_name = Str::slug($file_name);
        $languages = Input::get('language');

        $exportArray = [];

        $translations_items = Translation::all(['id', 'key']);

        foreach ($languages as $lang) {
            foreach ($translations_items as $item) {

                $translation_value = $this->getTranslationsValue($item->key, $lang);

                $language = Language::find($lang);

                $exportArray = $this->reformatExportTranslationData($translation_value, $item->key, $language, $exportArray);
            }
        }

        Excel::create($file_name, function ($excel) use ($exportArray) {
            foreach ($exportArray as $langIndex => $langItem) {
                $excel->sheet($langIndex, function ($sheet) use ($langIndex, $langItem) {
                    $sheet->fromArray($langItem)
                        ->freezeFirstRow();
                });
            }

        })->download('xlsx');
    }

    public static function backupTranslations()
    {
        $time = Carbon::now();
        $file_name = 'BACKUP_TRANSLATION' . "-" . $time->toDateTimeString();
        $file_name = Str::slug($file_name);
        $languages = Language::whereStatus(1)->get()->lists('id');

        $cols = [
            'translation_id',
            'key',
            'language_id',
            'values'
        ];
        if (!empty($languages)) {

            Excel::create($file_name, function ($excel) use ($languages, $cols) {

                foreach ($languages as $language) {
                    $translations = Translation::join('language_translation', 'translations.id', '=', 'language_translation.translation_id')->select($cols)->where('language_translation.language_id', $language)->get()->all();
                    $language = Language::find($language);

                    $excel->sheet($language->name, function ($sheet) use ($translations, $language) {
                        $data = [];
                        foreach ($translations as $key => $translation) {
                            $temp = array(

                                "key" => $translation->key,
                                "values" => $translation->values
                            );
                            array_push($data, $temp);
                        }
                        $sheet->setTitle($language->name);
                        $sheet->fromArray($data);
                    });

                }

            })->store('xlsx');
        }
    }

    public function getImport()
    {
        $this->data['title'] = 'Import translations';
        $this->data['languages'] = Language::whereStatus(1)->get();
        $this->data['action'] = route('post_import_translation');
        $this->data['method'] = 'post';

        return view('system.export.import_translations', $this->data);
    }

    public function postImport()
    {

        $validator = Validator::make(Request::all(), ['file' => 'required|mimes:xlsx,xls']);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator->errors());

        } else {
            try {
                self::backupTranslations();

                $file = Request::file('file');
                $upload = new Upload();
                $file->move($upload->getUploadPath(), $file->getClientOriginalName());

                $reader = Excel::load($upload->getUploadPath() . $file->getClientOriginalName(), 'UTF-8')->all();
                DB::statement("SET foreign_key_checks=0");
                DB::table('translations')->truncate();
                DB::table('language_translation')->truncate();
                DB::statement("SET foreign_key_checks=1");

                $reader->each(function ($sheet) {
                    $languagesheet = $sheet->getTitle();
                    //Get language by sheet name
                    $language = Language::whereName($languagesheet)->first();
                    if (!empty($language)) {
                        // Loop through all rows
                        $sheet->each(function ($row) use ($language) {

                            $translation = Translation::whereKey($row->key)->first();
                            if (empty($translation)) {
                                $translation = new Translation();
                                $translation->key = $row->key;
                                /*$translation->type = 'text';*/
                                $translation->save();
                            }
                            $language_translation = new LanguageTranslation();
                            $language_translation->translation_id = intval($translation->id);
                            $language_translation->language_id = intval($language->id);
                            $language_translation->values = $row->values;
                            $language_translation->save();
                        });
                    }
                });
            } catch (ErrorException $e) {

            }
            return redirect('system/translations');
        }
    }
}
