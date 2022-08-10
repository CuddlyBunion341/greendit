<?php
if (!isset($_FILES['file'])) {
    http_response_code(400);
    exit('Missing file');
}
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}
$image = $_FILES['file'];
$extension = pathinfo($image['name'], PATHINFO_EXTENSION);

$image_extensions = array('png', 'jpg', 'jpeg', 'gif');
if (!in_array(strtolower($extension), $image_extensions)) {
    http_response_code(400);
    exit('Invalid file type');
}
$img = imagecreatefromstring(file_get_contents($image['tmp_name']));
$size = min(imagesx($img), imagesy($img));
$img = imagecrop($img, [
    'x' => 0,
    'y' => 0,
    'width' => $size,
    'height' => $size
]);
$img = imagescale($img, 128);
imagepng($img, '../resources/pfps/' . $_SESSION['username'] . '.png');
echo 'resources/pfps/' . $_SESSION['username'] . '.png';
