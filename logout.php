<?php
session_start();
session_destroy();
$location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header('Location: ' . $location);
?>
