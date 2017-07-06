<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Verify Your Email Address</h2>

<div>
    Your account has been created, you can login with the following credentials : <br/>
    E-mail : {{$email}} <br>
    Password : {{$password}} <br>
    Please click a link below for verification:<br>
    {{ URL::to('system/registered-user/verify/' . $token) }}<br/>

</div>

</body>
</html>

