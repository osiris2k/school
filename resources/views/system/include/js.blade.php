<!-- Mainly scripts -->
<script src="{{ asset('system/js/jquery-2.1.1.js')}}"></script>
<script src="{{ asset('system/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('system/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Jquery Form -->
<script src={{asset('vendors/jquery.form/jquery.form.min.js')}}></script>

<!-- PLUpload -->
<script type="text/javascript" src="{{asset('vendors/plupload/js/plupload.full.min.js')}}"></script>
<script type="text/javascript"
        src="{{asset('vendors/plupload/js/jquery.ui.plupload/jquery.ui.plupload.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $("li[data-tag-menu='{{{$tag_first_menu or ''}}}'").addClass('active');
    });
</script>
<script src="{{ asset('system/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{ asset('system/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<!-- Flot -->
<script src="{{ asset('system/js/plugins/flot/jquery.flot.js')}}"></script>
<script src="{{ asset('system/js/plugins/flot/jquery.flot.tooltip.min.js')}}"></script>
<script src="{{ asset('system/js/plugins/flot/jquery.flot.spline.js')}}"></script>
<script src="{{ asset('system/js/plugins/flot/jquery.flot.resize.js')}}"></script>
<script src="{{ asset('system/js/plugins/flot/jquery.flot.pie.js')}}"></script>

<!-- Peity -->
<script src="{{ asset('system/js/plugins/peity/jquery.peity.min.js')}}"></script>
<script src="{{ asset('system/js/demo/peity-demo.js')}}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('system/js/inspinia.js')}}"></script>
<script src="{{ asset('system/js/plugins/pace/pace.min.js')}}"></script>

<script src="{{ asset('system/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{ asset('system/js/plugins/datetimepicker/moment.js')}}"></script>
<script src="{{ asset('system/js/plugins/datetimepicker/bootstrap-datetimepicker.js')}}"></script>

<!-- GITTER -->
<script src="{{ asset('system/js/plugins/gritter/jquery.gritter.min.js')}}"></script>

<!-- Sparkline -->
<script src="{{ asset('system/js/plugins/sparkline/jquery.sparkline.min.js')}}"></script>

<!-- Sparkline demo data  -->
<script src="{{ asset('system/js/demo/sparkline-demo.js')}}"></script>

<!-- ChartJS-->
<script src="{{ asset('system/js/plugins/chartJs/Chart.min.js')}}"></script>
<!-- Toastr -->
<script src="{{ asset('system/js/plugins/toastr/toastr.min.js')}}"></script>

<!-- summernote -->
<script src="{{ asset('system/js/plugins/summernote/summernote.min.js')}}"></script>

<!-- cropper -->
<script src="{{ asset('system/js/plugins/cropper/cropper.min.js')}}"></script>

<!-- Chosen -->
<script src="{{ asset('system/js/plugins/chosen/chosen.jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('system/vendors/chosen-order/dist/chosen.order.jquery.min.js')}}"></script>
<script src="{{ asset('system/vendors/jquery-chosen-sortable-master/jquery-chosen-sortable.js')}}"></script>

<!-- Tiny MCE -->
<script src="{{ asset('system/vendors/tinymce/js/tinymce/tinymce.min.js') }}"></script>

<!-- FancyBox -->
<script type="text/javascript" src="{{asset('vendors/fancybox/source/jquery.fancybox.pack.js?v=2.1.5')}}"></script>

