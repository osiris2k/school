<?php

use App\Site;
use App\Helpers\ViewHelper;

Event::listen('illuminate.query', function ($sql) {
    // var_dump($sql);l
    // echo '<br>';
});

Route::get('getform', function () {
    $data['language_id'] = \App\Language::where('name', '=', 'en')->first()->id;
    \App::singleton('user_current_language', function () use ($data) {
        $language_id = $data['language_id'];

        return $language_id;
    });
    \View::share('user_current_language', app('user_current_language'));

    dd(ViewHelper::genFormByName('contact'));
});

Route::get('getmenu', function () {
    $data = App\Helpers\ViewHelper::getSubContent(3, 1);
    dd($data);
});

/**
 * Robots.txt
 */
Route::get('robots.txt', function ()
{
    $content ='User-agent: *
Disallow:';
    $response = Response::make($content, 200);
    $response->header('Content-Type', 'text/plain');
    return $response;
});

/**
 * Handle sitemap route
 */
Route::get('sitemap.xml', function () {

    // Set default language by initial language
    \App::singleton('user_current_language', function () {
        return \App\Language::where('initial', 1)->first()->id;
    });

    $filename = 'sitemap.xml';

    if (file_exists(public_path() . $filename)) {
        $filepath = public_path() . '/' . $filename;
    } else {
        $host = $_SERVER['HTTP_HOST'];
        $subdomain = explode('.', $host)[0];
        $filepath = public_path() . '/sitemaps/' . $subdomain . '/' . $filename;
    }

    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
    } else {
        $site = ViewHelper::getMainSite();
//        dd($site);
//        if (env('ISSINGLESITE')) {
//            // One-site
//            $site = ViewHelper::getMainSite();
//        } else {
//            // Multi-site
//            $site = Site::where('name', '=', explode('.', $host)[0])->first();
//            dd($host);
//        }

        $data = ViewHelper::genSiteMap($site->id);
        $except = [
            'sitemap',
            'hotel_booking_url',
            'logo',
            'footer_logo_section'
        ];
        $content = ViewHelper::renderXMLSiteMap($data, $except,
            [
                'homepage' => ['change_freq' => 'daily', 'priority' => '1'],
                'common' => ['change_freq' => 'daily', 'priority' => '0.8'],
            ],
            $site->siteLanguages);

        $content = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
            $content .
            '</urlset>';
    }

    $response = Response::make($content, 200);
    $response->header('Content-Type', 'text/xml');

    return $response;
});

Route::pattern('id', '[0-9]+');
Route::pattern('slug', '[a-z0-9-]+');
Route::pattern('other_links', '(.*)');

Route::get('system/login', 'System\AuthController@login');
Route::post('system/login', 'System\AuthController@auth');
Route::get('system/logout', 'System\AuthController@logout');

// reset password
Route::get('system/password/email', 'System\PasswordController@getEmail');
Route::post('system/password/email', 'System\PasswordController@postEmail');
Route::get('system/password/reset/{token}', 'System\PasswordController@getResetPassword');
Route::post('system/password/new', 'System\PasswordController@postNewPassword');


Route::get('genform', function () {
    // require bootstrap
    $data = App\Helpers\CmsHelper::genFormByName('contact', 1);
    $x = view('helper/gen_form', $data);

    return $x;
});

Route::get('reset', 'Auth\PasswordController@index');

// developer feature
Route::delete('system/content-delete-properties/{id}', "System\ContentObjectController@deleteProperties");
Route::resource('system/site-properties', "System\SitePropertieController");

Route::get('system/content-objects/getAjax', "System\ContentObjectController@getAjax");
Route::resource('system/content-objects', "System\ContentObjectController");
Route::post('system/content-add-properties/{id}', "System\ContentObjectController@addProperties");
Route::post('system/content-update-properties/{id}', "System\ContentObjectController@updateProperties");
Route::resource('system/form-objects/getAjax', "System\FormObjectController@getAjax");
Route::resource('system/form-objects', "System\FormObjectController");
Route::post('system/form-add-properties/{id}', "System\FormObjectController@addProperties");
Route::post('system/form-update-properties/{id}', "System\FormObjectController@updateProperties");
Route::delete('system/form-delete-properties/{id}', "System\FormObjectController@deleteProperties");

