@extends('system::layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Standalone Version
                        <small class="m-l-sm">manage your media...</small>
                    </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <iframe width="100%" height="550" frameborder="0"
                            src="{{ route('files.system.get_file_manager_lib') }}/?type=0">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
