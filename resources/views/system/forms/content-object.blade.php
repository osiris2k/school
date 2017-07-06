@extends('system.master')


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
                        <label class="control-label">Content Name</label>
                        <input type="text" name="name" class="txt-lable form-control" required
                               @if(isset($obj))
                               value="{{$obj->name}}"
                                @endif
                        >
                    </div>
                </div>
                @if(isset($content_object_types_array))
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Content Object Type</label>
                            <select class="form-control" name="content_object_types" id="content_object_types">
                                @foreach($content_object_types_array as $cot_item)
                                    <option value="{{$cot_item->id}}">{{$cot_item->content_object_types_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Generate SEO Meta</label>
                        <select class="form-control" name="generate_seo_meta" id="generate_seo_meta">
                            <option value="NO">NO</option>
                            <option value="YES" selected>YES</option>
                        </select>
                    </div>
                </div>
                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/content-objects')}}" class="btn btn-white">Cancel</a>
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
@stop