<?php
session_start();
$params = session_get_cookie_params();
setcookie(
    session_name(),
    '',
    1,
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
);
session_destroy();
header("Location: login.php");
