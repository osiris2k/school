@extends('system.master')

@section('css')
    <link href="{{ asset('system/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/summernote/summernote-bs3.css')}}" rel="stylesheet">
    <link href="{{ asset('system/vendors/image-picker/image-picker.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/dropzone/basic.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/cropper/cropper.min.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet">
    <!-- Iphone checkbox style -->
    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="col-lg-12">
        <div class="ibox-title">
            <h3>{{$title}}</h3>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="form-horizontal">
                    @foreach($errors->all() as $error)
                        <p class="alert alert-danger">{{$error}}</p>
                    @endforeach
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Hotel Name</label>
                            <input type="text" name="name" class="form-control content_action"
                                   value="{{ $obj->name or old('name') }}" requied>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">URL Friendly</label>
                            <input type="text" name="slug" id="slug" class="txt-lable form-control menu_slug" required
                                   @if(isset($obj))
                                   value="{{$obj->slug}}"
                                    @endif
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Homepage</label>
                            <select name="homepage" class="form-control homepage_action chosen-select">
                                <option value="">- Select Homepage -</option>
                                @if(isset($hotel_homepage))
                                    <option value="{{$hotel_homepage->id}}" selected>{{ $hotel_homepage->name }}
                                        {{'('.strtoupper(str_replace('_',' ',$hotel_homepage->contentObject->name)).')'}}</option>
                                @endif
                                @foreach($contents as $content)
                                    @if(preg_match('/property_homepage/', $content->content_object_name))
                                        <option value="{{$content->id}}"> {{ $content->name }}
                                            {{'('.strtoupper(str_replace('_',' ',$content->content_object_name)).')'}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="checkbox-inline">
                                <input class="checkbox-iphone" id="content_active"
                                       @if(isset($obj))
                                       {{ ($obj->active == 1) ? 'checked' : '' }}
                                       value='{{$obj->active}}' @else  value='1' checked
                                       @endif name='active'
                                       type="checkbox"
                                       data-toggle="toggle" data-onstyle="primary"
                                       data-offstyle="danger" data-class="fast">Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="panel-options">
                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li class="@if($language->initial==1) active @endif">
                            <a data-toggle="tab" href="#tab-{{$language->name}}" class="content_tab_language"
                               onclick="showMap('{{$language->name}}')"
                               data-langauge="{{$language->name}}">{{strtoupper($language->name)}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="ibox-content">
                <div class="panel-body">
                    <div class="tab-content">
                        <?php
                        $maps = array();
                        $GLOBALS['maps'] = array();
                        ?>
                        @foreach($languages as $language)
                            <div id="tab-{{$language->name}}"
                                 class="tab-pane @if($language->initial==1) active @endif ">
                                {!! Form::open(array(
                                'url'=>$action,
                                'method'=>$method,
                                'id'=>'content_form_'.$language->name,
                                'onsubmit'=>'SubmitContent();return storeMultiForm(this);',
                                'class'=>'form-horizontal',
                                'files'=>true,
                                'data-submit_language'=>$language->name))
                                !!}
                                <input type="hidden" name="form_type" value="{{$form_type}}">
                                <input type="hidden" name="form_method" value="{{$method}}">
                                <input type="hidden" name="language_id" value="{{$language->id}}">
                                <input type="hidden" name='name' class='content_name'
                                       value="{{ $obj->name or old('name') }}">
                                <input type="hidden" name='slug' class='content_slug'
                                       value="{{ $obj->slug or old('slug') }}">
                                <input type="hidden" class='content_active' name='active'>
                                <input type="hidden" class='content_allow_cross_site' name='allow_cross_site'>
                                <input type="hidden" name='homepage' class='content_homepage'
                                       value="{{ $hotel_homepage->id or '' }}">
                                <input type="hidden" name='old_homepage' value="{{ $hotel_homepage->id or '' }}">
                                <input type="hidden" name='site_id' class='content_site_id'
                                       value="{{ $obj->site_id or old('site_id') }}">
                                <input type="hidden" name="language_name" id="language_{{$language->name}}"
                                       value="{{$language->name}}"/>
                                <input type="hidden" name="langauge_initial" id="language_initial_{{$language->name}}"
                                       value="{{$language->initial}}"/>
                                {{-- render --}}
                                @foreach($hotel_properties as $property)
                                    <?php $property['page'] = 'hotel'; ?>
                                    <?php $property['options_form'] = array('language_id' => $language->id); ?>
                                    @include('system.renders.'.$property->DataType->name,$property)
                                @endforeach
                                <div class="form-group action-zone">
                                    <div class="col-sm-12 text-center">
                                        <div class="btn-group">
                                            <a href="{{route('hotel.system.get_hotel_list')}}" class="btn btn-white">Cancel</a>
                                            <button class="btn btn-primary" name="SAVE_AND_STAY" value="SAVE_AND_STAY"
                                                    type="submit" onclick="changeBtState(this)">Save
                                            </button>
                                            <button class="btn btn-success" name="SAVE_AND_EXIT" value="SAVE_AND_EXIT"
                                                    type="submit" onclick="changeBtState(this)">Save&Exit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <!-- Iphone checkbox style I -->
    <script src="{{ asset('system/js/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('vendors/jquery-blockui/jquery.blockUI.js')}}"></script>
    <script>
        $(document).ready(function () {
            /**
             * Notification
             */
            var statusMessage = '{{session('PROCESS_STATUS')}}';
            if (statusMessage == 'SUCCESS') {
                toastr.success('{{session('PROCESS_STATUS_MESSAGE')}}');
            } else if (statusMessage == "FAILED") {
                toastr.error('{{session('PROCESS_STATUS_MESSAGE')}}');
            }
        });

        var btState = 'SAVE_AND_EXIT';
        function storeMultiForm(formEle) {

            $.blockUI({
                message: '<img src="{{asset('assets/images/loading_64.gif')}}">',
                css: {
                    border: 'none',
                    padding: '10px',
                    backgroundColor: 'transparent'
                }
            });
            tinymce.triggerSave();
            var allLanguageObjs = {!! $languages !!};
            var currentLang = $(formEle).attr('data-submit_language');
            var initialFormData = new Array;
            var initialLang = '';
            var otherFormData = new Array;
            $.each(allLanguageObjs, function (index, val) {
                if (val.initial == 1) {
                    initialLang = val.name;
                    initialFormData.push($('#content_form_' + val.name).serialize());
                } else {
                    otherFormData.push($('#content_form_' + val.name).serialize());
                }
            });

            $.ajax({
                url: $(formEle).attr('action'),
                method: $(formEle).find('input[name=form_method]').val(),
                dataType: 'JSON',
                data: {
                    _token: '{{csrf_token()}}',
                    form_type: $(formEle).find('input[name=form_type]').val(),
                    site_id: $('.site_id_action').val(),
                    content_object_id: $('input[name=content_object_id]').val(),
                    content_object_type_id: $('input[name=content_object_type_id]').val(),
                    name: $('.content_action').val(),
                    slug: $('.menu_slug').val(),
                    homepage: $('.homepage_action').val(),
                    old_homepage: '{{ $hotel_homepage->id or 0 }}',
                    allow_cross_site: $('.allow_cross_site_action').val(),
                    active: $('#content_active').val(),
                    language_id: $(formEle).find('input[name=language_id]').val(),
                    menu_group_id: $('.menu_id_action').val(),
                    bt_state: btState,
                    initial_form_data: initialFormData,
                    other_form_data: otherFormData
                }
            })
                    .done(function (data) {
                        $.unblockUI();
                        if (data.REDIRECT_URL == '') {
                            if (data.STATUS == 'SUCCESS') {
                                toastr.success(data.MESSAGE);
                            } else if (data.STATUS == "FAILED") {
                                if (typeof data.MESSAGE != "object") {
                                    toastr.error(data.MESSAGE);
                                } else {
                                    var objectMessage = '<ul>';
                                    $.each(data.MESSAGE, function (index, val) {
                                        objectMessage += '<li>' + val + '</li>';
                                    });
                                    objectMessage += '</ul>';

                                    toastr.error(objectMessage);
                                }
                            }
                        }
                        if (data.REDIRECT_URL != '' && data.REDIRECT_URL !== undefined) {
                            window.location.href = data.REDIRECT_URL;
                        }
                    });

            return false;
        }

        function changeBtState(btEle) {
            btState = $(btEle).val();
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#allow-cross-site,.checkbox-iphone').change(function () {

                $(this).prop('checked');

                if ($(this).is(":checked")) {

                    $(this).val(1);
                    $('.content_allow_cross_site').val(1);

                } else {

                    $(this).val(0);
                    $('.content_allow_cross_site').val(0);

                }
            });
        })
    </script>
    <!-- DROPZONE -->
    <script src="{{ asset('system/js/plugins/dropzone/dropzone.js')}}"></script>
    <script>
        $(document).ready(function () {

            Dropzone.options.myAwesomeDropzone = {

                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 100,
                maxFiles: 100,

                // Dropzone settings
                init: function () {
                    myDropzone = this;

                    this.element.querySelector("button[type=submit]").addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        myDropzone.processQueue();
                    });
                    this.on("sendingmultiple", function () {
                    });
                    this.on("successmultiple", function (files, response) {
                        refreshImage(function () {
                            close_select_image('#add-image', function () {
                                $('.image_picker_selector').prepend(add_html);
                                myDropzone.removeAllFiles(true);
                            });
                        });
                    });
                    this.on("errormultiple", function (files, response) {
                    });
                }
            }
        });
    </script>
    <!-- DROPZONE -->
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 500,
                onImageUpload: function (files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
                }
            });
            function sendFile(file, editor, welEditable) {
                data = new FormData();
                data.append("file", file);//You can append as many data as you want. Check mozilla docs for this
                $_token = "{{ csrf_token() }}";
                data.append('_token', $_token);
                $.ajax({
                    data: data,
                    type: "POST",
                    url: '{{url("system/uploadImagesAjax")}}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (url) {
                        editor.insertImage(welEditable, '{{ asset("") }}' + url);
                    }
                });
            }
        });
    </script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js">
    </script>
    <script type="text/javascript">
        function initialize(lat, lng, id) {
            setTimeout(
                    function () {
                        //do something special
                        var mapOptions = {
                            center: {lat: lat, lng: lng},
                            zoom: 8
                        };
                        var map = new google.maps.Map(document.getElementById(id),
                                mapOptions);

                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, lng),
                            map: map,
                            title: 'test',
                            draggable: true
                        });
                        google.maps.event.addListener(marker, 'drag', function (event) {
                            // console.debug('new position is '+event.latLng.lat()+' / '+event.latLng.lng());
                        });
                        google.maps.event.addListener(marker, 'dragend', function (event) {
                            console.debug('final position is ' + event.latLng.lat() + ' / ' + event.latLng.lng());
                            console.debug(marker);
                            // alert('tets');
                            my_map = marker.map.streetView.j;
                            my_map_id = $(my_map).attr('id');
                            gmnoprint = $('#tab-' + language + ' #' + my_map_id).parents('.form-group');
                            $(gmnoprint).find('input.latitude').val(event.latLng.lat());
                            $(gmnoprint).find('input.longitude').val(event.latLng.lng());
                        });
                    }, 300);
        }
        @foreach($languages as $language)
            @if($language->initial==1)
                @foreach($GLOBALS['maps'] as $index=>$map)
                    initialize({{$map['latitude']}}, {{$map['longitude']}}, 'map-canvas-{{$language->name}}-{{$index+1}}');
        @endforeach
        @endif
        @endforeach
        function showMap(tmp_language) {
            language = tmp_language;
            @foreach($GLOBALS['maps'] as $index=>$map)
                initialize({{$map['latitude']}}, {{$map['longitude']}}, 'map-canvas-' + language + '-{{$index+1}}');
            @endforeach
        }
        <?php
        unset($GLOBALS['maps']);
        ?>

        function SubmitContent() {
            $('.content_name').val($('.content_action').val());
            $('.content_homepage').val($('.homepage_action').val());
            $('.content_site_id').val($('.site_id_action').val());

            $('.content_active').val($('#content_active').val());
            $('.content_allow_cross_site').val($('.allow_cross_site_action').val());
            $('.content_menu_id').val($('.menu_id_action').val());
            $('.content_slug').val($('.menu_slug').val());
            setTimeout(function () {
                return true;
            }, 3000);
        }
    </script>

@stop