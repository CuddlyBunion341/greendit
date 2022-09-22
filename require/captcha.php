<?php
function createCaptcha($code, $width = 130, $height = 50, $font_size = 30) {
    // create base image
    $img = new Imagick();
    $img->newImage($width, $height, '#fff');

    // draw font
    $draw = new ImagickDraw();
    $draw->setFont('resources/fonts/Monaco.ttf');
    $draw->setFontSize($font_size);
    $img->annotateImage($draw, 10, $height / 2 + $font_size / 2, 0, $code);

    // add noise
    $img->waveImage(2, 20);
    $img->addNoiseImage(4);
    $img->setImageType(IMG_FILTER_GRAYSCALE);

    $draw = new ImagickDraw();
    $y1 = rand(0, $height);
    $y2 = rand(0, $height);
    $draw->line(0, $y1, $width, $y2);

    // export image
    $img->drawImage($draw);
    $img->setImageFormat('png');
    $data = $img->getImageBlob();
    $data = base64_encode($data);
    return $data;
}
function createCaptchaCode($length = 6) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    return $code;
}