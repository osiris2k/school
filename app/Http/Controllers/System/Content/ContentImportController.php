<?php namespace App\Http\Controllers\System\Content;

use App\ContentValue;
use App\Http\Controllers\Controller;
use App\Language;
use ErrorException;
use Illuminate\Http\Request;
use \Excel;

class ContentImportController extends Controller
{
    const IMPORT_MENU_STATUS = 'CONTENT_IMPORT_TRANSLATION';
    const IMPORT_MAIN_TITLE = 'Import Content Translations';
    const IMPORT_FORM_TITLE = 'Content Translation Import Form';

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = self::IMPORT_MAIN_TITLE;
        $this->data['tag_first_menu'] = config('content.MAIN_MENU_STATUS');
        $this->data['tag_sub_menu'] = self::IMPORT_MENU_STATUS;
    }

    protected function __getImportFormViewData()
    {
        return [
            'pageTitle' => self::IMPORT_FORM_TITLE,
        ];
    }

    public function getImportContentFormView()
    {
        $this->data = array_merge($this->data, $this->__getImportFormViewData());

        return view('system.content.import.import_form', $this->data);
    }

    public function postImportContentFile(Request $request)
    {
        $statusMessage = 'SUCCESS';
        try {
            $uploadPath = public_path('uploads/translations');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
            }

            if (!is_writable($uploadPath)) {
                chmod($uploadPath, 0777);
            }

            $request->file('import_file')->move($uploadPath, $request->file('import_file')->getClientOriginalName());

            $importFile = $request->file('import_file')->getClientOriginalName();
            $importFileFullPath = $uploadPath . DIRECTORY_SEPARATOR . $importFile;

            Excel::load($importFileFullPath, function ($reader) {

                $reader->each(function ($sheet) {
                    $language = Language::where('languages.name', '=', $sheet->getTitle())->first();
                    $sheet->each(function ($row) use ($language, $sheet) {
                        $contentValue = ContentValue::find((int)$row->key);
                        $contentValue->value = $row->field_value;
                        $contentValue->save();
                    });
                });
            });

        } catch (ErrorException $e) {
            $statusMessage = 'FAILED';
        }

        return redirect()->route('content.system.import.get_get_import_content_form_view')
            ->with('STATUS_MESSAGE', $statusMessage);

    }
}