<!DOCTYPE html>
<html lang="en">
<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!-- Some icons used on greendit are from Font Awesome Pro 6.1.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc.-->

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="white">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Greendit is a lightweight and open source version of reddit using PHP and MySQL">
    <title>Greendit</title>
    <base href="/greendit/" />
    <script src="js/myquery.js"></script>
    <script src="js/main.js" defer></script>
    <link rel="stylesheet" href="stylesheets/main.min.css">
</head>
<?php
$body_class = '';
if (isset($header) && $header['flex'])
    $body_class .= 'flex';
?>

<body <?= isset($body_class) ? 'class="' . $body_class . '"' : '' ?>>
    <div id="icon-defs">
        <?php
        // used to preload svgs
        $path = __DIR__ . '/../resources/icons';

        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ('.' === $file) continue;
                if ('..' === $file) continue;
                $name = explode('.', $file)[0];
                $svg = file_get_contents($path . '/' . $file);
                $svg = str_replace('<svg', '<svg id="icon-' . $name . '"', $svg);
                $svg = str_replace(' xmlns="https://www.w3.org/2000/svg ', '', $svg);
                echo $svg;
            }
            closedir($handle);
        }
        ?>
    </div>
    <header>
        <?php require 'nav.php'; ?>
    </header>