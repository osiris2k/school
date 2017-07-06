<head>
    @include('system.include.meta')

    <link href="{{ asset('system/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('system/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <!-- FancyBox-->
    <link rel="stylesheet" href="{{asset('vendors/fancybox/source/jquery.fancybox.css?v=2.1.5')}}" type="text/css"
          media="screen"/>

    <!-- Toastr style -->
    <link href="{{ asset('system/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
    <!-- Gritter -->
    <link href="{{ asset('system/js/plugins/gritter/jquery.gritter.css')}}" rel="stylesheet">

    <!-- Jquery UI Css -->
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/smoothness/jquery-ui.min.css" rel="stylesheet">
    <!-- PLUpload Css -->
    <link rel="stylesheet" href="{{asset('vendors/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css')}}"/>

    @yield('css','<link href="'.asset('system/css/animate.css').'" rel="stylesheet">')
    <link href="{{ asset('system/vendors/DataTables-1.10.7/media/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/main.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/custom.css')}}" rel="stylesheet">
</head>
