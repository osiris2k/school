@extends('system.master')

@section('css')
    <link href="{{ asset('system/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Export Form</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form role="form" class="form-inline col-md-offset-3 col-md-9">
                    <div class="form-group datepicker-group">
                        <label class="font-noraml">Date range</label>
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control ignore-change"
                                   id="filter-date-start"
                                   name="filter-date-start"/>
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control ignore-change"
                                   id="filter-date-end"
                                   name="filter-date-end"/>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-default inline-filter-bt" type="reset" id="reset_filter">
                        Reset
                    </button>
                    <button class="btn btn-outline btn-primary inline-filter-bt" type="button" id="export_filter">
                        <i class="fa fa-filter"></i> Filter Export
                    </button>
                    <button class="btn btn-outline btn-success inline-filter-bt" type="button"
                            id="submission-export-bt">
                        <i class='fa fa-download'></i> Export
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Forms {{ ucfirst(str_replace('_', ' ', $object_name)) }}</h5>&nbsp;&nbsp;
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
                        @foreach($form_properties as $property)
                            <th>{{$property->name}}</th>
                        @endforeach
                        <th>Submission date</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{asset('vendors/jquery-redirect/jquery.redirect.js')}}"></script>

    <script>
        $(document).ready(function () {
            var filterDateStart = $("#filter-date-start");
            var filterDateEnd = $("#filter-date-end");
            data_i = 1;
            url = window.location.href;
            array = url.split('/');
            lastsegment = array[array.length - 1];
            file_path = "http://" + array[2] + "/";
                    <?php
                    $i = 0;
                    $position = -1;
                    ?>

            var table = $('#table').DataTable({
                        stateSave: true,
                        "ajax": {
                            url: "{{URL::to('/system/forms/getAjax/')}}/" + lastsegment,
                            method: 'GET',
                            dataType: 'JSON',
                            data: function (d) {
                                d.filter_date_start = $('#filter-date-start').val();
                                d.filter_date_end = $('#filter-date-end').val();
                            }
                        },
                        "columns": [
                                @foreach($form_properties as $property)
                            {
                                "data": "{{$property->variable_name}}"
                            },
                                @if($property->data_type_id == 19)
                                <?php $position = $i; ?>
                                @endif
                                <?php $i++; ?>
                                @endforeach
                            {
                                "data": "submission_date"
                            },
                            {"data": "id"},

                        ],
                        "order": [[ {{sizeOf($form_properties)}}, "desc"]],
                        "columnDefs": [
                            {
                                "render": function (data, type, row) {
                                    return "<form action='{{url('system/delete/submission')}}/" + data + "' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
                                },
                                "targets": {{sizeOf($form_properties)+1}}
                            }
                            @if($position != -1)
                            , {
                                "targets": {{$position}},
                                "render": function (data, type, row) {
                                    return "<a  href='" + file_path + data + "' target='_bank'><span class='badge badge-primary'>Download</span></a>";
                                }
                            }
                            @endif
                        ],
                    })
                    .on('preXhr.dt', function (e, settings, data) {
                        tableIndex = 0;
                    });

            $('#export_filter').click(function (event) {
                table.ajax.reload();
            });

            $("#reset_filter").click(function () {
                filterDateStart.datepicker("setDate", null)
                        .attr('value', '');
                filterDateEnd.datepicker("setDate", null)
                        .attr('value', '');

                table.ajax.reload();
            });

            $("#submission-export-bt").click(function () {
                $.redirect('{{URL::to('system/export/'.$object_id)}}?filter_date_start=' + $('#filter-date-start').val() + '&filter_date_end=' + $('#filter-date-end').val(), null, 'GET', '_blank');
            });

        });
    </script>
@stop