<?php
Route::group(['prefix' => '/', 'namespace' => 'Modules\Files\Http\Controllers\System'], function () {
    Route::controller('system/files', 'FileManagerController', [
        'getIndex'          => 'files.system.get_index',
        'getFileManagerLib' => 'files.system.get_file_manager_lib',
        'getAjaxCall'       => 'files.system.get_ajax_call',
        'postExecute'       => 'files.system.post_execute',
        'postAjaxCall'      => 'files.system.post_ajax_call',
        'postIndex'         => 'files.system.post_index',
        'postUploadFile'    => 'files.system.post_upload_file',
        'postForceDownload' => 'files.post_force_download'
    ]);
});

