@extends('system.master')

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
                                    <option value="{{$cot_item->id}}"
                                            {{ ($obj->content_object_types_id == $cot_item->id)
                                            ? 'selected' : ''}}>{{$cot_item->content_object_types_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
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
            @foreach($content_properties as $content_property)
                <?php
                $json_options = json_decode($content_property->options);
                ?>
                <div class="ibox float-e-margins" data-type='content' data-id="{{$content_property->id}}">
                    <div class="ibox-title text-uppercase">
                        <i class="fa fa-font"></i><span class='show-label'>{{$content_property->name}}</span> <span
                                class="label label-default">{{$content_property->dataType->name}} </span>
                        {!! Form::open(array('url'=>'system/content-delete-properties/'.$content_property->id,'method'=>'delete','class'=>'inline-control form-ajax','onclick'=>'return(delete_type(this))'))!!}
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
                    {!! Form::open(array('url'=>'system/content-update-properties/'.$content_property->id,'method'=>'post'))!!}
                    <div class="ibox-content">
                        <div class="form-group">
                            <div class="col-md-6">
                                <input type="hidden" name="data_type_id" value="{{$content_property->data_type_id}}">
                                <label class="control-label">Label</label>
                                <input type="text" name="label" value="{{$content_property->name}}"
                                       class="txt-lable form-control" required onkeyup="setLable(this)">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Variable Name</label>
                                <input type="text" name="var" class="form-control"
                                       value="{{$content_property->variable_name}}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="control-label">
                                    <input type="radio" name="is_mandatory" value="1"
                                           {{{ $content_property->is_mandatory==1 ? "checked" : '' }}}> Mandatory
                                </label>
                                <label>
                                    <input type="radio" name="is_mandatory" value="0"
                                           {{{ $content_property->is_mandatory==0 ? "checked" : '' }}}> Optional
                                </label>
                            </div>
                        </div>
                        @foreach ($json_options as $json_option)
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label">{{$json_option->name}}</label>
                                    <?php
                                    $tmp = '';
                                    $i = 0;
                                    ?>
                                    @if($json_option->name=='list')
                                        @foreach($json_option->value as $value)
                                            <?php
                                            if ($i != sizeof($json_option->value) - 1)
                                                $tmp .= $value . ',';
                                            else
                                                $tmp .= $value;
                                            $i++;
                                            ?>
                                        @endforeach
                                    @else
                                        <?php $tmp = $json_option->value[0]; ?>
                                    @endif
                                    <input type="text" class="form-control" name="{{$json_option->name}}"
                                           value="{{$tmp}}">
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
                url = '{{ action('System\SystemController@renderType','', $attributes = array(), $secure = null) }}/' + type_id + '/' + type_name + '/content/{{$obj->id}}';
                $.get(url, function (data) {
                    console.log(data);
                    $('#new-type').append(data);
                    $('#new-type .ibox').fadeIn();
                });
            }
        });
    </script>
@stop