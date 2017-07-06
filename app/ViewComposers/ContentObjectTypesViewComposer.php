<?php namespace App\ViewComposers;

use App\ContentObjectType;
use Illuminate\Contracts\View\View;

class ContentObjectTypesViewComposer
{

    /**
     * ContentObjectTypesViewComposer constructor.
     */
    public function __construct()
    {

    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('content_object_types_composer', ContentObjectType::getAllContentObjectType());
    }

}