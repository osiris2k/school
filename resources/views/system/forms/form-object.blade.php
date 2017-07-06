@extends('system.master')
@section('css')
    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$title}}</h5>
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
                               @else
                               value="{{old('name')}}"
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
                                >
                                    <option value=''>Choose...</option>
                                    @foreach($sites as $site)
                                        <option @if(isset($obj)) {{($site->id == $obj->site_id) ? 'selected' : ''}} @endif value="{{$site->id}}">{{$site->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--@if(\App\Helpers\HotelHelper::isEnable())
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
                        <input type="text" name="email" class="txt-lable form-control"
                               @if(isset($obj))
                               value="{{$obj->email}}"
                               @else
                               value="{{old('email')}}"
                                @endif
                        >
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-iphone"
                                   @if(isset($obj) && $obj->is_send == 1) checked
                                   value='1' @else  value='0'
                                   @endif name='is_send'
                                   class='checkbox-iphone-action' type="checkbox"
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
                                   class='checkbox-iphone-action' type="checkbox"
                                   data-toggle="toggle" data-onstyle="primary"
                                   data-offstyle="danger" data-class="fast">User notification
                        </label>
                    </div>
                </div>
                {{--<div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Sending report to email</label>
                        <input type="checkbox" name="is_send" value="1" class="txt-lable form-control"
                               @if(isset($obj))
                               @if($obj->is_send==1)
                               checked
                                @endif
                                @endif
                        >
                    </div>
                </div>
                <div class="form-group">
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
                url = '{{ action('System\SystemController@renderType','', $attributes = array(), $secure = null) }}/' + type_id + '/' + type_name + '/content';
                $.get(url, function (data) {
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