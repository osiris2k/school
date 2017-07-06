<?php namespace App\Http\Controllers\System\Content;

use App\Content;
use App\ContentObject;
use App\ContentProperty;
use App\ContentValue;
use App\Http\Controllers\Controller;
use App\Language;
use App\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Excel;

class ContentExportController extends Controller
{
    // TODO Clean code
    const EXPORT_MENU_STATUS = 'CONTENT_EXPORT_TRANSLATION';
    const EXPORT_MAIN_TITLE = 'Export Content Translations';
    const EXPORT_LIST_TITLE = 'Content Lists';

    private $data = [];

    public function __construct($configs = [])
    {
        $this->middleware('auth');
        $this->data['page_herader'] = self::EXPORT_MAIN_TITLE;
        $this->data['tag_first_menu'] = config('content.MAIN_MENU_STATUS');
        $this->data['tag_sub_menu'] = self::EXPORT_MENU_STATUS;
    }

    protected function __getListViewData()
    {
        $user = Auth::user();
        $userSite = $user->sites()->get()->lists('id');
        $siteLists = new Site();

        if ($user->role_id != 1 && $user->role_id != 4) {
            $siteLists = $siteLists->whereIn('id', $userSite);
        }

        return [
            'pageTitle'       => self::EXPORT_LIST_TITLE,
            'siteLists'       => $siteLists->get(),
            'contentObjLists' => ContentObject::filterByContentObjectType([config('content.CONTENT_TYPE_ID')])->get()
        ];
    }

    public function getExportContentListView()
    {
        $this->data = array_merge($this->data, $this->__getListViewData());

        return view('system.content.export.export_list', $this->data);
    }

    public function postExportContentListData(Request $request)
    {
        $user = Auth::user();
        $userSite = $user->sites()->get()->lists('id');
        $contentDataArray = Content::getContentWithParent()
            ->filterByContentObjectTypeId([config('content.CONTENT_TYPE_ID')]);

        if ($user->role_id != 1 && !$user->role_id != 4) {
            $contentDataArray = $contentDataArray->filterBySiteId($userSite);
        }
        if ($request->input('site_filter') != '') {
            $contentDataArray = $contentDataArray->filterBySiteId([$request->input('site_filter')]);
        }
        if ($request->has('template_filter') &&
            count($request->input('template_filter')) > 0 &&
            !in_array('ALL', $request->input('template_filter'))
        ) {
            $contentDataArray = $contentDataArray->filterByContentObjectId($request->input('template_filter'));
        }

        return response()->json(['data' => $contentDataArray->get()]);
    }


    // TODO Create view for export template (Later)
    public function postExportContentTranslationsFile(Request $request)
    {
        $contentPropertiesInitial = $this->getInitialContentProperty($request)->get();

        $exportArray = [];
        if (count($contentPropertiesInitial) == 0) {
            $exportArray['No Translations'][] = ['No Translation Available'];
        } else {
            foreach (Language::orderBy('priority', 'ASC')->get() as $lang) {
                foreach ($contentPropertiesInitial as $contentPropertyInitItem) {

                    $contentTranslation = $this->getContentTranslation($request, $lang, $contentPropertyInitItem)->first();

                    $exportArray = $this->reFormatExportTranslationData($contentTranslation, $contentPropertyInitItem, $lang, $exportArray);
                }
            }
        }

        //TODO Load view (later)
        Excel::create(config('content.EXPORT_FILE_NAME_FORMAT'), function ($excel) use ($exportArray) {
            foreach ($exportArray as $langIndex => $langItem) {
                $excel->sheet($langIndex, function ($sheet) use ($langIndex, $langItem) {
                    $sheet->fromArray($langItem)
                        ->freezeFirstRow();
                });
            }

        })
            ->download('xlsx');
    }

    /**
     * Filter by User Site
     *
     * @param $user
     * @param $filterData
     * @param $userSite
     * @return mixed
     * @internal param $filterData
     */
    protected function filterByUserSite($user, $filterData, $userSite)
    {
        if ($user->role_id != 1 && !$user->role_id != 4) {
            return $filterData = $filterData->filterBySiteId($userSite);
        }

        return $filterData;
    }