// administrator feature
Route::get('system/languages/getAjax', "System\LanguageController@getAjax");
// language reorder
Route::get('system/languages/reorder', "System\LanguageController@reorder");
Route::resource('system/languages', "System\LanguageController");
Route::get('system/menus/getAjax/', "System\MenuController@getAjax");
Route::resource('system/menus', "System\MenuController");

Route::get('system/translations/getAjax/', "System\TranslationController@getAjax");
Route::get('system/translations/export', [
    'as' => 'get_export_translation',
    'uses' => "System\TranslationController@getExport"
]);
Route::post('system/translations/export', [
    'as' => 'post_export_translation',
    'uses' => "System\TranslationController@postExport"
]);
Route::get('system/translations/import', [
    'as' => 'get_import_translation',
    'uses' => "System\TranslationController@getImport"
]);
Route::post('system/translations/import', [
    'as' => 'post_import_translation',
    'uses' => "System\TranslationController@postImport"
]);

Route::resource('system/translations', "System\TranslationController");

Route::get('system/redirecturls/getAjax/', "System\RedirecturlController@getAjax");
Route::resource('system/redirecturls', "System\RedirecturlController");

// general user feature
Route::get('system/export/{id}', 'System\FormController@export');

Route::get('system/sites/getAjax', "System\SiteController@getAjax");
Route::resource('system/sites', "System\SiteController");
Route::get('system/dashboard', "System\SystemController@dashboard");
Route::get('system/order/{type}', "System\SystemController@orderPropertyType");
// Route::get('system/order/{id}/{type}/{order}',"System\SystemController@orderProperty");

Route::get('system/renderType/{type_id}/{type_name}/{type}/{obj_id}', 'System\SystemController@renderType');
Route::get('system/renderType/{type_id}/{type_name}/{type}', 'System\SystemController@renderType');

// Content reorder
Route::get('system/contents/reorder/{reorderType?}/{siteId?}/{contentId?}/', ['as' => 'content_reorder', 'uses' => 'System\ContentController@reorder']);
Route::post('system/contents/updateOrder', ['as' => 'update_content_order', 'uses' => 'System\ContentController@updateOrder']);

Route::get('system/contents/create/{id}', "System\ContentController@create");
Route::get('system/contents/getAjax/{contentObjTypeId}/{contentObjId?}', "System\ContentController@getAjax");
Route::get('system/contents/{contentObjTypeId}/{contentObjId?}', "System\ContentController@show")->where('contentObjId', '[0-9]+');;
Route::resource('system/contents', "System\ContentController");

Route::get('system/forms/getAjax/{id}', "System\FormController@getAjax");
Route::get('system/forms/create/{id}', "System\FormController@create");
Route::delete('system/delete/submission/{id}', "System\FormController@destroy_submission");
Route::resource('system/forms', "System\FormController");

//Route::get('system/menus/re-order', "System\MenuController@reorder");
//Route::post('system/menus/re-order', "System\MenuController@saveReorder");

Route::get('system/menu-groups/getAjax/', "System\MenuGroupController@getAjax");
Route::resource('system/menu-groups', "System\MenuGroupController");

Route::post('system/uploadImagesAjax', 'System\SystemController@uploadImagesAjax');
Route::post('system/uploadCropImage', 'System\SystemController@uploadCropImage');
Route::get('system/get-media', 'System\SystemController@getMedia');
Route::get('system/get-rule/{id}/{type}', 'System\SystemController@getRule');

Route::get('system/users/getAjax', "System\UserController@getAjax");
Route::resource('system/users', "System\UserController");

/**
 * Route Files module
 */
// Route system/files use FilesController
Route::controller('system/files', '\Modules\Files\Http\Controllers\System\FileController', [
    'getIndex' => '`files.system.get_index`',
]);

