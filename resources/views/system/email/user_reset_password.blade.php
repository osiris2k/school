<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Reset Password</h2>

<div>
    Click here to reset your password: {{ url('system/password/reset/'.$token) }}

</div>

</body>
</html>