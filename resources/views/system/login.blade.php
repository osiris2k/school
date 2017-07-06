<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS 2015</title>
    <link href="{{ asset('system/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('system/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/custom.css') }}" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="login-container">
    <div class="login-body container animated fadeInDown">
        <div class="row login-body-wrapper">
            <div class="col-xs-12 col-sm-6 text-center">
                <img src="{{ asset('system/img/publico-logo.png') }}" class="img-responsive login-logo">
                {{--<h2><a href="/" target="_blank" class="login-logo-subtitle">publico</a></h2>--}}
            </div>
            <div class="login-vertical-line"></div>
            <div class="col-xs-12 col-sm-6">
                {!! Form::open(array('action'=>'System\AuthController@auth','method'=>'post','class'=>'m-t login-form','role'=>'form'))!!}

                <h1 class="login-title">Welcome to</h1>
                <h4 class="login-subtitle">Content Management System 2015</h4>

                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" required="">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b login-button">Login</button>

                <a href="{{url('system/password/email')}}" class="forget-password-link"><p>Forgot password?</p></a>
                {{-- <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a> --}}
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <footer>
        <span>Copyright &copy; <?= date('Y'); ?> <a href="http://www.quo-global.com" target="_blank">www.quo-global.com</a></span>
    </footer>
</div>

<!-- Mainly scripts -->
<script src="{{ asset('system/js/jquery-2.1.1.js') }}"></script>
<script src="{{ asset('system/js/bootstrap.min.js') }}"></script>
</body>
</html>
