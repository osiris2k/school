@extends('system::layouts.master')
@section('js')
@endsection
@section('scripts')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Modal Version
                        <small class="m-l-sm">manage your media...</small>
                    </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Some Image</label>

                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="fieldID4">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary"
                                            data-toggle="modal" data-target="#file_manager_modal" >Select!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal inmodal fade" id="file_manager_modal" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                    </button>
                                    <h4 class="modal-title">Modal title</h4>
                                    <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and
                                        typesetting industry.
                                    </small>
                                </div>
                                <div class="modal-body" style="min-width:700px;">
                                    <iframe width="100%" height="550" frameborder="0"
                                            src="{{ route('files.system.get_file_manager_lib') }}/?type=0&field_id=fieldID4">
                                    </iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="hr-line-dashed"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function responsive_filemanager_callback(field_id) {
            console.log(field_id);
            var url=jQuery('#'+field_id).val();
            alert('update '+field_id+" with "+url);
            //your code
        }
    </script>
@endsection