    /**
     * Filter by Site Id
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $filterData
     * @return mixed
     */
    protected function filterByRequestSiteId(Request $request, $filterData)
    {
        if ($request->input('site_filter') != '') {
            return $filterData->filterBySiteId([$request->input('site_filter')]);
        }

        return $filterData;
    }

    /**
     * Filter by Content Object Id
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $filterData
     * @return mixed
     * @internal param $filterData
     */
    protected function filterByRequestContentObjectId(Request $request, $filterData)
    {
        if ($request->has('template_filter') &&
            count($request->input('template_filter')) > 0 &&
            !in_array('ALL', $request->input('template_filter'))
        ) {
            return $filterData->whereIn('content_values.content_object_id', $request->input('template_filter'));
        }

        return $filterData;
    }

    /**
     * Get initial Content Property and Value.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function getInitialContentProperty(Request $request)
    {
        $user = Auth::user();
        $userSite = $user->sites()->get()->lists('id');

        $contentPropertiesInitial = ContentProperty::getContentPropForReportFile()
            ->filterByContentObjectTypesId([config('content.CONTENT_TYPE_ID')])
            ->filterByDataTypeId(config('content.EXPORT_DATA_TYPE_ID'))
            ->filterByLanguageInitial();

        $contentPropertiesInitial = $this->filterByUserSite($user, $contentPropertiesInitial, $userSite);
        $contentPropertiesInitial = $this->filterByRequestSiteId($request, $contentPropertiesInitial);
        $contentPropertiesInitial = $this->filterByRequestContentObjectId($request, $contentPropertiesInitial);

        return $contentPropertiesInitial;
    }

    /**
     * Get ContentValue for translation.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $lang
     * @param                          $contentPropertyInitItem
     * @return mixed
     */
    protected function getContentTranslation(Request $request, $lang, $contentPropertyInitItem)
    {
        $user = Auth::user();
        $userSite = $user->sites()->get()->lists('id');

        $contentTranslation = ContentValue::getContentValueForExportFile()
            ->filterByContentObjectTypeId([config('content.CONTENT_TYPE_ID')])
            ->filterByDataTypeId(config('content.EXPORT_DATA_TYPE_ID'))
            ->filterByLangId([$lang->id])
            ->filterByContentPropertyId([$contentPropertyInitItem->content_property_id])
            ->filterByContentObjectId([$contentPropertyInitItem->content_object_id])
            ->filterByContentId([$contentPropertyInitItem->content_id]);

        $contentTranslation = $this->filterByUserSite($user, $contentTranslation, $userSite);
        $contentTranslation = $this->filterByRequestSiteId($request, $contentTranslation);
        $contentTranslation = $this->filterByRequestContentObjectId($request, $contentTranslation);

        return $contentTranslation;
    }

    /**
     * Re-format ExportData into Export File Format.
     *
     * @param $contentTranslation
     * @param $contentPropertyInitItem
     * @param $lang
     * @param $exportArray
     * @return mixed
     */
    protected function reFormatExportTranslationData($contentTranslation, $contentPropertyInitItem, $lang, $exportArray)
    {
        if (!empty($contentTranslation)) {
            $exportArray[$lang->name][] = [
                'Key'         => $contentTranslation->id,
                'Content'     => $contentPropertyInitItem->content_name,
                'Language'    => $lang->name,
                'Field Name'  => $contentPropertyInitItem->content_property_name,
                'Field Value' => $contentTranslation->content_value
            ];

            return $exportArray;
        } else {
            $newContentValueByLang = new ContentValue;
            $newContentValueByLang->content_id = $contentPropertyInitItem->content_id;
            $newContentValueByLang->content_object_id = $contentPropertyInitItem->content_object_id;
            $newContentValueByLang->content_property_id = $contentPropertyInitItem->content_property_id;
            $newContentValueByLang->language_id = $lang->id;
            $newContentValueByLang->value = $contentPropertyInitItem->content_value;
            $newContentValueByLang->save();

            $exportArray[$lang->name][] = [
                'Key'         => $newContentValueByLang->id,
                'Content'     => $contentPropertyInitItem->content_name,
                'Language'    => $lang->name,
                'Field Name'  => $contentPropertyInitItem->content_property_name,
                'Field Value' => $newContentValueByLang->value
            ];

            return $exportArray;
        }
    }

}