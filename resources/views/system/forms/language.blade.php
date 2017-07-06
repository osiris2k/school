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
@stop

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                @foreach($errors->all() as $error)
                    <p class="alert alert-danger">{{$error}}</p>
                @endforeach
                {!! Form::open(array('url'=>$action,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Language</label>
                        <input type="text" name="name" class="txt-label form-control"
                               value="{{ $obj->name or old('name') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Label</label>
                        <input type="text" name="label" class="txt-label form-control"
                               value="{{ $obj->label or old('label') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Locale</label>
                        <input type="text" name="locale" class="txt-label form-control"
                               value="{{ $obj->locale or old('locale') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Initial</label>
                        <select name="initial" class='form-control'>
                            <?php
                            $options = array('0' => 'No', '1' => 'Yes');
                            ?>
                            @foreach ($options as $key => $value)
                                <?php
                                $selected = '';
                                if (isset($obj)) {
                                    if ($obj->initial == $key) {
                                        $selected = 'selected';
                                    }
                                }
                                ?>
                                <option value="{{$key}}" {{$selected}}>{{$value}}</option>
                            @endforeach
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Enable</label>
                        <select name="status" class='form-control'>
                            <?php
                            $options = array('0' => 'No', '1' => 'Yes');
                            ?>
                            @foreach ($options as $key => $value)
                                <?php
                                $selected = '';
                                if (isset($obj)) {
                                    if ($obj->status == $key) {
                                        $selected = 'selected';
                                    }
                                }
                                ?>
                                <option value="{{$key}}" {{$selected}}>{{$value}}</option>
                            @endforeach
                            ?>
                        </select>
                    </div>
                </div>
                @if(isset($continents) && !empty($continents))
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Country</label>
                            <select id="countries" name="countries[]" data-placeholder="Choose a Country..."
                                    class="form-control chosen-select" multiple>
                                @foreach($continents as $continentKey => $continent)
                                    <optgroup label="{{$continentKey}}">
                                        @foreach($continent as $country)
                                            <option value="{{$country['country_id']}}"
                                                    {{ (isset($selected_country)
                                                    && in_array($country['country_id'],$selected_country))
                                                     ? 'selected' : '' }}
                                            >{{$country['country_name']}}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            <!--<div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Priority</label>
                        <input type="text" name="priority" class="txt-lable form-control"
                               value="{{ $obj->priority or old('priority') }}">
                    </div>
                </div>-->
                <input type="hidden" name="priority" class="txt-lable form-control"
                       value="{{ $obj->priority or old('priority') }}">
                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/languages/')}}" class="btn btn-white">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop