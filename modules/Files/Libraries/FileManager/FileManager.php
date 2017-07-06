<?php namespace Modules\Files\Libraries\FileManager;

use \Request;

class FileManager
{
    const CLASS_PATH = "FileManager";
    // TODO Maybe refactor property to array instead of separate property
    private $config;
    protected $lib_path;
    protected $subdir;
    protected $hidden_folders;
    protected $parent;
    protected $rfm_subfolder;
    protected $current_path;
    protected $upload_dir;
    protected $thumbs_base_path;
    protected $cur_dir;
    protected $cur_path;
    protected $thumbs_path;
    protected $default_view;
    protected $apply;
    protected $view;
    protected $filter;
    protected $sort_by;
    protected $descending;
    protected $boolarray;
    protected $get_params;
    protected $popup;
    protected $crossdomain;
    protected $return_relative_url;
    protected $editor;
    protected $field_id;
    protected $type_param;
    protected $lang;
    protected $overPath = [];

    public function __construct($arguments = [])
    {
        $this->lib_path = $this->getFileManagerPath();

        $this->config = $this->setConfig();

        $this->checkAccessKey();
        $this->setVerifySession();
        $this->setLanguageSession();
        $this->setCurrentPath();
        $this->setUploadDir();
        $this->setThumbsBasePath();
        $this->setSubDir();
        $this->setRfmSubFolder();
        $this->setDefaultView();
        $this->setOverridePath($arguments);
        $this->setFileManagerVariable();
    }

    private function setConfig()
    {
        return include($this->lib_path . '/config/config.php');
    }

    // TODO Change die to throw exception
    private function checkAccessKey()
    {
        $access_keys = $this->config['access_keys'];

        if (USE_ACCESS_KEYS == true) {
            if (!isset($_GET['akey'], $access_keys) || empty($access_keys)) {
                die('Access Denied!');
            }

            $_GET['akey'] = strip_tags(preg_replace("/[^a-zA-Z0-9\._-]/", '', $_GET['akey']));

            if (!in_array($_GET['akey'], $access_keys)) {
                die('Access Denied!');
            }
        }

        return true;
    }

    private function setVerifySession()
    {
        $_SESSION['RF']["verify"] = "RESPONSIVEfilemanager";
    }

    private function setLanguageSession()
    {
        // Define default language session if not existed
        if (!isset($_SESSION['RF']['language_file']) && !isset($_SESSION['RF']['language'])) {
            $_SESSION['RF']['language'] = $this->config['default_language'];
            $_SESSION['RF']['language_file'] = $this->lib_path . '/lang/' . $this->config['default_language'] . '.php';
        }

    }

    private function setSubFolderSession()
    {
        if (isset($_GET['subfolder']) && $_GET['subfolder'] != '') {
            $_SESSION['RF']["subfolder"] = $_GET['subfolder'];
        } else {
            $_SESSION['RF']["subfolder"] = '';
        }
        /*if (!isset($_SESSION['RF']["subfolder"])) {
            $_SESSION['RF']["subfolder"] = '';
        }*/
    }

    private function setCurrentPath()
    {
        $this->current_path = $this->config['current_path'];
    }

    private function setUploadDir()
    {
        $this->upload_dir = $this->config['upload_dir'];
    }

    private function setThumbsBasePath()
    {
        $this->thumbs_base_path = $this->config['thumbs_base_path'];
    }

    private function setDefaultView()
    {
        $this->default_view = $this->config['default_view'];
    }

    private function setPopup()
    {
        if (isset($_GET['popup'])) {
            $this->popup = strip_tags($_GET['popup']);
        } else $this->popup = 0;
        //Sanitize popup
        $this->popup = !!$this->popup;

        return $this->popup;
    }

    private function setCrossDomain()
    {
        if (isset($_GET['crossdomain'])) {
            $this->crossdomain = strip_tags($_GET['crossdomain']);
        } else $this->crossdomain = 0;

        //Sanitize crossdomain
        $this->crossdomain = !!$this->crossdomain;

        return $this->crossdomain;
    }

