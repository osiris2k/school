@extends('system.master')

@section('css')
    <link rel="stylesheet" href="{{asset('vendors/bootstrap-fileinput/css/fileinput.min.css')}}">
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
                        <form id="import_form" name="import_form"
                              action="{{route('content.system.import.post_import_content_file')}}"
                              method="POST"
                              class="form-horizontal"
                              enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select Import File</label>

                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <input class="ignore-change" type="file" id="import_file" name="import_file"/>
                                        <span class="help-block m-b-none">Allow File Extenstions : .xls , .xlsx</span>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{asset('vendors/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{asset('vendors/jquery-blockui/jquery.blockUI.js')}}"></script>
    <script>
        // TODO Compiled common function to create module
        $(document).ready(function () {
            var statusMessage = '{{session('STATUS_MESSAGE')}}';
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "400",
                "timeOut": "3000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            if (statusMessage == 'SUCCESS') {
                toastr.success('Import successful.');
            } else if (statusMessage == "FAILED") {
                toastr.error('Import failed, please try again.');
            }

            $('#import_form').on('submit', function (event) {
                $.blockUI({
                    message: '<h3><img src="{{asset('assets/images/circle-preloading.gif')}}"> Importing...</h3>',
                    css: {
                        border: 'none',
                        padding: '10px',
                    }
                });
            });

            $("#import_file").fileinput({
                showPreview: false,
                allowedFileExtensions: ["xls", "xlsx"]
            });
        });
    </script>
@stop