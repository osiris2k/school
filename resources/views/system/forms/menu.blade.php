@extends('system.master')
@section('css')
    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                @if(\Session::get('message'))
                    <p class="alert alert-success">{{\Session::get('message')}}</p>
                @endif
                {!! \Session::get('response') !!}
                @foreach($errors->all() as $error)
                    <p class="alert alert-danger">{{$error}}</p>
                @endforeach
                {!! Form::open(array('url'=>$url,'method'=>$method,'id'=>'menu-form','class'=>'form-horizontal','files'=>true))!!}
                <input type="hidden" id="language_id" name="language_id">
                <input type="hidden" id="menu_language_title" name="menu_language_title">
                <input type="hidden" id="menu_language_detail" name="menu_language_detail">
                <input type="hidden" id="save_type" name="save_type">
                <div class="tab-content">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Menu Name</label>
                            <input type="text" name="menu_title" class="txt-lable form-control" required
                                   @if(isset($obj))
                                   value="{{$obj->menu_title}}"
                                    @endif
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Menu Group</label>
                            <select name="menu_group_id" class="chosen-select-width form-control ignore-change" required
                                    id="menu_group">
                                <option value="">Please select...</option>
                                @foreach($groups as $group)
                                    @if((isset($obj) ? $obj->menu_group_id : '') ==$group->id)
                                        <option value="{{$group->id}}" selected>{{$group->site->name}}
                                            : {{$group->name}}</option>
                                    @else
                                        <option value="{{$group->id}}">{{$group->site->name}}: {{$group->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Parent Menu</label>

                            <select name="parent_menu_id" class="chosen-select-width form-control ignore-change">
                                <option value="">Please select...</option>
                                @if(!empty($menus))
                                    @foreach($menus as $menu)
                                        @if((isset($obj) ? $obj->parent_menu_id : '') ==$menu->id)
                                            @if($menu->menuGroup!=null)
                                                <option value="{{$menu->id}}"
                                                        selected>{{$menu->menuGroup->site->name.': '.$menu->menu_title}}</option>@endif
                                        @else
                                            @if($menu->menuGroup!=null)
                                                <option value="{{$menu->id}}">{{$menu->menuGroup->site->name.': '.$menu->menu_title}}</option>@endif
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Content</label>
                            <select name="content_id" class="chosen-select-width form-control ignore-change" required>
                                <option value="">Please select...</option>
                                @if(!empty($menus))
                                    @foreach($contents as $content)
                                        @if((isset($obj) ? $obj->content_id : '') ==$content->id)
                                            <option value="{{$content->id}}"
                                                    selected>{{$content->site->name.': '.$content->name}}</option>
                                        @else
                                            <option value="{{$content->id}}">{{$content->site->name.': '.$content->name}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">External link</label>
                            <input type="text" name="external_link" class="txt-lable form-control"
                                   @if(isset($obj))
                                   value="{{$obj->external_link}}"
                                    @endif
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Image source</label>
                            <input type="text" name="image_src" class="txt-lable form-control"
                                   @if(isset($obj))
                                   value="{{$obj->image_src}}"
                                    @endif
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Custom Class</label>
                            <input type="text" name="class" class="txt-lable form-control"
                                   @if(isset($obj))
                                   value="{{$obj->class}}"
                                    @endif
                            >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Target: </label>
                            <input type="radio" name='target' value='_self'
                                   @if(isset($obj))
                                   {{ $obj->target == '_self' ? 'checked' : '' }}
                                   @else
                                   checked
                                    @endif
                            > Self
                            <input type="radio" name='target'
                                   value='_blank'
                            @if(isset($obj))
                                {{ $obj->target == '_blank' ? 'checked' : '' }}
                                    @endif
                            > New Windows
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="checkbox-inline">
                                <input class="checkbox-iphone" id="menu_active"
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
                {!! Form::close() !!}

                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        @foreach($languages as $language)

                            <li class="@if($language->initial==1) active @endif">
                                <a data-toggle="tab" href="#tab-{{$language->name}}"
                                   onclick="showMap('{{$language->name}}')">{{strtoupper($language->name)}}</a>
                            </li>

                        @endforeach
                    </ul>
                </div>
                <div class="tab-content">
                    @foreach($languages as $language)

                        <div id="tab-{{$language->name}}" class="tab-pane @if($language->initial==1) active @endif">
                            <div class="form-group action-zone">
                                <div class="col-md-6">
                                    <label class="control-label"><b>Menu Label</b></label>
                                    <input type="hidden" id="tmp_language_id" class="tmplanguageid"
                                           value="{{$language->id}}">
                                    <input type="text" id="tmp_menu_language_title"
                                           class="tmpmenulanguagetitle txt-lable form-control" required
                                           @if(isset($language_menu[$language->id]->label))
                                           value="{{$language_menu[$language->id]->label}}"
                                            @endif
                                    >
                                    <br>
                                </div>
                            </div>

                            <div class="form-group action-zone">
                                <div class="col-md-12">
                                    <label class="control-label"><b>Menu Detail</b></label>
                                    <input type="text" id="tmp_menu_language_detail"
                                           class="tmpmenulanguagedetail txt-lable form-control" required
                                           @if(isset($language_menu[$language->id]->detail))
                                           value="{{$language_menu[$language->id]->detail}}"
                                            @endif
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group action-zone" style="margin-top:40px;">
                                        <div class="col-md-6">
                                            <a href="{{url('system/menus')}}" class="btn btn-white">Cancel</a>
                                            <button type="submit" class="btn btn-primary" value="save">Save</button>
                                            <button type="submit" class="btn btn-primary" value="exit">Save and exit</button>
                                            <a href="{{url('system/menus/create')}}" class="btn"> <i
                                                        class="fa fa-plus"></i> Create new menu</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <!-- Iphone checkbox style I -->
    <script src="{{ asset('system/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#menu_active.checkbox-iphone').change(function () {

                $(this).prop('checked');

                if ($(this).is(":checked")) {

                    $(this).val(1);
                } else {

                    $(this).val(0);
                }
            });
        })
    </script>
    <script>
        $('.btn-primary').click(function () {
            $('#language_id').val($('.tab-pane.active .tmplanguageid').val());
            $('#menu_language_title').val($('.tab-pane.active .tmpmenulanguagetitle').val());
            $('#menu_language_detail').val($('.tab-pane.active .tmpmenulanguagedetail').val());
            $('#save_type').val($(this).val());
            $('#menu-form').submit();
        });
    </script>
@stop