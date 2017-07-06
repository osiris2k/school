@extends('system.master')
@section('css')
    <link href="{{ asset('system/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
@endsection
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
                {!! Form::open(array('url'=>$action,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
                    <div class="form-group">
                        <div class="col-md-6">
                            {!!  Form::file('file', array('class' => 'form-control')) !!}
                        </div>
                    </div>



                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/translations')}}" class="btn btn-white">Cancel</a>
                        <button class="btn btn-primary" type="submit">Import</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('system/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('.checkbox-iphone').change(function () {

                $(this).prop('checked');

                if ($(this).is(":checked")) {

                    $(this).val(1);

                } else {
                    $(this).val(0);

                }
            });
        })
    </script>
@endsection