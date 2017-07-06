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

    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins" id="tab-{{$language}}">
            <div class="ibox-title">
                <h5>All form elements
                    <small>set obcject</small>
                </h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @foreach($errors->all() as $error)
                    <p class="alert alert-danger">{{$error}}</p>
                @endforeach
                {!! Form::open(array('url'=>$action,'method'=>$method,'onsubmit'=>'javascript:has_action=false','class'=>'form-horizontal','files'=>true))!!}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Site Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $obj->name or old('name') }}"
                               requied>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-iphone"
                                   @if(isset($obj) && $obj->main_site == 1) checked
                                   value='1' @else  value='0'
                                   @endif name='main_site'
                                   class='checkbox-iphone-action' type="checkbox"
                                   data-toggle="toggle" data-onstyle="primary"
                                   data-offstyle="danger" data-class="fast">Main site
                        </label>
                    </div>
                </div>
                {{--<div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Active</label>
                        <input type="checkbox" name="active" @if(isset($obj) && $obj->active) checked
                               @endif class="form-control" value="1">
                    </div>
                </div>--}}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-iphone"
                                   @if(isset($obj) && $obj->active == 1) checked
                                   value='1' @else  value='0'
                                   @endif name='active'
                                   type="checkbox"
                                   data-toggle="toggle" data-onstyle="primary"
                                   data-offstyle="danger" data-class="fast">Active
                        </label>
                    </div>
                </div>
                <?php
                if (isset($site_languages)) {
                    $active_langues = array();
                    foreach ($site_languages as $l) {
                        array_push($active_langues, $l->language_id);
                    }
                }
                ?>

                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Site Languages</label>
                        @foreach($languages as $l)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="languages[]"
                                           @if(isset($active_langues) && !empty($active_langues) && in_array($l->id,$active_langues))
                                           checked
                                           @endif

                                           value="{{$l->id}}"
                                           @if($language == $l->name && $method != 'post') disabled @endif >
                                    {{$l->name}}
                                </label>
                            </div>
                            @if($language == $l->name) <input name="languages[]" type='hidden'
                                                              value="{{$l->id}}"> @endif
                        @endforeach

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Site Parent</label>
                        <select name="parent_site" class="form-control">
                            <option value="">Parent</option>
                            @foreach($sites as $site)
                                @if(isset($obj) ? $obj->parent_site : '' ==$site->id)
                                    <option value="{{$site->id}}" selected>{{$site->name}}</option>
                                @else
                                    <option value="{{$site->id}}">{{$site->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Template</label>
                        <select name="template" class="form-control" required>
                            <option value="">Template</option>
                            @foreach($directories as $directory)
                                @if((isset($obj) ? $obj->template : '') == $directory)
                                    <option value="{{$directory}}" selected>{{$directory}}</option>
                                @else
                                    <option value="{{$directory}}">{{$directory}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- render --}}
                <?php
                $maps = array();
                $GLOBALS['maps'] = array();
                ?>
                @foreach($site_properties as $site_property)
                    <?php
                    $site_property['page'] = 'site';
                    $site_property['options_form'] = '';
                    ?>
                    <div class="form-holder">
                        <div class="col-xs-12">
                            @include('system.renders.'.$site_property->DataType->name,$site_property)
                        </div>
                    </div>
                @endforeach
                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/sites')}}" class="btn btn-white">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ asset('system/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('.checkbox-iphone').change(function () {

                   // $(this).prop('checked');

                    if ($(this).is(":checked")) {

                        $(this).val(1);
                        console.log( $(this).val());

                    } else {
                        $(this).val(0);
                    }
                });

        });
    </script>

    <script src="{{ asset('system/vendors/image-picker/image-picker.js')}}"></script>
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
            var mapOptions = {
                center: {lat: lat, lng: lng},
                zoom: 8
            };
            var map = new google.maps.Map(document.getElementById(id),
                    mapOptions);

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lng),
                map: map,
                title: 'test',
                draggable: true
            });
            google.maps.event.addListener(marker, 'drag', function (event) {
                console.debug('new position is ' + event.latLng.lat() + ' / ' + event.latLng.lng());
            });
            google.maps.event.addListener(marker, 'dragend', function (event) {
                console.debug('final position is ' + event.latLng.lat() + ' / ' + event.latLng.lng());
                console.debug(marker.map.streetView.V);
                my_map = marker.map.streetView.V;
                my_map_id = $(my_map).attr('id');
                gmnoprint = $('#' + my_map_id + ' .gmnoprint').parents('.form-group');
                $(gmnoprint).find('input.latitude').val(event.latLng.lat());
                $(gmnoprint).find('input.longitude').val(event.latLng.lng());
            });
        }
        @foreach($GLOBALS['maps'] as $index=>$map)
            initialize({{$map['latitude']}}, {{$map['longitude']}}, 'map-canvas-{{$index+1}}');
        @endforeach
        <?php
        unset($GLOBALS['maps']);
        ?>
    </script>

@stop