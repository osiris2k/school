<?php namespace Modules\Files\Http\Controllers\System;

use Modules\Files\Libraries\FileManager\FileManager;
use Pingpong\Modules\Routing\Controller;

class FileManagerController extends Controller
{
    public function __construct()
    {
        $this->fileManager = new FileManager([
            'exePath'      => route('files_manager.system.post_execute'),
            'ajaxCallGet'  => route('files_manager.system.get_ajax_call'),
            'ajaxCallPost' => route('files_manager.system.post_ajax_call')
        ]);
        $this->fileManager = $this->fileManager->getFileMangerSetting();
    }

    public function getAjaxCall()
    {
        require $this->fileManager['lib_path'] . '/ajax_calls.php';
    }

    public function postExecute()
    {
        require $this->fileManager['lib_path'] . '/execute.php';
    }

    public function postAjaxCall()
    {
        require $this->fileManager['lib_path'] . '/ajax_calls.php';
    }

    public function postIndex()
    {
        return view('files::index');
    }

    public function postUploadFile()
    {
        require $this->fileManager['lib_path'] . '/upload.php';
    }

    public function postForceDownload()
    {
        include $this->fileManager['lib_path'] . '/force_download.php';
    }

    public function getFileManagerLib()
    {
        $fileManager = $this->fileManager;

        return view('files::lib.file_manager.file_manager', compact('fileManager'));
    }
}