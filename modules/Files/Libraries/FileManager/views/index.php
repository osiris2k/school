<!-- TODO Clean code -->
<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="robots" content="noindex,nofollow">
    <title>Media Management</title>
    <link rel="shortcut icon" href="<?php echo asset('favicon.ico') ?>">
    <link href="<?php echo asset('modules/files/libraries/file_manager/css/style.css') ?>" rel="stylesheet"
          type="text/css"/>
    <link
        href="<?php echo asset('modules/files/libraries/file_manager/js/jPlayer/skin/blue.monday/jplayer.blue.monday.css') ?>"
        rel="stylesheet" type="text/css">
    <!--[if lt IE 8]>
    <style>
        .img-container span, .img-container-mini span {
            display: inline-block;
            height: 100%;
        }
    </style><![endif]-->
    <script src="<?php echo asset('modules/files/libraries/file_manager/js/plugins.js'); ?>"></script>
    <script
        src="<?php echo asset('modules/files/libraries/file_manager/js/jPlayer/jquery.jplayer/jquery.jplayer.js'); ?>"></script>
    <script src="<?php echo asset('modules/files/libraries/file_manager/js/modernizr.custom.js'); ?>"></script>

</head>
<body>
<!-- Begin upload form -->
<?php
if ($upload_files) {
    include('upload_form.php');
} ?>
<!-- End upload form -->

