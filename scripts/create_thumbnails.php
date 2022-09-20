<?php
// check if directory exists
$directory = __DIR__ . '/../resources/uploads';
if (!is_dir($directory)) {
    exit('Directory not found');
}

// get filenames
$files = array();
foreach (scandir($directory) as $file) {
    if ($file != '.' && $file != '..' && !is_dir($directory . '/' . $file)) {
        $files[] = $file;
    }
}

// create thumbnail dir if it doesn't exist
if (!is_dir($directory . '/thumbnails')) {
    mkdir($directory . '/thumbnails');
}

// create temporary directory
$tmp_dir = $directory . '/tmp';
function remove_dir($dir) {
    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if ($file != '.' && $file != '..') {
                unlink($dir . '/' . $file);
            }
        }
        rmdir($dir);
    }
}
remove_dir($tmp_dir);
mkdir($tmp_dir);

echo '<pre>';
print_r($files);
// create thumbnails
foreach ($files as $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $hash = explode('.', $file)[0];
    $path = $directory . '/' . $file;
    $mime = mime_content_type($path);
    if (strstr($mime, 'video/')) {
        $temp_path = "$tmp_dir/$hash.jpg";
        exec("ffmpeg -i $path -ss 0 -vframes 1 $temp_path");
        createThumbnail($temp_path, $hash);
    } else if (strstr($mime, 'image/')) {
        createThumbnail($path, $hash);
    } else {
        print_r($mime);
    }
}

// creates thumbnail for a given file
function createThumbnail($path, $hash) {
    global $directory;
    $img = imagecreatefromstring(file_get_contents($path));
    $width = imagesx($img);
    $height = imagesy($img);
    $ratio = .75;
    if ($width > $height) {
        $new_height = imagesy($img);
        $new_width = $height / $ratio;
    } else if ($width < $height) {
        $new_width = imagesx($img);
        $new_height = $width * $ratio;
    } else {
        $new_width = $width;
        $new_height = $height / $ratio;
    }

    $img = imagecrop($img, [
        'x' => 0,
        'y' => 0,
        'width' => $new_width,
        'height' => $new_height
    ]);
    $thumb = imagecreatetruecolor(280, 210);
    imagefilledrectangle($thumb, 0, 0, 280, 210, imagecolorallocate($thumb, 255, 255, 255));
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, 280, 210, $new_width, $new_height);
    imagejpeg($thumb, $directory . '/thumbnails/' . $hash . '.jpg');
    imagedestroy($img);
    imagedestroy($thumb);
}

// remove temporary directory
remove_dir($tmp_dir);