<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="{{ asset('system/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('system/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('system/css/style.css') }}" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div id="logo">
            <img src="{{ asset('system/img/logo.png') }}">
        </div>
        <h2>Reset Password</h2>
        <img class="loading" src="{{ asset('system/img/loading/default.gif') }} " style="display: none;">
        <div class="alert " style="display: none;"></div>
        {!! Form::open(
            array(
            'action'=>"System\PasswordController@postEmail",
            'method'=>'post','class'=>'m-t',
            'role'=>'form',
            'id' => 'ResetPasswordForm'
            )
        )!!}
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Email" required value="{{old('email')}}">
        </div>

        <button type="submit" class="btn btn-primary block full-width m-b">Send</button>

        {!! Form::close() !!}
        <a id="LoginPage" href="{{url('system/login')}}" class="btn btn-primary block full-width m-b" style="display: none;">Go to login page</a>
        <p class="m-t"> <small>copy right &copy; <?= date('Y'); ?> www.quo-global.com</small> </p>
    </div>
</div>
<!-- Mainly scripts -->
<script src="{{ asset('system/js/jquery-2.1.1.js') }}"></script>
<script src="{{ asset('system/js/bootstrap.min.js') }}"></script>
<script>
    $(function(){
        var form = $("#ResetPasswordForm");
        var alert_message = $('.gray-bg .alert');
        form.submit(function (e) {
            $(".loading").show();
            form.hide();
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                dataType: 'json',
                data: form.serialize(),
                success: function (data) {
                    alert_message.removeClass('alert-danger').addClass('alert-success').hide();
                    alert_message.text(data.message).show();
                    form.hide();
                    $("#LoginPage").show();
                    $(".loading").hide();
                },
                error: function (data) {

                    var errors = data.responseJSON;

                    alert_message.removeClass('alert-success').addClass('alert-danger').empty().hide();

                    var error_message = "";

                    for (var prop in errors) {
                        error_message += errors[prop] + "<br>";
                        form.find("input[name='" + prop + "']").removeClass('valid').addClass('error').show();
                    }
                    alert_message.append(error_message).show();
                    form.show();
                    $("#LoginPage").hide();
                    $(".loading").hide();
                }
            });
            return false;
        });
    });
</script>
</body>
</html>
