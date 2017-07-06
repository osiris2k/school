<?php namespace App\Http\Controllers;

/**
 * Class MockupController
 *
 * @package App\Http\Controllers
 */
class MockupController extends Controller
{
    private $viewPath = '';
    private $templateName = '';
    private $viewsName = '';
    private $dataForViews = [];

    /**
     * MockupController constructor.
     */
    public function __construct()
    {
        $this->initAllSettings();
    }

    /**
     * Init all settings
     */
    private function initAllSettings()
    {
        $this->initCoreSetting();
        $this->initViewData();
    }

    /**
     * Mandatory setting for mockup feature
     */
    private function initCoreSetting()
    {
        $this->viewPath = config('mockup.MOCKUP_VIEWS_PATH');
        $this->templateName = config('mockup.TEMPLATE_NAME');

        \App::singleton('user_current_language', function () {
            return 1;
        });
    }

    /**
     * Setup views data like real data from database
     */
    private function initViewData()
    {
        $this->dataForViews = [
            "language_id"        => 0,
            "language_name"      => "en",
            "site_id"            => 1,
            "site"               => [],
            "current_content_id" => 0,
            "translations"       => [],
            "currentPage"        => []
        ];

        return $this;
    }

    /**
     * Setup views path for mockup feature
     *
     * @return string
     */
    private function getViewPath()
    {
        return $this->viewPath . '.' . $this->templateName . '.' . $this->viewsName;
    }

    /**
     * Set default and dynamic data for views
     *
     * @param $pageData array
     * @return array
     */
    private function setPageData($pageData)
    {
        $returnData = [
            'meta_title'       => 'Meta Title',
            'meta_keywords'    => 'Meta Keywords',
            'meta_description' => 'Meta Description'
        ];

        return array_merge($pageData, $returnData);
    }

    /**
     * How to generate method link below
     *
     * @link https://laravel.com/docs/5.0/controllers#implicit-controllers
     */

    public function getIndex()
    {
        $this->viewsName = 'homepage';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Homepage Title',
            'images_data' => [
                //For day shift images
                [
                    "image_url" => "",
                    "is_night" => false
                ],
                [
                    "image_url" => "",
                    "is_night" => false
                ],
                [
                    "image_url" => "",
                    "is_night" => false
                ],
                //For night shift images
                [
                    "image_url" => "",
                    "is_night" => true
                ],
                [
                    "image_url" => "",
                    "is_night" => true
                ],
                [
                    "image_url" => "",
                    "is_night" => true
                ]
            ]
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getDay()
    {
        $this->viewsName = 'day';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Day Title'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getNight()
    {
        $this->viewsName = 'night';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Night Title'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getExperiences()
    {
        $this->viewsName = 'experiences';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Experiences'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getMenus()
    {
        $this->viewsName = 'menus';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Menus'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getTeam()
    {
        $this->viewsName = 'team';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Team'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getPrivateEvents()
    {
        $this->viewsName = 'private-events';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Private Events'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getSweetDeals()
    {
        $this->viewsName = 'sweet-deals';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Sweet Deals'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getSweetDealsDetail()
    {
        $this->viewsName = 'sweet-deals-detail';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Sweet Deals'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getGallery()
    {
        $this->viewsName = 'gallery';
        $pageData = $this->setPageData([
            'template' => $this->viewsName,
            'title'    => 'Gallery'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

    public function getSocialFeed()
    {
        $this->viewsName = 'social-feed';

        $pageData = $this->setPageData([
            'template'  => $this->viewsName,
            'title'     => 'Social Feed'
        ]);

        $this->dataForViews[$this->viewsName] = $pageData;
        $this->dataForViews['currentPage'] = $pageData;

        return view($this->getViewPath(), $this->dataForViews);
    }

}