/** Route system/file-manager use FileManagerController */
Route::controller('system/files-manager', '\Modules\Files\Http\Controllers\System\FileManagerController', [
    'getFileManagerLib' => 'files_manager.system.get_file_manager_lib',
    'getAjaxCall' => 'files_manager.system.get_ajax_call',
    'postExecute' => 'files_manager.system.post_execute',
    'postAjaxCall' => 'files_manager.system.post_ajax_call',
    'postIndex' => 'files_manager.system.post_index',
    'postUploadFile' => 'files_manager.system.post_upload_file',
    'postForceDownload' => 'files_manager.post_force_download'
]);

/**
 * WYSIWYG route
 * Route system/wysiwyg
 * use \App\Http\Controllers\System\WysiwygController
 */
// save form value with sync data
Route::controller('system/wysiwyg', '\App\Http\Controllers\System\WysiwygController', [
    'getTemplate' => 'wysiwyg.system.get_template'
]);

/**
 * Dashboard  route
 * Route system/dashboard/
 * use \App\Http\Controllers\System\DashboardController
 */
Route::controller('system/dashboard', '\App\Http\Controllers\System\DashboardController', [
    'getIndex' => 'dashboard.system.get_index'
]);

/**
 * PlUpload File route
 */
Route::controller('system/upload-file', '\App\Http\Controllers\System\UploadController', [
    'postChunkUploadFile' => 'upload_file.system.post_chunk_upload_file'
]);

/**
 * Content Import/Export Translation
 */
Route::controller('system/export/content', '\App\Http\Controllers\System\Content\ContentExportController', [
    'getExportContentListView' => 'content.system.export.get_get_export_content_list_view',
    'postExportContentListData' => 'content.system.export.post_get_export_content_list_data',
    'postExportContentTranslationsFile' => 'content.system.export.post_export_content_translations_file',
]);
Route::controller('system/import/content', '\App\Http\Controllers\System\Content\ContentImportController', [
    'getImportContentFormView' => 'content.system.import.get_get_import_content_form_view',
    'postImportContentFile' => 'content.system.import.post_import_content_file'
]);

/**
 * Content Re-order
 */
Route::controller('system/re-order', '\App\Http\Controllers\System\ReorderController', [
    'getReorderLists' => 'reorder.system.get_get_reorder_list',
    'postUpdateReorder' => 'reorder.system.post_update_reorder'
]);

/**
 * Hotel route
 */
Route::controller('system/hotel', '\App\Http\Controllers\System\Hotels\HotelController', [
    'getHotelList' => 'hotel.system.get_hotel_list',
    'getCreateHotel' => 'hotel.system.get_create_hotel',
    'getEditHotel' => 'hotel.system.get_edit_hotel',
    'postSaveHotel' => 'hotel.system.post_save_hotel',
    'postUpdateHotel' => 'hotel.system.post_update_hotel',
    'deleteUpdateHotel' => 'hotel.system.delete_update_hotel',

    /** API */
    'getGetHotelData' => 'hotel.system.get_get_hotel_data',

]);

/**
 * Hotel Property route
 */
Route::controller('system/hotel-properties', '\App\Http\Controllers\System\Hotels\HotelPropertyController', [
    'getSetHotelProperty' => 'hotel.system.get_set_hotel_property',
    'postSaveHotelProperty' => 'hotel.system.post_save_hotel_property',
    'postUpdateHotelProperty' => 'hotel.system.post_update_hotel_property',
    'deleteDeleteHotelProperty' => 'hotel.system.delete_delete_hotel_property'
]);

// save form value with sync data
Route::post('form/{id}', 'System\FormController@formSaveValue');


/**
 * Frontend feature
 */
if (config('mockup.MOCKUP_MODE') === false) {
    Route::group(['domain' => '{subdomain}.' . env('SUBDOMAIN')], function () {
        Route::get('{other_links?}', 'SubRoutingController@index');
    });

    Route::get('{other_links?}', 'RoutingController@index');

} else {
    Route::controller('/', 'MockupController');
}