<div class="container-fluid">

    <?php

    $class_ext = '';
    $src = '';

    $files = scandir($current_path . $rfm_subfolder . $subdir);
    $n_files = count($files);

    //php sorting
    $sorted = array();
    $current_folder = array();
    $prev_folder = array();
    $current_files_number = 0;
    $current_folders_number = 0;

    foreach ($files as $k => $file) {
        if ($file == ".") $current_folder = array('file' => $file);
        elseif ($file == "..") $prev_folder = array('file' => $file);
        elseif (is_dir($current_path . $rfm_subfolder . $subdir . $file)) {
            $date = filemtime($current_path . $rfm_subfolder . $subdir . $file);
            if ($show_folder_size) {
                list($size, $nfiles, $nfolders) = folder_info($current_path . $rfm_subfolder . $subdir . $file);
                $current_folders_number++;
            } else {
                $size = 0;
            }
            $file_ext = file_manager_trans('Type_dir');
            $sorted[$k] = array(
                'file'            => $file,
                'file_lcase'      => strtolower($file),
                'date'            => $date,
                'size'            => $size,
                'nfiles'          => $nfiles,
                'nfolders'        => $nfolders,
                'extension'       => $file_ext,
                'extension_lcase' => strtolower($file_ext));
        } else {
            $current_files_number++;
            $file_path = $current_path . $rfm_subfolder . $subdir . $file;
            $date = filemtime($file_path);
            $size = filesize($file_path);
            $file_ext = substr(strrchr($file, '.'), 1);
            $sorted[$k] = array('file' => $file, 'file_lcase' => strtolower($file), 'date' => $date, 'size' => $size, 'extension' => $file_ext, 'extension_lcase' => strtolower($file_ext));
        }
    }

    // Should lazy loading be enabled
    $lazy_loading_enabled = ($lazy_loading_file_number_threshold == 0 || $lazy_loading_file_number_threshold != -1 && $n_files > $lazy_loading_file_number_threshold) ? true : false;

    switch ($sort_by) {
        case 'date':
            usort($sorted, 'dateSort');
            break;
        case 'size':
            usort($sorted, 'sizeSort');
            break;
        case 'extension':
            usort($sorted, 'extensionSort');
            break;
        default:
            usort($sorted, 'filenameSort');
            break;
    }

    if (!$descending) {
        $sorted = array_reverse($sorted);
    }

    $files = array_merge(array($prev_folder), array($current_folder), $sorted);
    ?>
    <!-- header div start -->
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="brand"><?php echo file_manager_trans('Toolbar'); ?></div>
                <div class="nav-collapse collapse">
                    <div class="filters">
                        <div class="row-fluid">
                            <div class="span4 half">
                                <?php if ($upload_files) { ?>
                                    <button class="tip btn upload-btn"
                                            title="<?php echo file_manager_trans('Upload_file'); ?>">
                                        <i class="rficon-upload"></i></button>
                                <?php } ?>
                                <?php if ($create_text_files) { ?>
                                    <!--<button class="tip btn create-file-btn"
                                            title="<?php echo file_manager_trans('New_File'); ?>"><i
                                            class="icon-plus"></i><i
                                            class="icon-file"></i></button>-->
                                <?php } ?>
                                <?php if ($create_folders) { ?>
                                    <button class="tip btn new-folder"
                                            title="<?php echo file_manager_trans('New_Folder') ?>"><i
                                            class="icon-plus"></i><i class="icon-folder-open"></i></button>
                                <?php } ?>
                                <?php if ($copy_cut_files || $copy_cut_dirs) { ?>
                                    <button class="tip btn paste-here-btn"
                                            title="<?php echo file_manager_trans('Paste_Here'); ?>"><i
                                            class="rficon-clipboard-apply"></i></button>
                                    <button class="tip btn clear-clipboard-btn"
                                            title="<?php echo file_manager_trans('Clear_Clipboard'); ?>"><i
                                            class="rficon-clipboard-clear"></i></button>
                                <?php } ?>
                            </div>
                            <div class="span2 half view-controller">
                                <button class="btn tip<?php if ($view == 0) echo " btn-inverse"; ?>" id="view0"
                                        data-value="0" title="<?php echo file_manager_trans('View_boxes'); ?>"><i
                                        class="icon-th <?php if ($view == 0) echo "icon-white"; ?>"></i></button>
                                <button class="btn tip<?php if ($view == 1) echo " btn-inverse"; ?>" id="view1"
                                        data-value="1" title="<?php echo file_manager_trans('View_list'); ?>"><i
                                        class="icon-align-justify <?php if ($view == 1) echo "icon-white"; ?>"></i>
                                </button>
                                <button class="btn tip<?php if ($view == 2) echo " btn-inverse"; ?>" id="view2"
                                        data-value="2" title="<?php echo file_manager_trans('View_columns_list'); ?>"><i
                                        class="icon-fire <?php if ($view == 2) echo "icon-white"; ?>"></i></button>
                            </div>
                            <div class="span6 entire types">
                                <span><?php echo file_manager_trans('Filters'); ?>:</span>
                                <?php if ($_GET['type'] != 1 && $_GET['type'] != 3) { ?>
                                    <input id="select-type-1" name="radio-sort" type="radio"
                                           data-item="ff-item-type-1" checked="checked" class="hide"/>
                                    <label id="ff-item-type-1" title="<?php echo file_manager_trans('Files'); ?>"
                                           for="select-type-1" class="tip btn ff-label-type-1"><i
                                            class="icon-file"></i></label>
                                    <input id="select-type-2" name="radio-sort" type="radio"
                                           data-item="ff-item-type-2" class="hide"/>
                                    <label id="ff-item-type-2" title="<?php echo file_manager_trans('Images'); ?>"
                                           for="select-type-2" class="tip btn ff-label-type-2"><i
                                            class="icon-picture"></i></label>
                                    <input id="select-type-3" name="radio-sort" type="radio"
                                           data-item="ff-item-type-3" class="hide"/>
                                    <label id="ff-item-type-3" title="<?php echo file_manager_trans('Archives'); ?>"
                                           for="select-type-3" class="tip btn ff-label-type-3"><i
                                            class="icon-inbox"></i></label>
                                    <input id="select-type-4" name="radio-sort" type="radio"
                                           data-item="ff-item-type-4" class="hide"/>
                                    <label id="ff-item-type-4" title="<?php echo file_manager_trans('Videos'); ?>"
                                           for="select-type-4" class="tip btn ff-label-type-4"><i
                                            class="icon-film"></i></label>
                                    <input id="select-type-5" name="radio-sort" type="radio"
                                           data-item="ff-item-type-5" class="hide"/>
                                    <label id="ff-item-type-5" title="<?php echo file_manager_trans('Music'); ?>"
                                           for="select-type-5" class="tip btn ff-label-type-5"><i
                                            class="icon-music"></i></label>
                                <?php } ?>
                                <input accesskey="f" type="text"
                                       class="filter-input <?php echo(($_GET['type'] != 1 && $_GET['type'] != 3) ? '' : 'filter-input-notype'); ?>"
                                       id="filter-input" name="filter"
                                       placeholder="<?php echo fix_strtolower(file_manager_trans('Text_filter')); ?>..."
                                       value="<?php echo $filter; ?>"/><?php if ($n_files > $file_number_limit_js) { ?>
                                    <label id="filter" class="btn"><i class="icon-play"></i></label><?php } ?>

                                <input id="select-type-all" name="radio-sort" type="radio"
                                       data-item="ff-item-type-all" class="hide"/>
                                <label id="ff-item-type-all" title="<?php echo file_manager_trans('All'); ?>"
                                       <?php if ($_GET['type'] == 1 || $_GET['type'] == 3){ ?>style="visibility: hidden;"
                                    <?php } ?>
                                       data-item="ff-item-type-all" for="select-type-all" style="margin-rigth:0px;"
                                       class="tip btn btn-inverse ff-label-type-all"><i
                                        class="icon-remove icon-white"></i></label>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- header div end -->

    <!-- breadcrumb div start -->

    <div class="row-fluid">
        <?php
        $link = route('files_manager.system.get_file_manager_lib') . '?' . $get_params;
        ?>
        <ul class="breadcrumb">
            <li class="pull-left"><a href="<?php echo $link ?>"><i class="icon-home"></i></a></li>
            <li><span class="divider">/</span></li>
            <?php
            $bc = explode("/", $subdir);
            $tmp_path = '';
            if (!empty($bc))
                foreach ($bc as $k => $b) {
                    $tmp_path .= $b . "/";
                    if ($k == count($bc) - 2) {
                        ?>
                        <li class="active"><?php echo $b ?></li><?php
                    } elseif ($b != "") { ?>
                        <li><a href="<?php echo $link . $tmp_path ?>"><?php echo $b ?></a></li>
                        <li><span class="divider"><?php echo "/"; ?></span></li>
                    <?php }
                }
            ?>
            <li class="pull-right">
                <a id="refresh" class="btn-small"
                   href="<?php echo route('files_manager.system.get_file_manager_lib'); ?>?<?php echo $get_params . $subdir . "&" . uniqid() ?>">
                    <i class="icon-refresh"></i></a></li>

            <li class="pull-right">
                <div class="btn-group">
                    <a class="btn dropdown-toggle sorting-btn" data-toggle="dropdown" href="#">
                        <i class="icon-signal"></i>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-left sorting">
                        <li class="text-center"><strong><?php echo file_manager_trans('Sorting') ?></strong></li>
                        <li><a class="sorter sort-name <?php if ($sort_by == "name") {
                                echo ($descending) ? "descending" : "ascending";
                            } ?>" href="javascript:void('')"
                               data-sort="name"><?php echo file_manager_trans('Filename'); ?></a>
                        </li>
                        <li><a class="sorter sort-date <?php if ($sort_by == "date") {
                                echo ($descending) ? "descending" : "ascending";
                            } ?>" href="javascript:void('')"
                               data-sort="date"><?php echo file_manager_trans('Date'); ?></a></li>
                        <li><a class="sorter sort-size <?php if ($sort_by == "size") {
                                echo ($descending) ? "descending" : "ascending";
                            } ?>" href="javascript:void('')"
                               data-sort="size"><?php echo file_manager_trans('Size'); ?></a></li>
                        <li><a class="sorter sort-extension <?php if ($sort_by == "extension") {
                                echo ($descending) ? "descending" : "ascending";
                            } ?>" href="javascript:void('')"
                               data-sort="extension"><?php echo file_manager_trans('Type'); ?></a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <small class="hidden-phone">(<span
                        id="files_number"><?php echo $current_files_number . "</span> " . file_manager_trans('Files') . " - <span id='folders_number'>" . $current_folders_number . "</span> " . file_manager_trans('Folders'); ?>
                        )</small>
            </li>
        </ul>
    </div>
    <!-- breadcrumb div end -->
    <div class="row-fluid ff-container">
        <div class="span12">
            <?php if (@opendir($current_path . $rfm_subfolder . $subdir) === false){ ?>
            <br/>

            <div class="alert alert-error">There is an error! The upload folder there isn't. Check your config.php
                file.
            </div>
            <?php }else{ ?>
            <h4 id="help"><?php echo file_manager_trans('Swipe_help'); ?></h4>
            <?php if(isset($folder_message)){ ?>
            <div class="alert alert-block"><?php echo $folder_message; ?></div>
            <?php } ?>
            <?php if($show_sorting_bar){ ?>
            <!-- sorter -->
            <div class="sorter-container <?php echo "list-view" . $view; ?>">
                <div class="file-name"><a class="sorter sort-name <?php if ($sort_by == "name") {
                        echo ($descending) ? "descending" : "ascending";
                    } ?>" href="javascript:void('')" data-sort="name"><?php echo file_manager_trans('Filename'); ?></a>
                </div>
                <div class="file-date"><a class="sorter sort-date <?php if ($sort_by == "date") {
                        echo ($descending) ? "descending" : "ascending";
                    } ?>" href="javascript:void('')" data-sort="date"><?php echo file_manager_trans('Date'); ?></a>
                </div>
                <div class="file-size"><a class="sorter sort-size <?php if ($sort_by == "size") {
                        echo ($descending) ? "descending" : "ascending";
                    } ?>" href="javascript:void('')" data-sort="size"><?php echo file_manager_trans('Size'); ?></a>
                </div>
                <div class='img-dimension'><?php echo file_manager_trans('Dimension'); ?></div>
                <div class='file-extension'><a class="sorter sort-extension <?php if ($sort_by == "extension") {
                        echo ($descending) ? "descending" : "ascending";
                    } ?>" href="javascript:void('')" data-sort="extension"><?php echo file_manager_trans('Type'); ?></a>
                </div>
                <div class='file-operations'><?php echo file_manager_trans('Operations'); ?></div>
            </div>
            <?php } ?>

            <input type="hidden" id="file_number" value="<?php echo $n_files; ?>"/>
            <!--ul class="thumbnails ff-items"-->
            <ul class="grid cs-style-2 <?php echo "list-view" . $view; ?>" id="main-item-container">
                <?php

                $jplayer_ext = array("mp4", "flv", "webmv", "webma", "webm", "m4a", "m4v", "ogv", "oga", "mp3", "midi", "mid", "ogg", "wav");
                foreach ($files as $file_array) {
                    $file = $file_array['file'];
                    if ($file == '.' || (isset($file_array['extension']) && $file_array['extension'] != file_manager_trans('Type_dir')) || ($file == '..' && $subdir == '') || in_array($file, $hidden_folders) || ($filter != '' && $n_files > $file_number_limit_js && $file != ".." && stripos($file, $filter) === false))
                        continue;
                    $new_name = fix_filename($file, $transliteration);
                    if ($file != '..' && $file != $new_name) {
                        //rename
                        rename_folder($current_path . $subdir . $file, $new_name, $transliteration);
                        $file = $new_name;
                    }
                    //add in thumbs folder if not exist
                    if (!file_exists($thumbs_path . $subdir . $file)) create_folder(false, $thumbs_path . $subdir . $file);
                    $class_ext = 3;
                    if ($file == '..' && trim($subdir) != '') {
                        $src = explode("/", $subdir);
                        unset($src[count($src) - 2]);
                        $src = implode("/", $src);
                        if ($src == '') $src = "/";
                    } elseif ($file != '..') {
                        $src = $subdir . $file . "/";
                    }

                    ?>
                    <li data-name="<?php echo $file ?>"
                        class="<?php if ($file == '..') echo 'back'; else echo 'dir'; ?>" <?php if (($filter != '' && stripos($file, $filter) === false)) echo ' style="display:none;"'; ?>><?php
                        $file_prevent_rename = false;
                        $file_prevent_delete = false;
                        if (isset($filePermissions[$file])) {
                            $file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
                            $file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
                        }
                        ?>
                        <figure data-name="<?php echo $file ?>"
                                class="<?php if ($file == "..") echo "back-"; ?>directory"
                                data-type="<?php if ($file != "..") {
                                    echo "dir";
                                } ?>">
                            <?php if($file == ".."){ ?>
                            <input type="hidden" class="path"
                                   value="<?php echo str_replace('.', '', dirname($rfm_subfolder . $subdir)); ?>"/>
                            <input type="hidden" class="path_thumb"
                                   value="<?php echo dirname($thumbs_path . $subdir) . "/"; ?>"/>
                            <?php } ?>
                            <!--<a class="folder-link"
                           href="dialog.php?<?php echo $get_params . rawurlencode($src) . "&" . uniqid() ?>">-->
                            <a class="folder-link"
                               href="<?php echo route('files_manager.system.get_file_manager_lib'); ?>?<?php echo $get_params . rawurlencode($src) . "&" . uniqid() ?>">
                                <div class="img-precontainer">
                                    <div class="img-container directory"><span></span>
                                        <img class="directory-img"
                                             src="<?php echo asset('modules/files/libraries/file_manager/') ?>/img/<?php echo $icon_theme; ?>/folder<?php if ($file == "..") {
                                                 echo "_back";
                                             } ?>.png"/>
                                    </div>
                                </div>
                                <div class="img-precontainer-mini directory">
                                    <div class="img-container-mini">
                                        <span></span>
                                        <img class="directory-img"
                                             src="<?php echo asset('modules/files/libraries/file_manager/') ?>/img/<?php echo $icon_theme; ?>/folder<?php if ($file == "..") {
                                                 echo "_back";
                                             } ?>.png"/>
                                    </div>
                                </div>
                                <?php if ($file == ".."){ ?>
                                <div class="box no-effect">
                                    <h4><?php echo file_manager_trans('Back') ?></h4>
                                </div>
                            </a>

                            <?php }else{ ?>
                            </a>
                            <div class="box">
                                <h4 class="<?php if ($ellipsis_title_after_first_row) {
                                    echo "ellipsis";
                                } ?>">
                                    <a class="folder-link" data-file="<?php echo $file ?>"
                                       href="<?php echo route('files_manager.system.get_file_manager_lib'); ?>?<?php echo $get_params . rawurlencode($src) . "&" . uniqid() ?>"><?php echo $file; ?></a>
                                </h4>
                            </div>
                            <input type="hidden" class="name" value="<?php echo $file_array['file_lcase']; ?>"/>
                            <input type="hidden" class="date" value="<?php echo $file_array['date']; ?>"/>
                            <input type="hidden" class="size" value="<?php echo $file_array['size']; ?>"/>
                            <input type="hidden" class="extension"
                                   value="<?php echo file_manager_trans('Type_dir'); ?>"/>

                            <div
                                class="file-date"><?php echo date(file_manager_trans('Date_type'), $file_array['date']) ?></div>
                            <?php if($show_folder_size){ ?>
                            <div class="file-size"><?php echo makeSize($file_array['size']) ?></div>
                            <input type="hidden" class="nfiles" value="<?php echo $file_array['nfiles']; ?>"/>
                            <input type="hidden" class="nfolders" value="<?php echo $file_array['nfolders']; ?>"/>
                            <?php } ?>
                            <div class='file-extension'><?php echo file_manager_trans('Type_dir'); ?></div>
                            <figcaption>
                                <a href="javascript:void('')"
                                   class="tip-left edit-button rename-file-paths <?php if ($rename_folders && !$file_prevent_rename) echo "rename-folder"; ?>"
                                   title="<?php echo file_manager_trans('Rename') ?>"
                                   data-path="<?php echo $rfm_subfolder . $subdir . $file; ?>">
                                    <i class="icon-pencil <?php if (!$rename_folders || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>
                                <a href="javascript:void('')"
                                   class="tip-left erase-button <?php if ($delete_folders && !$file_prevent_delete) echo "delete-folder"; ?>"
                                   title="<?php echo file_manager_trans('Erase') ?>"
                                   data-confirm="<?php echo file_manager_trans('Confirm_Folder_del'); ?>"
                                   data-path="<?php echo $rfm_subfolder . $subdir . $file; ?>">
                                    <i class="icon-trash <?php if (!$delete_folders || $file_prevent_delete) echo 'icon-white'; ?>"></i>
                                </a>
                            </figcaption>
                            <?php } ?>
                        </figure>
                    </li>
                    <?php
                }
                $files_prevent_duplicate = array();
                foreach ($files as $nu => $file_array) {
                $file = $file_array['file'];

                if ($file == '.' || $file == '..' || is_dir($current_path . $rfm_subfolder . $subdir . $file) || in_array($file, $hidden_files) || !in_array(fix_strtolower($file_array['extension']), $ext) || ($filter != '' && $n_files > $file_number_limit_js && stripos($file, $filter) === false))
                    continue;

                $file_path = $current_path . $rfm_subfolder . $subdir . $file;
                //check if file have illegal caracter

                $filename = substr($file, 0, '-' . (strlen($file_array['extension']) + 1));

                if ($file != fix_filename($file, $transliteration)) {
                    $file1 = fix_filename($file, $transliteration);
                    $file_path1 = ($current_path . $rfm_subfolder . $subdir . $file1);
                    if (file_exists($file_path1)) {
                        $i = 1;
                        $info = pathinfo($file1);
                        while (file_exists($current_path . $rfm_subfolder . $subdir . $info['filename'] . ".[" . $i . "]." . $info['extension'])) {
                            $i++;
                        }
                        $file1 = $info['filename'] . ".[" . $i . "]." . $info['extension'];
                        $file_path1 = ($current_path . $rfm_subfolder . $subdir . $file1);
                    }

                    $filename = substr($file1, 0, '-' . (strlen($file_array['extension']) + 1));
                    rename_file($file_path, fix_filename($filename, $transliteration), $transliteration);
                    $file = $file1;
                    $file_array['extension'] = fix_filename($file_array['extension'], $transliteration);
                    $file_path = $file_path1;
                }

                $is_img = false;
                $is_video = false;
                $is_audio = false;
                $show_original = false;
                $show_original_mini = false;
                $mini_src = "";
                $src_thumb = "";
                $extension_lower = fix_strtolower($file_array['extension']);
                if (in_array($extension_lower, $ext_img)) {
                    $src = $base_url . $cur_dir . rawurlencode($file);
                    $mini_src = $src_thumb = $thumbs_path . $subdir . $file;
                    //add in thumbs folder if not exist
                    if (!file_exists($src_thumb)) {
                        try {
                            if (!create_img($file_path, $src_thumb, 122, 91)) {
                                $src_thumb = $mini_src = "";
                            } else {
                                new_thumbnails_creation($current_path . $rfm_subfolder . $subdir, $file_path, $file, $current_path, '', '', '', '', '', '', '', $fixed_image_creation, $fixed_path_from_filemanager, $fixed_image_creation_name_to_prepend, $fixed_image_creation_to_append, $fixed_image_creation_width, $fixed_image_creation_height, $fixed_image_creation_option);
                            }
                        } catch (Exception $e) {
                            $src_thumb = $mini_src = "";
                        }
                    }
                    $is_img = true;
                    //check if is smaller than thumb
                    list($img_width, $img_height, $img_type, $attr) = getimagesize($file_path);
                    if ($img_width < 122 && $img_height < 91) {
                        $src_thumb = $current_path . $rfm_subfolder . $subdir . $file;
                        $show_original = true;
                    }

                    if ($img_width < 45 && $img_height < 38) {
                        $mini_src = $current_path . $rfm_subfolder . $subdir . $file;
                        $show_original_mini = true;
                    }
                }
                $is_icon_thumb = false;
                $is_icon_thumb_mini = false;
                $no_thumb = false;
                if ($src_thumb == "") {
                    $no_thumb = true;

                    if (file_exists('modules/files/libraries/file_manager/img/' . $icon_theme . '/' . $extension_lower . ".jpg")) {
                        $src_thumb = 'modules/files/libraries/file_manager/img/' . $icon_theme . '/' . $extension_lower . ".jpg";
                    } else {
                        $src_thumb = "modules/files/libraries/file_manager/img/" . $icon_theme . "/default.jpg";
                    }
                    $is_icon_thumb = true;
                }
                if ($mini_src == "") {
                    $is_icon_thumb_mini = false;
                }

                $class_ext = 0;
                if (in_array($extension_lower, $ext_video)) {
                    $class_ext = 4;
                    $is_video = true;
                } elseif (in_array($extension_lower, $ext_img)) {
                    $class_ext = 2;
                } elseif (in_array($extension_lower, $ext_music)) {
                    $class_ext = 5;
                    $is_audio = true;
                } elseif (in_array($extension_lower, $ext_misc)) {
                    $class_ext = 3;
                } else {
                    $class_ext = 1;
                }
                if ((!($_GET['type'] == 1 && !$is_img) && !(($_GET['type'] == 3 && !$is_video) && ($_GET['type'] == 3 &&
                            !$is_audio))) && $class_ext > 0){
                ?>
                <li class="ff-item-type-<?php echo $class_ext; ?> file"
                    data-name="<?php echo $file; ?>" <?php if (($filter != '' && stripos($file, $filter) === false)) echo ' style="display:none;"'; ?>><?php
                    $file_prevent_rename = false;
                    $file_prevent_delete = false;
                    if (isset($filePermissions[$file])) {
                        if (isset($filePermissions[$file]['prevent_duplicate']) && $filePermissions[$file]['prevent_duplicate']) {
                            $files_prevent_duplicate[] = $file;
                        }
                        $file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
                        $file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
                    }
                    ?>
                    <figure data-name="<?php echo $file ?>" data-type="<?php if ($is_img) {
                        echo "img";
                    } else {
                        echo "file";
                    } ?>">
                        <a href="javascript:void('')" class="link" data-file="<?php echo $file; ?>"
                           data-function="<?php echo $apply; ?>">
                            <div class="img-precontainer">
                                <?php if ($is_icon_thumb) { ?>
                                    <div class="filetype"><?php echo $extension_lower ?></div><?php } ?>
                                <div class="img-container">
                                    <span></span>
                                    <img
                                        class="<?php echo $show_original ? "original" : "" ?><?php echo $is_icon_thumb ? " icon" : "" ?><?php echo $lazy_loading_enabled ? " lazy-loaded" : "" ?>" <?php echo $lazy_loading_enabled ? "data-original" : "src" ?>
                                    ="<?php echo '/' . $src_thumb; ?>">
                                </div>
                            </div>
                            <div class="img-precontainer-mini <?php if ($is_img) echo 'original-thumb' ?>">
                                <div
                                    class="filetype <?php echo $extension_lower ?> <?php if (in_array($extension_lower, $editable_text_file_exts)) echo 'edit-text-file-allowed' ?> <?php if (!$is_icon_thumb) {
                                        echo "hide";
                                    } ?>"><?php echo $extension_lower ?></div>
                                <div class="img-container-mini">
                                    <span></span>
                                    <?php if ($mini_src != "") { ?>
                                        <img
                                            class="<?php echo $show_original_mini ? "original" : "" ?><?php echo $is_icon_thumb_mini ? " icon" : "" ?><?php echo $lazy_loading_enabled ? " lazy-loaded" : "" ?>"
                                            <?php ($lazy_loading_enabled)
                                                ? "data-original" : "src" . "=\"/" . $mini_src . "\"" ?>>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($is_icon_thumb) { ?>
                                <div class="cover"></div>
                            <?php } ?>
                        </a>

                        <div class="box">
                            <h4 class="<?php if ($ellipsis_title_after_first_row) {
                                echo "ellipsis";
                            } ?>"><a href="javascript:void('')" class="link" data-file="<?php echo $file; ?>"
                                     data-function="<?php echo $apply; ?>">
                                    <?php echo $filename; ?></a></h4>
                        </div>
                        <input type="hidden" class="date" value="<?php echo $file_array['date']; ?>"/>
                        <input type="hidden" class="size" value="<?php echo $file_array['size'] ?>"/>
                        <input type="hidden" class="extension" value="<?php echo $extension_lower; ?>"/>
                        <input type="hidden" class="name" value="<?php echo $file_array['file_lcase']; ?>"/>

                        <div
                            class="file-date"><?php echo date(file_manager_trans('Date_type'), $file_array['date']) ?></div>
                        <div class="file-size"><?php echo makeSize($file_array['size']) ?></div>
                        <div class='img-dimension'><?php if ($is_img) {
                                echo $img_width . "x" . $img_height;
                            } ?></div>
                        <div class='file-extension'><?php echo $extension_lower; ?></div>
                        <figcaption>
                            <form action="<?php echo route('files_manager.post_force_download'); ?>" method="post"
                                  class="download-form"
                                  id="form<?php echo $nu; ?>">
                                <input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
                                <input type="hidden" name="path" value="<?php echo $rfm_subfolder . $subdir ?>"/>
                                <input type="hidden" class="name_download" name="name" value="<?php echo $file ?>"/>

                                <a title="<?php echo file_manager_trans('Download') ?>" class="tip-right"
                                   href="javascript:void('')" onclick="$('#form<?php echo $nu; ?>').submit();"><i
                                        class="icon-download"></i></a>
                                <?php if ($is_img && $src_thumb != "" && $extension_lower != "tiff" && $extension_lower != "tif") { ?>
                                    <a class="tip-right preview" title="<?php echo file_manager_trans('Preview') ?>"
                                       data-url="<?php echo $src; ?>" data-toggle="lightbox"
                                       href="#previewLightbox"><i class="icon-eye-open"></i></a>
                                <?php } elseif (($is_video || $is_audio) && in_array($extension_lower, $jplayer_ext)) { ?>
                                    <a class="tip-right modalAV <?php if ($is_audio) {
                                        echo "audio";
                                    } else {
                                        echo "video";
                                    } ?>"
                                       title="<?php echo file_manager_trans('Preview') ?>"
                                       data-url="<?php echo route('files_manager.system.post_ajax_call'); ?>?action=media_preview&title=<?php echo $filename; ?>&file=<?php echo $current_path . $rfm_subfolder . $subdir . $file; ?>"
                                       href="javascript:void('');"><i class=" icon-eye-open"></i></a>
                                <?php } elseif ($preview_text_files && in_array($extension_lower, $previewable_text_file_exts)) { ?>
                                    <a class="tip-right file-preview-btn"
                                       title="<?php echo file_manager_trans('Preview') ?>"
                                       data-url="<?php echo route('files_manager.system.get_ajax_call'); ?>?action=get_file&sub_action=preview&preview_mode=text&title=<?php echo $filename; ?>&file=<?php echo $current_path . $rfm_subfolder . $subdir . $file; ?>"
                                       href="javascript:void('');"><i class=" icon-eye-open"></i></a>
                                <?php } elseif ($googledoc_enabled && in_array($extension_lower, $googledoc_file_exts)) { ?>
                                    <a class="tip-right file-preview-btn"
                                       title="<?php echo file_manager_trans('Preview') ?>"
                                       data-url="<?php echo route('files_manager.system.get_ajax_call'); ?>?action=get_file&sub_action=preview&preview_mode=google&title=<?php echo $filename; ?>&file=<?php echo $current_path . $rfm_subfolder . $subdir . $file; ?>"
                                       href="docs.google.com;"><i class=" icon-eye-open"></i></a>

                                <?php } elseif ($viewerjs_enabled && in_array($extension_lower, $viewerjs_file_exts)) { ?>
                                    <a class="tip-right file-preview-btn"
                                       title="<?php echo file_manager_trans('Preview') ?>"
                                       data-url="<?php echo route('files_manager.system.get_ajax_call'); ?>?action=get_file&sub_action=preview&preview_mode=viewerjs&title=<?php echo $filename; ?>&file=<?php echo $current_path . $rfm_subfolder . $subdir . $file; ?>"
                                       href="docs.google.com;"><i class=" icon-eye-open"></i></a>

                                <?php } else { ?>
                                    <a class="preview disabled"><i class="icon-eye-open icon-white"></i></a>
                                <?php } ?>
                                <a href="javascript:void('')"
                                   class="tip-left edit-button rename-file-paths <?php if ($rename_files && !$file_prevent_rename) echo "rename-file"; ?>"
                                   title="<?php echo file_manager_trans('Rename') ?>"
                                   data-path="<?php echo $rfm_subfolder . $subdir . $file; ?>">
                                    <i class="icon-pencil <?php if (!$rename_files || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>

                                <a href="javascript:void('')"
                                   class="tip-left erase-button <?php if ($delete_files && !$file_prevent_delete) echo "delete-file"; ?>"
                                   title="<?php echo file_manager_trans('Erase') ?>"
                                   data-confirm="<?php echo file_manager_trans('Confirm_del'); ?>"
                                   data-path="<?php echo $rfm_subfolder . $subdir . $file; ?>">
                                    <i class="icon-trash <?php if (!$delete_files || $file_prevent_delete) echo 'icon-white'; ?>"></i>
                                </a>
                            </form>
                        </figcaption>
                    </figure>
                </li>
            <?php
            }
            }

            ?></div>
        </ul>
        <?php } ?>
    </div>