<script src="{{ asset('system/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{ asset('system/vendors/DataTables-1.10.7/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('system/js/custom.js')}}"></script>
<script src="{{asset('system/scripts/notification.js')}}"></script>
<script src="{{ asset('system/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ asset('system/js/plugins/tooltip-picture/main.js')}}"></script>
<script>

    language = '{{CmsHelper::getInitLanguage()->name}}';
    // Config for multiple select
    var config = config = {
        '.chosen-select': {width: "100%"},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "100%"}
    };
    var gallery_id = 0;
    var add_html = '<li ><div class="col-xs-2 add-image" onclick="add_image()"><i class="fa fa-plus"></i></div></li>';
    var options_images = '';
    var has_action = false;
    $_token = "{{ csrf_token() }}";

    $(document).ready(function () {

        $(':input').not(".ignore-change").change(function () {
            has_action = true;
        });
        $('.btn[type="submit"]').click(function () {
            has_action = false;
        });
        orderImage();
        // image.cropper section, it's about crop image
        var add_image = 1;
        $image = $(".image-crop > img");
        $("#zoomIn").click(function () {
            $image.cropper("zoom", 0.1);
        });
        $("#zoomOut").click(function () {
            $image.cropper("zoom", -0.1);
        });

        $("#rotateLeft").click(function () {
            $image.cropper("rotate", 45);
        });

        $("#rotateRight").click(function () {
            $image.cropper("rotate", -45);
        });
        // default datetime and date picker
        $('.datetime-picker').datetimepicker({
            format: 'MM/DD/YYYY HH:mm:ss'
        });
        $('.date-picker').datetimepicker({
            format: 'MM/DD/YYYY'
        });
        $('.datepicker-group .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });
        $(".btn-close").click(function () {
            $('#tab-' + language + ' #txt-' + gallery_id).html('');
            if ($(this).data('target') == '#add-image') {
                refreshImage(function () {
                    close_select_image('#add-image', function () {
                        $('.image_picker_selector').prepend(add_html); // js prepend html
                        myDropzone.removeAllFiles(true);  // reset all file in upload section
                    });
                });
            } else {
                close_select_image($(this).data('target'));
            }
        });
        $('#choose-image').click(function () {
            var id = gallery_id.replace('gallery-', '');
            json_new_select = JSON.parse(new_select_image);
            orderImage();
            // input save select images
            console.log('get select images: ' + json_new_select);
            getRatioImages(id, function (rule) {
                //send obj and rule to check image rule
                index_new_select = 0;
                image_rule = rule;
                processCropper(json_new_select);
            });
        });
        $('.select-image').click(function () {
            gallery = $(this).parents('.gallery');
            gallery_id = $(gallery).attr('id');
            old_select_image = $('#tab-' + language + ' #txt-' + gallery_id).val();
            same_gallery = true;
            if (gallery_id != $('#select-image').data('gallery')) {
                same_gallery = false;
            }
            $('#select-image').attr('data-gallery', gallery_id);
            $('#spinner').show();
            refreshImage(function () {
                $('.image_picker_selector').prepend(add_html);
                $('#select-image').show(0, function () {
                    $('#select-image').css('top', $(window).scrollTop() + "px");
                    $('#spinner').hide();
                });
            }, same_gallery);
            $('#select-image,#spinner,#cropper').css('top', $('body').scrollTop());
            $('body').css('overflow', 'hidden');
        });
        // delete file update hidden fiels delete_xxx
        $('.delete-file').click(function () {
            $(this).parents('.form-group').find('.status-delete').val(1);
            $(this).parents('.form-group').find('.file-name').css('opacity', 0.5);
        });

        // setting multiple select
        for (var selector in config) {
            $(selector).chosen(config[selector]).chosenSortable();
        }

//        if($(".color-picker-container").length > 0){
//            generateColorPicker();
//        }
    });


    function processCropper() {
        url_image = json_new_select[index_new_select];
        if (index_new_select != $(json_new_select).size()) {
            showCropper(url_image, image_rule)
        } else {
            // end loop select image
            $('#spinner').hide();
            var target = $('#select-image .btn-close').data('target');
            close_select_image(target, function () {
                renderSelectImage();
            });
        }
    }

    function showCropper(url_image, image_rule) {
        console.log('=====showCropper=====');
        var cropper_ratio = image_rule.ratio;
        cropper_width = image_rule.width;
        cropper_height = image_rule.height;
        console.log(cropper_ratio);
        // loop image for check size with image_rule
        var img = new Image();
        img.src = "{{url()}}/" + url_image;
        $($image).cropper({
            aspectRatio: cropper_ratio,
            preview: ".img-preview",
            done: function (data) {
                $('#dataHeight').html(Math.round(data.height));
                $('#dataWidth').html(Math.round(data.width));
                // Output the result data for cropping image.
            }
        });
        img.onload = function () {
            // get size image
            var image_height = img.height;
            var image_width = img.width;
            var image_ratio = image_width / image_height;
            console.log("=====image_ratio=====");
            console.log("image_ratio: " + image_ratio);
            if (cropper_width == "*" && cropper_height == "*") {
                console.log('=====no rule=====');
                index_new_select++;
                processCropper();
            } else if (image_height == cropper_height && image_width == cropper_width) {  // same size
                console.log('=====same size=====');
                index_new_select++;
                processCropper();
            } else if (image_ratio == cropper_ratio) { // same ratio
                console.log('=====same ratio=====');
                saveCroppper(img.src);
            } else { // new crop
                if (cropper_width == image_width) {
                    processCropper();
                    index_new_select++;
                } else if (cropper_height == image_height) {
                    processCropper();
                    index_new_select++;
                } else {
                    console.log('=====new crop=====');
                    console.log(img.src);
                    $('#spinner').hide();
                    $('#cropper').show();
                    $('#cropper').css('top', $(window).scrollTop() + "px");
                    console.log(' M ' + img)
                    $image.cropper("reset", true).cropper("replace", img.src);
                }
            }
        }
    }

    function saveCroppper(image_url) {
        $('#cropper').hide();
        $('#spinner').css('top', $(window).scrollTop() + "px");
        $('#spinner').show();
        console.log('=====saveCroppper=====');
        var url_crop_image = "{{url('system/uploadCropImage')}}";
        $.post(url_crop_image,
                {file: image_url, _token: $_token, width: cropper_width, height: cropper_height}
                , function (response) {
                    console.log(response);
                    json_new_select[index_new_select] = response.new_path;
                    // console.log(index_new_select);
                    index_new_select++;
                    processCropper();
                });
    }

    function getRatioImages(id, callback) {
        console.log('=====getRatioImages=====');
        var type = '{{Request::segment(2)}}';
        rule_url = '{{url("system/get-rule")}}/' + id + '/' + type;
        data = '';
        $.getJSON(rule_url, function (data) {
            data = data;
            console.log(data.image_rule);
            callback(data);
        });
    }

    function renderSelectImage() {
        console.log("====renderSelectImage====");
        // render new select image
        var group = $('#' + gallery_id + ' input').first().attr('name') + '_';
        console.log('group ' + group);
        console.log('gallery_id ' + gallery_id);
        var start_new_image_html = '<div class="old-image"><div class="row"><div class="col-xs-2"><img src="{{url()}}/';
        var end_new_image_html = '"><div class="badge-image"><span class="badge badge-danger"  onclick="delete_image(this)">delete</span></div></div>' +
                '<div class="col-xs-10">' +
                '<div class="col-xs-12"><label class="control-label bold">Title</label><input type="text" class="form-control" name="' + group + 'title[]" value=""></div>' +
                '<div class="col-xs-12"><label class="control-label bold">Alt</label><input type="text" class="form-control" name="' + group + 'alt[]" value=""></div>' +
                '<div class="col-xs-12"><label class="control-label bold">Caption</label><input type="text" class="form-control" name="' + group + 'caption[]" value=""></div>' +
                '<div class="col-xs-12"><label class="control-label bold">Class</label><input type="text" class="form-control" name="' + group + 'class[]" value=""></div>' +
                '</div></div></div>';
        new_image_html = '';

        $(json_new_select).each(function (index, value) {
            new_image_html += start_new_image_html + value + end_new_image_html;
        });

        // set input hidden field
        var select_images = $('#tab-' + language + ' #txt-' + gallery_id).val();
        var select_images_check = true;
        // check old value in hidden field
        try {
            var json = JSON.parse(select_images);
        } catch (e) {
            select_images_check = false;
        }
        if (select_images_check) {
            hidden_value = json.concat(json_new_select);
        } else {
            hidden_value = json_new_select;
        }
        if (limit == 1) {
            var length = $('#tab-' + language + ' #' + gallery_id + ' .show-image .old-image').length;
            if (length > 0) {
                $('#tab-' + language + ' #' + gallery_id + ' .show-image .old-image').remove();
                hidden_value.splice(0, 1);
            }
            $('#tab-' + language + ' #' + gallery_id + ' .show-image .order-image').append(new_image_html);
        } else {
            $('#tab-' + language + ' #' + gallery_id + ' .show-image .order-image').append(new_image_html);
        }
        hidden_value = JSON.stringify(hidden_value);
        console.log("hidden_value: " + hidden_value);
        console.log("id: " + '#tab-' + language + ' #txt-' + gallery_id);
        $('#tab-' + language + ' #txt-' + gallery_id).val(hidden_value);
    }

    function refreshImage(callback, same_gallery) {
        $.getJSON("{{url('system/get-media')}}", function (data) {
            options_html = '';
            $(data).each(function (index) {
                // if(same_gallery){
                //     if(options_images.indexOf(data[index]["path"])==-1){
                //         options_html += setOption(data[index]["path"],'size: '+data[index]["width"]+"x"+data[index]["height"]);
                //     }
                // }else{
                //     options_html += setOption(data[index]["path"],'size: '+data[index]["width"]+"x"+data[index]["height"]);
                // }
                options_html += setOption(data[index]["path"], 'size: ' + data[index]["width"] + "x" + data[index]["height"]);
            })
            // if(same_gallery){
            // $(".image-picker").append(options_html);
            // }else{
            $(".image-picker").html(options_html);
            // }
            if ($('#' + gallery_id).data('limit') != "") {
                limit = $('#' + gallery_id).data('limit')
                option = {
                    clicked: function () {
                        var val = $(this).val();
                        var val = JSON.stringify(val);
                        new_select_image = val;
                    },
                    show_label: true,
                    limit: $('#' + gallery_id).data('limit')
                }
            } else {
                limit = 0;
                option = {
                    clicked: function () {
                        var val = $(this).val();
                        var val = JSON.stringify(val);
                        new_select_image = val;
                    },
                    show_label: true,
                }
            }
            $(".image-picker").imagepicker(option);
            $('.image_picker_selector').css('height', $(window).height() - 150 + 'px');
            callback();
        });
    }

    function setOption(value, label) {
        option = '<option data-img-src="{{url('')}}/' + value + '" value="' + value + '">' + label + '</option>';
        options_images += option;
        return option;
    }

    function close_select_image(target, callback) {
        if (target == "#select-image" || target == "#cropper") {
            $('body').css('overflow', 'auto');
        }
        $('#spinner').hide();
        $(target).fadeOut();
        callback();
    }

    function add_image(obj) {
        var s = $(window).scrollTop();
        var addImgEl = $('#add-image');

        addImgEl.css('top', s);
        addImgEl.fadeIn();
    }

    function delete_image(obj) {
        if (!$(obj).hasClass('disabled')) {
            // opacity delete item
            console.log('==== delete_image ====');
            var delete_url = $(obj).parents('.old-image').find('img').attr('src');
            // delete item in hidden field
            delete_url = delete_url.split('/');
            delete_url = delete_url[$(delete_url).size() - 3] + '/' + delete_url[$(delete_url).size() - 2] + '/' + delete_url[$(delete_url).size() - 1];
            console.log('delete_url: ' + delete_url);
            $(obj).parents('.old-image').css('opacity', '0.5');
            var hidden_value = $(obj).parents('.gallery').find("input[type='hidden']").val();
            hidden_value = JSON.parse(hidden_value);
            hidden_value.splice(hidden_value.indexOf(delete_url), 1);
            // temp_hidden_value = hidden_value;
            console.log("hidden_value: " + hidden_value);
            $(obj).parents('.gallery').find("input[type='hidden']").val(JSON.stringify(hidden_value));
            $(obj).addClass('disabled');
        }
    }

    function delete_type(obj) {
        check = confirm('Do you want to delete this element?');
        if (check) {
            var ajax = $(obj).hasClass('form-ajax');
            if (ajax) {
                $.ajax({
                    url: $(obj).attr('action'),
                    method: 'post',
                    data: {_method: 'delete', _token: $_token}
                }).done(function (data) {
                    json = JSON.parse(data);
                    if (json.code == 200) {
                        var ibox = $(obj).closest('.cms_show');
                        if (json.data.delete != '') {
                            ibox = $(obj).closest(json.data.delete);
                            $(ibox).remove();
                        }
                        toastr.success(json.message);
                    } else {
                        toastr.warning(json.message);
                    }
                });
                return false;
            } else {
                $(obj).append('<input type="text" name="_token" value="' + $_token + '"/>');
                $(obj).append('<input type="text" name="_method" value="delete"/>');
                $(obj).submit();
            }
        }
    }

    function form_ajax() {
        has_action = false;
    }

    function save_type(obj) {
        check = confirm('Do you want to save this element?');
        if (check) {
            $.ajax({
                url: $(obj).attr('action'),
                method: 'post',
                data: {_method: 'put', _token: $_token,}
            }).done(function (data) {
                json = JSON.parse(data);
                if (json.code == 200) {
                    var ibox = $(obj).closest('.ibox');
                    $(ibox).remove();
                } else {
                    alert('can\'t update this element');
                }
            });
        }
        return false;
    }
    function orderImage() {
        $('.order-image').sortable({
            stop: function (event, ui) {
                images = $(this).find('.old-image');
                arry_images = [];
                $(images).each(function (index, value) {
                    arry_image = [];
                    $(images[index]).find('.order').val(index);
                    console.log($(images[index]).find('img').attr('src'));
                    domain = '{{URL::to('')}}/';
                    image_url = $(images[index]).find('img').attr('src');
                    arry_image['image'] = image_url.replace(domain, '');
                    arry_images.push(arry_image['image']);
                });
                json_images = JSON.stringify(arry_images);
                $(images).parents('.gallery').find('input[type="hidden"]').val(json_images);
            }
        });
    }

    function deleteType(obj) {
        var ibox = $(obj).closest('.ibox');
        $(ibox).remove();
    }

    // check reload
    window.onbeforeunload = function (evt) {
        if (has_action) {
            return "It's changed some data";
        }
    }


    /**
     * New Richtext Editor
     */
    // TODO cleancode
    // TODO integrate more config (ref from Baruna and Goco)
    // TODO CSS RTL
    $(document).ready(function () {
        tinymce.init({
            selector: "textarea.new_richtext_editor",
            schema: 'html5',
            theme: "modern",
            body_class: 'timy_mce_editor',
            height: 350,
            relative_urls: false,
            document_base_url: '/',
            menubar: 'edit insert view format table tools',
            removed_menuitems: '',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'template paste textcolor colorpicker textpattern imagetools responsivefilemanager filemanager'
            ],
            toolbar1: 'insertfile undo redo | styleselect template | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            toolbar2: 'fontsizeselect forecolor backcolor | table | link image media responsivefilemanager filemanager | print preview code',
            image_advtab: true,

            external_filemanager_path: "/assets/global/plugin/tinymce/js/tinymce/plugins/responsivefilemanager/",
            filemanager_title: "Media Manager",
            external_plugins: {
                "filemanager": "/system/vendors/tinymce/js/tinymce/plugins/responsivefilemanager/plugin.min.js"
            },
            getLibUrl: '{{ route('files_manager.system.get_file_manager_lib') }}',
            subFolderVal: '{{config('upload.WSYIWYG_ROOT_DIR')}}',
            content_css: '{{asset('templates/mestro/css/mestro.css')}}',
            invalid_elements: '',
            extended_valid_elements: 'script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]',
            fontsize_formats: '8px 10px 12px 14px 18px 24px 36px',
            templates: [{
                title: 'Experience description',
                description: '',
                url: '{{route('wysiwyg.system.get_template', 'experiences')}}'
            },{
                title: 'Team description',
                description: '',
                url: '{{route('wysiwyg.system.get_template', 'team')}}'
            },{
                title: 'Private Events item description',
                description: '',
                url: '{{route('wysiwyg.system.get_template', 'private_events_item')}}'
            },{
                title: 'Service time',
                description: '',
                url: '{{route('wysiwyg.system.get_template', 'service_time')}}'
            }]
        });

        /**
         * FancyBox Init
         */
        $(".fancybox").fancybox({
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });

        /**
         * PLUpload Init
         */

        var plUploadEleArray = $('.pl-uploader');

        if (plUploadEleArray.length > 0) {
            for (elIndex = 0; elIndex < plUploadEleArray.length; elIndex++) {
                var plUploadEle = plUploadEleArray[elIndex];
                var plLangName = $(plUploadEle).attr('data-language_name');
                var plVariableName = $(plUploadEle).attr('data-variable_name');
                var plAllowSize = $(plUploadEle).attr('data-max_file_size');
                var plAllowExt = $(plUploadEle).attr('data-allow_mime_type');
                var PlUploaderId = 'pl-uploader-' + plVariableName + '-' + plLangName;
//                var plContainerId = 'pl-container-' + plVariableName + '-' + plLangName;
//                var plBrowseBtId = 'pl-pickfiles-' + plVariableName + '-' + plLangName;
//                var plUploadBtId = 'pl-uploadfiles-' + plVariableName + '-' + plLangName;
//                var PlFileListId = 'pl-filelists-' + plVariableName + '-' + plLangName;
//                var PlConsoleId = 'pl-console-' + plVariableName + '-' + plLangName;

                $('#' + PlUploaderId).plupload({
                    runtimes: 'html5,flash,silverlight,html4',

                    url: '{{route('upload_file.system.post_chunk_upload_file')}}',
                    multipart_params: {
                        _token: '{{csrf_token()}}'
                    },

                    // Maximum file size
                    max_file_size: plAllowSize,

                    chunk_size: '1mb',

                    // Specify what files to browse for
                    filters: [
                        {title: "Allow files extenstions", extensions: plAllowExt},
                    ],

                    // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
                    dragdrop: true,

                    // Views to activate
                    views: {
                        list: true,
                        thumbs: true, // Show thumbs
                        active: 'thumbs'
                    },

                    // Flash settings
                    flash_swf_url: '{{asset('vendors/plupload/js/Moxie.swf')}}',

                    // Silverlight settings
                    silverlight_xap_url: '{{asset('vendors/plupload/js/Moxie.xap')}}',
                });
                // After upload complete remove all uploaded file
                $('#' + PlUploaderId).on('complete', function (event, args) {
                    args.up.splice();
                });
            }
        }

    });

    /**
     * Remove unwated images
     */
    function removeUnwantedImg() {
        $('div.waiting_to_del').remove();
    }


</script>