    private function setViewType()
    {
        if (!isset($_SESSION['RF']["view_type"])) {
            $this->view = $this->default_view;
            $_SESSION['RF']["view_type"] = $this->view;
        }

        if (isset($_GET['view'])) {
            $this->view = fix_get_params($_GET['view']);
            $_SESSION['RF']["view_type"] = $this->view;
        }

        $this->view = $_SESSION['RF']["view_type"];
    }

    private function setFilter()
    {
        //filter
        $this->filter = "";
        if (isset($_SESSION['RF']["filter"])) {
            $this->filter = $_SESSION['RF']["filter"];
        }

        if (isset($_GET["filter"])) {
            $this->filter = fix_get_params($_GET["filter"]);
        }
    }

    private function setSortBySession()
    {
        if (!isset($_SESSION['RF']['sort_by'])) {
            $_SESSION['RF']['sort_by'] = 'date';
        }

        if (isset($_GET["sort_by"])) {
            $this->sort_by = $_SESSION['RF']['sort_by'] = fix_get_params($_GET["sort_by"]);
        } else $this->sort_by = $_SESSION['RF']['sort_by'];
    }

    private function setDescendingSession()
    {
        if (!isset($_SESSION['RF']['descending'])) {
            $_SESSION['RF']['descending'] = true;
        }
    }

    private function setDescending()
    {
        if (isset($_GET["descending"])) {
            $this->descending = $_SESSION['RF']['descending'] = fix_get_params($_GET["descending"]) == 1;
        } else {
            $this->descending = $_SESSION['RF']['descending'];
        }
    }

    private function setBoolArray()
    {
        $this->boolarray = Array(false => 'false', true => 'true');
    }

    private function setReturnRelativeUrl()
    {
        $this->return_relative_url = isset($_GET['relative_url']) && $_GET['relative_url'] == "1" ? true : false;
    }

    private function setHttpGetType()
    {
        if (!isset($_GET['type'])) $_GET['type'] = 0;
    }

    private function setEditor()
    {
        if (isset($_GET['editor'])) {
            $this->editor = strip_tags($_GET['editor']);
        } else {
            if ($_GET['type'] == 0) {
                $this->editor = false;
            } else {
                $this->editor = 'tinymce';
            }
        }
    }

    private function setFieldId()
    {
        include_once 'include/utils.php';
        if (!isset($_GET['field_id'])) $_GET['field_id'] = '';
        $this->field_id = isset($_GET['field_id']) ? fix_get_params($_GET['field_id']) : '';
    }

    private function setTypeParam()
    {
        $this->type_param = fix_get_params($_GET['type']);
    }

    private function setApply()
    {
        if ($this->type_param == 1) $this->apply = 'apply_img';
        elseif ($this->type_param == 2) $this->apply = 'apply_link';
        elseif ($this->type_param == 0 && $_GET['field_id'] == '') $this->apply = 'apply_none';
        elseif ($this->type_param == 3) $this->apply = 'apply_video';
        else $this->apply = 'apply';
    }

    private function setLang()
    {
        $this->lang = $this->config['default_language'];
    }

