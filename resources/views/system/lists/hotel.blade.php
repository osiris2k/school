@extends('system.master')

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>All Hotel</h5>

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
                        <th>Active</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
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
            var table = $('#table').DataTable({
                stateSave: true,
                "ajax": "{{route('hotel.system.get_get_hotel_data')}}",
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "active"},
                    {"data": "created_at"},
                    {"data": "created_at"},
                    {"data": "id"},
                ],
                "columnDefs": [
                    {
                        "sClass": "text-center",
                        "render": function (data, type, row) {
                            if (data != 0) {
                                return "<span class='badge badge-primary'><i class='fa fa-check'></i></span>";
                            } else {
                                return "<span class='badge badge-danger'><i class='fa fa-close'></i></span>";
                            }
                        },
                        "targets": 2
                    },
                    {

                        "render": function (data, type, row) {
                            return "<a href='{{route('hotel.system.get_edit_hotel')}}/" + data + "/edit'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;<form action='{{route('hotel.system.delete_update_hotel')}}/" + data + "' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
                        },
                        "targets": 5
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