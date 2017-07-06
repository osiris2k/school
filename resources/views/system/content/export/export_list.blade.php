@extends('system.master')

@section('css')
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{$pageTitle}}</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="col-md-12">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Filter Export</label>

                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>Sites</label>
                                        <select class="form-control chosen-select ignore-change" id="site_filter"
                                                name="site_filter">
                                            <option value="">All Sites</option>
                                            @foreach($siteLists as $siteItem)
                                                <option value="{{$siteItem->id}}">{{$siteItem->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>Templates</label>
                                        <select class="form-control chosen-select ignore-change"
                                                id="template_filter"
                                                name="template_filter[]" multiple>
                                            <option value="ALL" selected>All Templates</option>
                                            @foreach($contentObjLists as $contentObjItem)
                                                <option value="{{$contentObjItem->id}}">
                                                    {{ucfirst(str_replace('_',' ',$contentObjItem->name))}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-offset-2 col-sm-10">
                                    <div class="form-group">
                                        <button class="btn btn-info" type="button" id="export_filter">
                                            <i class="fa fa-filter"></i> Filter Export
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                    <div class="col-md-12 text-center">
                        <button class="btn btn-primary" type="button" id="export_bt">
                            <i class="fa fa-download"></i>&nbsp;Export Translations
                        </button>
                        <div class="hr-line-dashed"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tableDataTable" class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Parent name</th>
                                    <th>Template</th>
                                    <th>Site</th>
                                    <th>Active</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{asset('vendors/jquery-redirect/jquery.redirect.js')}}"></script>
    <script>
        $(document).ready(function () {
            // TODO block ui

            var tableIndex = 0;
            var dataTableObj = $('#tableDataTable').DataTable({
                        stateSave: true,
                        "ajax": {
                            url: '{{route('content.system.export.post_get_export_content_list_data')}}',
                            method: 'POST',
                            dataType: 'JSON',
                            data: function (d) {
                                d._token = '{{csrf_token()}}';
                                d.site_filter = $('#site_filter').val();
                                d.template_filter = $('#template_filter').val();
                            }
                        },
                        "columnDefs": [
                            {
                                "data": "id",
                                "render": function (data, type, row) {
                                    return tableIndex += 1;
                                },
                                "targets": 0
                            },
                            {
                                "data": "name",
                                "render": function (data, type, row) {
                                    return data;
                                },
                                "targets": 1
                            },
                            {
                                "data": "slug",
                                "sClass": "text-center",
                                "render": function (data, type, row) {
                                    return data;
                                },
                                "targets": 2
                            },
                            {
                                "data": "content_parent_name",
                                "render": function (data, type, row) {
                                    return (data != null) ? data : "-";
                                },
                                "targets": 3
                            },
                            {
                                "data": "content_object_name",
                                "sClass": "text-center",
                                "render": function (data, type, row) {
                                    return data.charAt(0).toUpperCase() + data.slice(1).replace(/_/g, ' ').replace(/_/g, ' ');
                                },
                                "targets": 4
                            },
                            {
                                "data": "site_name",
                                "sClass": "text-center",
                                "render": function (data, type, row) {
                                    return data;
                                },
                                "targets": 5
                            },
                            {
                                "data": "active",
                                "render": function (data, type, row) {
                                    if (data != 0) {
                                        return "<span class='badge badge-primary'><i class='fa fa-check'></i></span>";
                                    } else {
                                        return "<span class='badge badge-danger'><i class='fa fa-close'></i></span>";
                                    }
                                },
                                "targets": 6
                            },
                            {
                                "data": "content_object_id"
                            },
                            {
                                "data": "site_id"
                            }
                        ]
                    })
                    .on('preXhr.dt', function (e, settings, data) {
                        tableIndex = 0;
                    });

            $('#export_filter').click(function (event) {
                dataTableObj.ajax.reload();
            })

            $('#export_bt').click(function () {
                $.redirect('{{route('content.system.export.post_export_content_translations_file')}}', {
                    _token: '{{csrf_token()}}',
                    site_filter: $('#site_filter').val(),
                    template_filter: $('#template_filter').val()
                }, 'POST', '_blank');
            });
        });


    </script>
@stop