    private function setParams()
    {
        $this->get_params = array(
            'editor'                   => $this->editor,
            'type'                     => $this->type_param,
            'lang'                     => $this->lang,
            'popup'                    => $this->popup,
            'crossdomain'              => $this->crossdomain,
            'field_id'                 => $this->field_id,
            'relative_url'             => $this->return_relative_url,
            'akey'                     => (isset($_GET['akey']) && $_GET['akey'] != '' ? $_GET['akey'] : 'key'),
            // More condition
            'multiple_file'            => Request::input('multiple_file'),
            'cms_lang'                 => Request::input('cms_lang'),
            'variable_name'            => Request::input('variable_name'),
            'override_allow_extension' => Request::input('override_allow_extension'),
            'subfolder'                => Request::input('subfolder')
        );

        if (isset($_GET['CKEditorFuncNum'])) {
            $this->get_params['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
            $this->get_params['CKEditor'] = (isset($_GET['CKEditor']) ? $_GET['CKEditor'] : '');
        }
        $this->get_params['fldr'] = '';

        $this->get_params = http_build_query($this->get_params);
    }

    private function setFilterSession($val = '')
    {
        $_SESSION['RF']["filter"] = $val;
    }

    private function setLastPositionCookie($subdir)
    {
        //remember last position
        $_SESSION['last_position'] = $subdir;
    }

    private function setHiddenFolders()
    {
        $this->hidden_folders = $this->config['hidden_folders'];
    }

    private function setSubDir()
    {
        if (isset($_GET['fldr'])
            && strpos($_GET['fldr'], '../') === false
            && strpos($_GET['fldr'], './') === false
        ) {
            $this->subdir = urldecode(trim(strip_tags($_GET['fldr']), "/") . "/");
            $this->setFilterSession();
        }

        if ($this->subdir == "") {
            if (!empty($_SESSION['last_position'])
                && strpos($_SESSION['last_position'], '.') === false
            )
                $this->subdir = trim($_SESSION['last_position']);
        }

        $this->setLastPositionCookie($this->subdir);

        if ($this->subdir == "/") {
            $this->subdir = "";
        }

        $this->setHiddenFolders();
        // If hidden folders are specified
        if (count($this->hidden_folders)) {
            // If hidden folder appears in the path specified in URL parameter "fldr"
            $dirs = explode('/', $this->subdir);
            foreach ($dirs as $dir) {
                if ($dir !== '' && in_array($dir, $this->hidden_folders)) {
                    // Ignore the path
                    $this->subdir = "";
                    break;
                }
            }
        }

        return $this;
    }

    private function setRfmSubFolder()
    {
        $this->setSubFolderSession();
        $this->rfm_subfolder = '';

        if (!empty($_SESSION['RF']["subfolder"]) && strpos($_SESSION['RF']["subfolder"], '../') === false
            && strpos($_SESSION['RF']["subfolder"], './') === false && strpos($_SESSION['RF']["subfolder"], "/") !== 0
            && strpos($_SESSION['RF']["subfolder"], '.') === false
        ) {
            $this->rfm_subfolder = $_SESSION['RF']['subfolder'];
        }

        if ($this->rfm_subfolder != "" && $this->rfm_subfolder[strlen($this->rfm_subfolder) - 1] != "/") {
            $this->rfm_subfolder .= "/";
        }

        if (!file_exists($this->current_path . $this->rfm_subfolder . $this->subdir)) {
            $this->subdir = '';
            if (!file_exists($this->current_path . $this->rfm_subfolder . $this->subdir)) {
                $this->rfm_subfolder = "";
            }
        }

        if (trim($this->rfm_subfolder) == "") {
            $this->cur_dir = $this->upload_dir . $this->subdir;
            $this->cur_path = $this->current_path . $this->subdir;
            $this->thumbs_path = $this->thumbs_base_path;
            $this->parent = $this->subdir;
        } else {
            $this->cur_dir = $this->upload_dir . $this->rfm_subfolder . $this->subdir;
            $this->cur_path = $this->current_path . $this->rfm_subfolder . $this->subdir;
            $this->thumbs_path = $this->thumbs_base_path . $this->rfm_subfolder;
            $this->parent = $this->rfm_subfolder . $this->subdir;
        }
    }

    private function setFileManagerVariable()
    {
        include_once 'include/utils.php';
        $cycle = true;
        $max_cycles = 50;
        $i = 0;
        while ($cycle && $i < $max_cycles) {
            $i++;
            if ($this->parent == "./") $this->parent = "";

            if (file_exists($this->current_path . $this->parent . "config.php")) {
                require_once $this->current_path . $this->parent . "config.php";
                $cycle = false;
            }

            if ($this->parent == "") $cycle = false;
            else $this->parent = fix_dirname($this->parent) . "/";
        }

        if (!is_dir($this->thumbs_path . $this->subdir)) {
            create_folder(false, $this->thumbs_path . $this->subdir);
        }

        $this->popup = $this->setPopup();

        $this->crossdomain = $this->setCrossDomain();

        $this->setViewType();

        $this->setFilter();

        $this->setSortBySession();

        $this->setDescendingSession();

        $this->setDescending();

        $this->setBoolArray();

        $this->setReturnRelativeUrl();

        $this->setHttpGetType();

        $this->setEditor();

        $this->setFieldId();

        $this->setTypeParam();

        $this->setApply();

        // Define default $lang for missing variable purpose
        $this->setLang();

        $this->setParams();

    }

    private function getConfig()
    {
        return $this->config;
    }

    private function getSubDir()
    {
        return $this->subdir;
    }

    private function getParent()
    {
        return $this->parent;
    }

    private function getThumbsPath()
    {
        return $this->thumbs_base_path;
    }

    private function getCurDir()
    {
        return $this->cur_dir;
    }

    private function getCurPath()
    {
        return $this->cur_path;
    }

    private function getFrmSubFolder()
    {
        return $this->rfm_subfolder;
    }

    private function getReturnRelativeUrl()
    {
        return $this->return_relative_url;
    }

    private function getApply()
    {
        return $this->apply;
    }

    private function getFieldId()
    {
        return $this->field_id;
    }

    private function getPopup()
    {
        return $this->popup;
    }

    private function getCrossDomain()
    {
        return $this->crossdomain;
    }

    private function getEditor()
    {
        return $this->editor;
    }

    private function getView()
    {
        return $this->view;
    }

    private function getTypeParam()
    {
        return $this->type_param;
    }

    private function getSortBy()
    {
        return $this->sort_by;
    }

    private function getDescending()
    {
        return $this->descending;
    }

    private function getFilter()
    {
        return $this->filter;
    }

    private function getGetParams()
    {
        return $this->get_params;
    }

    private function getLang()
    {
        return $this->lang;
    }

    private function getFileManagerPath()
    {
        return config('files.module_path') . config('files.lib_path') . self::CLASS_PATH;
    }

    private function setOverridePath($arguments = [])
    {
        $this->overPath['exePath'] = $arguments['exePath'];
        $this->overPath['ajaxCallGet'] = $arguments['ajaxCallGet'];
        $this->overPath['ajaxCallPost'] = $arguments['ajaxCallPost'];
    }

    public function getFileMangerSetting()
    {
        return [
            'lib_path'                 => $this->lib_path,
            'config'                   => $this->getConfig(),
            'subdir'                   => $this->getSubDir(),
            'cur_dir'                  => $this->getCurDir(),
            'cur_path'                 => $this->getCurPath(),
            'thumbs_path'              => $this->getThumbsPath(),
            'parent'                   => $this->getParent(),
            'rfm_subfolder'            => $this->getFrmSubFolder(),
            'apply'                    => $this->getApply(),
            'field_id'                 => $this->getFieldId(),
            'popup'                    => $this->getPopup(),
            'crossdomain'              => $this->getCrossDomain(),
            'editor'                   => $this->getEditor(),
            'view'                     => $this->getView(),
            'type_param'               => $this->getTypeParam(),
            'return_relative_url'      => $this->getReturnRelativeUrl(),
            'sort_by'                  => $this->getSortBy(),
            'descending'               => $this->getDescending(),
            'filter'                   => $this->getFilter(),
            'get_params'               => $this->getGetParams(),
            'lang'                     => $this->getLang(),
            'exePath'                  => $this->overPath['exePath'],
            'ajaxCallGet'              => $this->overPath['ajaxCallGet'],
            'ajaxCallPost'             => $this->overPath['ajaxCallPost'],

            // More condition
            'multiple_file'            => Request::input('multiple_file'),
            'cms_lang'                 => Request::input('cms_lang'),
            'variable_name'            => Request::input('variable_name'),
            'override_allow_extension' => Request::input('override_allow_extension'),
            'subfolder'                => Request::input('subfolder')
        ];
    }

//    public static function __callStatic($method, $arguments)
//    {
//        $instance = new static;
//
//        return call_user_func_array(array($instance, $method), $arguments);
//    }
}