</div>
</div>
<script>
    var files_prevent_duplicate = new Array();
    <?php
    foreach ($files_prevent_duplicate as $key => $value): ?>
    files_prevent_duplicate[<?php echo $key;?>] = '<?php echo $value; ?>';
    <?php endforeach; ?>
</script>

<!-- lightbox div start -->
<div id="previewLightbox" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class='lightbox-content'>
        <img id="full-img" src="">
    </div>
</div>
<!-- lightbox div end -->

<!-- loading div start -->
<div id="loading_container" style="display:none;">
    <div id="loading"
         style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:1000"></div>
    <img id="loading_animation"
         src="<?php echo asset('modules/files/libraries/file_manager/img/storing_animation.gif'); ?>"
         alt="loading"
         style="z-index:1001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
</div>
<!-- loading div end -->

<!-- player div start -->
<div class="modal hide fade" id="previewAV">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo file_manager_trans('Preview'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid body-preview">
        </div>
    </div>

</div>
<!-- player div end -->
<img id='aviary_img' src='' class="hide"/>

<?php if ($lazy_loading_enabled) { ?>
    <script>
        $(function () {
            $(".lazy-loaded").lazyload({
                event: 'scrollstop'
            });
        });
    </script>
<?php } ?>

<!-- Begin Customize Condition -->
<input type="hidden" name="override_allow_extension"
       value="<?php echo $override_allow_extension; ?>"/>
<input type="hidden" id="variable_name" value="<?php echo $variable_name; ?>"/>
<input type="hidden" id="cms_lang" value="<?php echo $cms_lang; ?>"/>
<input type="hidden" id="multiple_file" value="<?php echo $multiple_file; ?>"/>
<input type="hidden" id="multiple_file_value" value=""/>
<!-- End Customize Condition -->

<input type="hidden" id="popup" value="<?php echo $popup; ?>"/>
<input type="hidden" id="crossdomain" value="<?php echo $crossdomain; ?>"/>
<input type="hidden" id="editor" value="<?php echo $editor; ?>"/>
<input type="hidden" id="view" value="<?php echo $view; ?>"/>
<input type="hidden" id="subdir" value="<?php echo $subdir; ?>"/>
<input type="hidden" id="field_id" value="<?php echo $field_id; ?>"/>
<input type="hidden" id="type_param" value="<?php echo $type_param; ?>"/>
<input type="hidden" id="cur_dir" value="<?php echo $cur_dir; ?>"/>
<input type="hidden" id="cur_dir_thumb" value="<?php echo $thumbs_path . $subdir; ?>"/>
<input type="hidden" id="insert_folder_name" value="<?php echo file_manager_trans('Insert_Folder_Name'); ?>"/>
<input type="hidden" id="new_folder" value="<?php echo file_manager_trans('New_Folder'); ?>"/>
<input type="hidden" id="ok" value="<?php echo file_manager_trans('OK'); ?>"/>
<input type="hidden" id="cancel" value="<?php echo file_manager_trans('Cancel'); ?>"/>
<input type="hidden" id="rename" value="<?php echo file_manager_trans('Rename'); ?>"/>
<input type="hidden" id="lang_duplicate" value="<?php echo file_manager_trans('Duplicate'); ?>"/>
<input type="hidden" id="duplicate" value="<?php if ($duplicate_files) echo 1; else echo 0; ?>"/>
<input type="hidden" id="base_url" value="<?php echo $base_url ?>"/>
<input type="hidden" id="base_url_true" value="<?php echo base_url(); ?>"/>
<input type="hidden" id="fldr_value" value="<?php echo $subdir; ?>"/>
<input type="hidden" id="sub_folder" value="<?php echo $rfm_subfolder; ?>"/>
<input type="hidden" id="return_relative_url" value="<?php echo $return_relative_url == true ? 1 : 0; ?>"/>
<input type="hidden" id="lazy_loading_file_number_threshold"
       value="<?php echo $lazy_loading_file_number_threshold ?>"/>
<input type="hidden" id="file_number_limit_js" value="<?php echo $file_number_limit_js; ?>"/>
<input type="hidden" id="sort_by" value="<?php echo $sort_by; ?>"/>
<input type="hidden" id="descending" value="<?php echo $descending ? 1 : 0; ?>"/>
<input type="hidden" id="current_url"
       value="<?php echo str_replace(array('&filter=' . $filter, '&sort_by=' . $sort_by, '&descending=' . intval($descending)), array(''), $base_url . $_SERVER['REQUEST_URI']); ?>"/>
<input type="hidden" id="lang_show_url" value="<?php echo file_manager_trans('Show_url'); ?>"/>
<input type="hidden" id="copy_cut_files_allowed" value="<?php if ($copy_cut_files) echo 1; else echo 0; ?>"/>
<input type="hidden" id="copy_cut_dirs_allowed" value="<?php if ($copy_cut_dirs) echo 1; else echo 0; ?>"/>
<input type="hidden" id="copy_cut_max_size" value="<?php echo $copy_cut_max_size; ?>"/>
<input type="hidden" id="copy_cut_max_count" value="<?php echo $copy_cut_max_count; ?>"/>
<input type="hidden" id="lang_copy" value="<?php echo file_manager_trans('Copy'); ?>"/>
<input type="hidden" id="lang_cut" value="<?php echo file_manager_trans('Cut'); ?>"/>
<input type="hidden" id="lang_paste" value="<?php echo file_manager_trans('Paste'); ?>"/>
<input type="hidden" id="lang_paste_here" value="<?php echo file_manager_trans('Paste_Here'); ?>"/>
<input type="hidden" id="lang_paste_confirm" value="<?php echo file_manager_trans('Paste_Confirm'); ?>"/>
<input type="hidden" id="lang_files" value="<?php echo file_manager_trans('Files'); ?>"/>
<input type="hidden" id="lang_folders" value="<?php echo file_manager_trans('Folders'); ?>"/>
<input type="hidden" id="lang_files_on_clipboard" value="<?php echo file_manager_trans('Files_ON_Clipboard'); ?>"/>
<input type="hidden" id="clipboard"
       value="<?php echo((isset($_SESSION['RF']['clipboard']['path']) && trim($_SESSION['RF']['clipboard']['path']) != null) ? 1 : 0); ?>"/>
<input type="hidden" id="lang_clear_clipboard_confirm"
       value="<?php echo file_manager_trans('Clear_Clipboard_Confirm'); ?>"/>
<input type="hidden" id="lang_file_permission" value="<?php echo file_manager_trans('File_Permission'); ?>"/>
<input type="hidden" id="chmod_files_allowed" value="<?php if ($chmod_files) echo 1; else echo 0; ?>"/>
<input type="hidden" id="chmod_dirs_allowed" value="<?php if ($chmod_dirs) echo 1; else echo 0; ?>"/>
<input type="hidden" id="lang_lang_change" value="<?php echo file_manager_trans('Lang_Change'); ?>"/>
<input type="hidden" id="edit_text_files_allowed" value="<?php if ($edit_text_files) echo 1; else echo 0; ?>"/>
<input type="hidden" id="lang_edit_file" value="<?php echo file_manager_trans('Edit_File'); ?>"/>
<input type="hidden" id="lang_new_file" value="<?php echo file_manager_trans('New_File'); ?>"/>
<input type="hidden" id="lang_filename" value="<?php echo file_manager_trans('Filename'); ?>"/>
<input type="hidden" id="lang_file_info" value="<?php echo fix_strtoupper(file_manager_trans('File_info')); ?>"/>
<input type="hidden" id="lang_edit_image" value="<?php echo file_manager_trans('Edit_image'); ?>"/>
<input type="hidden" id="lang_select" value="<?php echo file_manager_trans('Select'); ?>"/>
<input type="hidden" id="lang_extract" value="<?php echo file_manager_trans('Extract'); ?>"/>
<input type="hidden" id="transliteration" value="<?php echo $transliteration ? "true" : "false"; ?>"/>
<input type="hidden" id="convert_spaces" value="<?php echo $convert_spaces ? "true" : "false"; ?>"/>
<input type="hidden" id="replace_with" value="<?php echo $convert_spaces ? $replace_with : ""; ?>"/>

<?php
if ($aviary_active) {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) { ?>
        <script src="https://dme0ih8comzn4.cloudfront.net/imaging/v2/editor.js"></script>
    <?php }else{ ?>
        <script src="http://feather.aviary.com/imaging/v2/editor.js"></script>
    <?php }
} ?>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
<![endif]-->

