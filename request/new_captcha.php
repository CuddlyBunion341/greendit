<?php
session_start();
require __DIR__ . '/../require/captcha.php';

$code = createCaptchaCode();
$_SESSION['captcha'] = $code;

$base64 = createCaptcha($code);

echo $base64;