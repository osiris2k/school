<?php namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

class WysiwygController extends Controller
{
    public function getTemplate($template)
    {
        return view('templates.'.config('app.template_name').'.wysiwyg.'.$template);
    }
}