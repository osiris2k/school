@extends('system.master')
@section('css')
    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')

    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$title_form}}</h5>
            </div>
            <div class="ibox-content">
                @foreach($errors->all() as $error)
                    <p class="alert alert-danger">{{$error}}</p>
                @endforeach
                {!! Form::open(array('url'=>$url,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Form Name</label>
                        <input type="text" name="name" class="txt-lable form-control" required
                               @if(isset($obj))
                               value="{{$obj->name}}"
                                @endif
                        >
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Sites</label>

                        <div class="row">
                            <div class="col-md-12">
                                <select name="site_id" data-placeholder="Choose..." class="chosen-select col-xs-12"
                                        required>
                                    <option value=''>Choose...</option>
                                    @foreach($sites as $site)
                                        <option @if(isset($obj)) {{($site->id == $obj->site_id) ? 'selected' : ''}} @endif value="{{$site->id}}">{{$site->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- @if(\App\Helpers\HotelHelper::isEnable())
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Hotel</label>
                            <select name="hotel_id" id="hotel_id" class="form-control" required>
                                <option value="">Choose Hotel</option>
                                @foreach($hotels as $hotelIndex => $hotel)
                                    @if((isset($obj) && ($obj->hotel()->first() && $obj->hotel()->first()->pivot->hotel_id == $hotel->id))
                                    || ($hotelIndex == 0))
                                        <option value="{{$hotel->id}}" selected>{{$hotel->name}}</option>
                                    @else
                                        <option value="{{$hotel->id}}">{{ $hotel->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif -->
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Email</label>
                        <input type="text" name="email" class="txt-lable form-control" required
                               @if(isset($obj))
                               value="{{$obj->email}}"
                                @endif
                        >
                    </div>
                </div>
                {{--  <div class="form-group">
                      <div class="col-md-6">
                          <label class="control-label">Sending report to email</label>
                          <input type="checkbox" name="is_send" value="1" class="txt-lable form-control"
                                 @if($obj->is_send==1)
                                 checked
                                  @endif
                          >
                      </div>
                  </div>--}}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-iphone"
                                   @if(isset($obj) && $obj->is_send == 1) checked
                                   value='1' @else  value='0'
                                   @endif name='is_send'
                                   class='allow_cross_site_action' type="checkbox"
                                   data-toggle="toggle" data-onstyle="primary"
                                   data-offstyle="danger" data-class="fast">Sending report to email
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-iphone"
                                   @if(isset($obj) && $obj->user_notification == 1) checked
                                   value='1' @else  value='0'
                                   @endif name='user_notification'
                                   class='allow_cross_site_action' type="checkbox"
                                   data-toggle="toggle" data-onstyle="primary"
                                   data-offstyle="danger" data-class="fast">User notification
                        </label>
                    </div>
                </div>
                {{--<div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">User notification</label>
                        <input type="checkbox" name="user_notification" value="1" class="txt-lable form-control"
                               @if(isset($obj))
                               @if($obj->user_notification == 1)
                               checked
                                @endif
                                @endif
                        >
                    </div>
                </div>--}}
                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/form-objects')}}" class="btn btn-white">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$title}}</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Select type</label>

                    <div class="col-sm-6">
                        <select class="form-control m-b text-uppercase" name="type" id='type'>
                            <option value="">Choose type</option>
                            @foreach ($dataTypes as $dataType)
                                <option value="{{ $dataType->id }}">{{ $dataType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="btn btn-primary" id="add-type">Add</div>
                </div>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div id="new-type">

        </div>
        <div id="box-type">
            @foreach($form_properties as $form_property)
                <?php
                $json_options = json_decode($form_property->options);
                ?>
                <div class="ibox float-e-margins" data-type='form' data-id="{{$form_property->id}}">
                    <div class="ibox-title text-uppercase">
                        <i class="fa fa-font"></i><span class='show-label'>{{$form_property->name}}</span> <span
                                class="label label-default">{{$form_property->dataType->name}} </span>
                        {!! Form::open(array('url'=>'system/form-delete-properties/'.$form_property->id,'method'=>'delete','class'=>'inline-control form-ajax','onclick'=>'return(delete_type(this))'))!!}
                        <a class="delete-link">
                            <i class="fa fa-trash-o"></i>
                        </a>
                        {!! Form::close() !!}
                        <div class="ibox-tools">
                            <a class="collapse-link fl">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    {!! Form::open(array('url'=>'system/form-update-properties/'.$form_property->id,'method'=>'post'))!!}
                    <div class="ibox-content">
                        <div class="form-group">
                            <div class="col-md-6">
                                <input type="hidden" name="data_type_id" value="{{$form_property->data_type_id}}">
                                <label class="control-label">Label</label>
                                <input type="text" name="label" value="{{$form_property->name}}"
                                       class="txt-lable form-control" required onkeyup="setLable(this)">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Variable Name</label>
                                <input type="text" name="var" class="form-control"
                                       value="{{$form_property->variable_name}}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="control-label">
                                    <input type="radio" name="is_mandatory" value="1"
                                           {{{ $form_property->is_mandatory==1 ? "checked" : '' }}}> Mandatory
                                </label>
                                <label>
                                    <input type="radio" name="is_mandatory" value="0"
                                           {{{ $form_property->is_mandatory==0 ? "checked" : '' }}}> Optional
                                </label>
                            </div>
                        </div>
                        @foreach ($json_options as $json_option)
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label">{{$json_option->name}}</label>
                                    <input type="text" class="form-control" name="{{$json_option->name}}"
                                           value="{{$json_option->value[0]}}">
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <div class="col-md-12" style="margin-top:20px;">
                                <input type="submit" class="btn btn-primary" value="Save">
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            @endforeach
        </div>
    </div>
@stop

@section('script')
    <script src="{{ asset('system/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('system/js/plugins/dropzone/dropzone.js')}}"></script>
    <script>
        $(document).ready(function () {
            WinMove();
            $("#add-type").click(function () {
                var type_name = $('#type option:selected').text();
                var type_id = $('#type option:selected').val();
                renderType(type_id, type_name);
            });
            function renderType(type_id, type_name) {
                url = '{{ action('System\SystemController@renderType','', $attributes = array(), $secure = null) }}/' + type_id + '/' + type_name + '/form/{{$obj->id}}';
                $.get(url, function (data) {
                    console.log(data);
                    $('#new-type').append(data);
                    $('#new-type .ibox').fadeIn();
                });
            }
        });
    </script>
    <script src="{{ asset('system/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('.checkbox-iphone').change(function () {

                $(this).prop('checked');

                if ($(this).is(":checked")) {

                    $(this).val(1);

                } else {
                    $(this).val(0);
                }
            });
        })
    </script>
@stop