<!-- Begin initial setup ajax -->
<script>
    $.ajaxSetup({
        data: {_token: '<?php echo csrf_token() ;?>'}
    });
</script>
<!-- End initial setup ajax -->

<script>
    var ext_img = new Array('<?php echo implode("','", $ext_img)?>');
    <?php
    // Override default allow extension
    if(empty($override_allow_extension)){
     ?>
    var allowed_ext = new Array('<?php echo implode("','", $ext)?>');
    <?php
    }else{
    ?>
    var allowed_ext = new Array('<?php echo implode("','", explode(',',$override_allow_extension)) ;?>');
    <?php
    }
    ?>
    var image_editor =<?php echo $aviary_active?"true":"false"; ?>;

    //dropzone config
    Dropzone.options.rfmDropzone = {
        dictInvalidFileType: "<?php echo file_manager_trans('Error_extension');?>",
        dictFileTooBig: "<?php echo file_manager_trans('Error_Upload'); ?>",
        dictResponseError: "SERVER ERROR",
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: <?php echo $MaxSizeUpload; ?>, // MB
        //url: "upload.php",
        url: "<?php echo route('files_manager.system.post_upload_file') ;?>",
        <?php if($apply!="apply_none"){ ?>
        init: function () {
            this.on("success", function (file, res) {
                file.previewElement.addEventListener("click", function () {
                    <?php echo $apply; ?>(res, '<?php echo $field_id; ?>');
                });
            });
        },
        <?php } ?>
        accept: function (file, done) {
            var extension = file.name.split('.').pop();
            extension = extension.toLowerCase();
            if ($.inArray(extension, allowed_ext) > -1) {
                done();
            }
            else {
                done("<?php echo file_manager_trans('Error_extension');?>");
            }
        }
    };
    if (image_editor) {
        var featherEditor = new Aviary.Feather({
            <?php
                foreach ($aviary_defaults_config as $aopt_key => $aopt_val) {
                    echo $aopt_key.": ".json_encode($aopt_val).",";
                } ?>
            onReady: function () {
                hide_animation();
            },
            onSaveButtonClicked: function (imageID) {

            },
            onSave: function (imageID, newURL) {

                show_animation();
                featherEditor.close();

                // TODO after create new file should autoload new file instead window refresh
                bootbox.prompt("Create new file or replace original?", 'Replace original', 'New file', function (e) {
                    var reloadWindow = false;
                    var oldFileName = $('#aviary_img').attr('data-name');
                    if (e !== null) {
                        if (e == oldFileName) {
                            oldFileName = oldFileName.split('.');
                            e = oldFileName[0] + '_' + new Date().getTime() + '.' + oldFileName[1];
                        }
                        reloadWindow = true;
                    } else {
                        e = oldFileName;
                    }
                    console.log(e);

                    $('#aviary_img').attr('data-name', e);

                    var img = document.getElementById(imageID);
                    img.src = newURL;
                    $.ajax({
                        type: "POST",
                        //url: "ajax_calls.php?action=save_img",
                        url: "<?php echo route('files_manager.system.post_ajax_call') ;?>?action=save_img",
                        data: {
                            url: newURL,
                            path: $('#sub_folder').val() + $('#fldr_value').val(),
                            name: $('#aviary_img').attr('data-name')
                        }
                    }).done(function (msg) {
//                        featherEditor.close();
                        d = new Date();
                        $("figure[data-name='" + $('#aviary_img').attr('data-name') + "']").find('img').each(function () {
                            $(this).attr('src', $(this).attr('src') + "?" + d.getTime());
                        });
                        $("figure[data-name='" + $('#aviary_img').attr('data-name') + "']").find('figcaption a.preview').each(function () {
                            $(this).attr('data-url', $(this).data('url') + "?" + d.getTime());
                        });
                        hide_animation();
                        if (reloadWindow === true) {
                            window.location.href = $("#refresh").attr("href") + "&" + (new Date).getTime()
                        }
                    });

                }, $('#aviary_img').attr('data-name'))

                return false;

            },
            onError: function (errorObj) {
                bootbox.alert(errorObj.message);
                hide_animation();
            }

        });
    }
</script>
<script src="<?php echo asset('modules/files/libraries/file_manager/js/include.js') ?>"></script>
<!-- Override include.js path -->
<script>
    executePath = '<?php echo $exePath ;?>';
    ajaxCallPath = {
        get_method: '<?php echo $ajaxCallGet;?>',
        post_method: '<?php echo $ajaxCallPost;?>'
    }
</script>
</body>
</html>

