@extends('system.master')

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>All Menu Groups</h5>&nbsp;&nbsp;<a href="{{url('system/menu-groups/create/')}}"><span
                            class='badge badge-primary'>Create</span></a>
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
                        <th class="col-md-1">Site</th>
                        <th class="col-md-1">Edit</th>
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
            var table = $('#table').DataTable({
                stateSave: true,
                "ajax": "{{URL::to('/system/menu-groups/getAjax')}}",
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {
                        "data": "site_name", "sClass":"text-center"
                    },
                    {
                        "data": "id"
                    },
                ],
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return "<a href='{{url('system/menu-groups/')}}/" + data + "'><i class='fa fa-bars'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='{{url('system/menu-groups/')}}/" + data + "/edit'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;<form action='{{url('system/menu-groups')}}/" + data + "' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
                        },
                        "targets": 3
                    },
                    {
                        "render": function (data, type, row) {
                            return data_i++;
                        },
                        "targets": 0
                    },
                ]
            });
        });
    </script>
@stop