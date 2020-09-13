<!DOCTYPE html>
<html>
<head>
    <title>Larafinance</title>
</head>
<body>
    <h1>usrname:{{ $user->email }}</h1><br>
    <p>password:{{ $user->email }}</p>
    <p>Click <a href="{{ route('login') }}">here to login</a></p>
    <p>Thank you</p>
</body>
</html>