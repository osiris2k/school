@extends('system.master')

@section('css')
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>All Contents</h5>&nbsp;&nbsp;
                <!-- @if(isset($object_id))
                        <a href="{{url('system/contents/create/'.$object_id)}}"><span
                                class='badge badge-primary'>Create</span></a>
                @endif -->
                @if(isset($content_objects))
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Create Content
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu contents-dropdown bt-create-dropdown">
                            <div class="col-md-12 no-pad">
                                <select class="chosen-select-width form-control ignore-change"
                                        onchange="createContentRedirect(this)">
                                    <option data-redirect_url="" value="">Please select...</option>
                                    @foreach($content_objects as $contentObjIndex => $contentObjItem)
                                        <option data-redirect_url="{{url('/system/contents/create/'.$contentObjItem->id)}}">
                                            {{ucfirst(str_replace('_',' ',$contentObjItem->name))}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="table" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th width="80">No</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Parent name</th>
                        <th>Template</th>
                        @if(\App\Helpers\HotelHelper::isEnable())
                            <th>Hotel</th>
                        @endif
                        <th>Site</th>
                        <th>Active</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {
            data_i = 1;
            url = window.location.href;
            array = url.split('/');
            lastsegment = array[array.length - 1];

            var table = $('#table').DataTable({
                stateSave: true,
                "ajax": "{{URL::to('/system/contents/getAjax',[$content_object_type_id ,$content_object_id])}}",
                "columns": [
                    {"data": "id"},
                    {"data": "content_name"},
                    {"data": "slug"},
                    {"data": "parent_content"},
                    {"data": "content_object_name"},
                    @if(\App\Helpers\HotelHelper::isEnable())
                    {
                        "data": "hotel_name"
                    },
                    @endif
                    {"data": "site_name"},
                    {"data": "active"},
                    {"data": "id"},
                ],
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return data_i++;
                        },
                        "targets": 0
                    },
                    {
                        "sClass": "text-center",
                        "render": function (data, type, row) {
                            var returnStr = '';
                            if (data.length == 0) {
                                returnStr = '-';
                            } else {
                                for (var i = 0; i < data.length; i++) {
                                    returnStr += data[i].name;

                                    if (data[(i + 1)]) {
                                        returnStr += ' , ';
                                    }
                                }
                            }

                            return returnStr;
                        },
                        "targets": 3
                    }, {
                        "sClass": "text-center",
                        "render": function (data, type, row) {
                            return data;
                        },
                        "targets": 4
                    },
                    {
                        "sClass": "text-center",
                        "render": function (data, type, row) {
                            return data;
                        },
                        "targets": 5
                    },
                    {
                        "sClass": "text-center",
                        "render": function (data, type, row) {
                            if (data != 0) {
                                return "<span class='badge badge-primary'><i class='fa fa-check'></i></span>";
                            } else {
                                return "<span class='badge badge-danger'><i class='fa fa-close'></i></span>";
                            }
                        },
                        "targets": {{(\App\Helpers\HotelHelper::isEnable()) ? 7 : 6}}
                    },
                    {
                        "render": function (data, type, row) {
                            return "<a href='{{url('system/contents/')}}/" + data + "/edit'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;<form action='{{url('system/contents/')}}/" + data + "' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
                        },
                        "targets": {{(\App\Helpers\HotelHelper::isEnable()) ? 8 : 7}}
                    },
                ]
            });

        });

        function createContentRedirect(ele) {
            if ($(ele).val() == '') {
                return false;
            }
            var redirectUrl = $(ele).children('option:selected').attr('data-redirect_url');

            window.open(redirectUrl, '_self');
        }

        $(document).ready(function () {
            $('.contents-dropdown').click(function (event) {
                event.stopPropagation();
            });
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
    </script>
@stop