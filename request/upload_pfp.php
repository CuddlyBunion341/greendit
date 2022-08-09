<?php
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        exit();
    }
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        exit();
    }
    $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    if ($extension != 'png') {
        http_response_code(405);
        exit();
    }
    // todo: crop image
    $file = $_FILES['file'];
    file_put_contents('../resources/pfps/'.$_SESSION['username'].'.png', file_get_contents($file['tmp_name']));
    echo 'resources/pfps/'.$_SESSION['username'].'.png';
?>