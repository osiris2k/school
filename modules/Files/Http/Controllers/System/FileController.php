<?php namespace Modules\Files\Http\Controllers\System;

use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Routing\Controller;

class FileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['page_herader'] = 'Files Management';
        $this->data['tag_first_menu'] = 'files';
    }

    public function getIndex()
    {
        return view('files::system.files',$this->data);
    }

}