<?php

use function PHPSTORM_META\map;

 require 'require/header.php'; ?>
<?php
require_once 'require/uuid.php';
$tab = 0;
$errors = array();
$values = array();
function create_post($title, $community, $body, $tab, $image, $video, $user_id) {
    global $errors;
    if (empty($title)) {
        $errors['title'] = 'Title must not be empty';
    }
    if (empty($community)) {
        $errors['sub'] = 'Please select a community';
    } else {
        $community_id = getField('select community_id from communities where shortname = \'' . $community . '\'');
        if (empty($community_id)) {
            $errors['sub'] = 'Community invalid';
        }
    }

    if ($tab == 0) {
        if (empty($body)) {
            $errors['content'] = 'Post content must not be empty';
        }
    }
    if ($tab == 1) {
        if ($image['error'] != 0) {
            $errors['image'] = 'Attachments cannot be empty';
        } else {
            $valid_extensions = array('png', 'jpg', 'gif', 'tiff', 'bmp', 'webp', 'heic');
            $extension = strtolower(pathinfo($image['name'])['extension']);
            if (!(in_array($extension, $valid_extensions))) {
                $errors['image'] = 'Uploads must be images';
            }
        }
    }
    if ($tab == 2) {
        if ($video['error'] != 0) {
            $errors['video'] = 'Attached video cannot be empty';
        } else {
            $valid_extensions = array('mov', 'mp4', 'avi', 'wmv', 'mp2');
            $extension = strtolower(pathinfo($video['name'])['extension']);
            if (!(in_array($extension, $valid_extensions))) {
                $errors['video'] = 'Upload must be a video';
            }
        }
    }
    if (!empty($errors)) return;
    do {
        $hash = random_string(6);
    } while (exists('select * from posts where hash = \'' . $hash . '\''));
    $sql = "
            insert into posts (title, content, user_id, community_id, hash) 
            values ('$title', '$body', $user_id, $community_id, '$hash')";
    if (execute($sql) !== TRUE) return 'SQL Error';
    if ($tab == 1 || $tab == 2) {
        $file = $tab == 1 ? $image : $video;
        $post_id = getField('select post_id from posts where hash = \'' . $hash . '\'');
        add_attachments($post_id, $file);
    }
}
function get($field) {
    if (isset($_POST[$field])) {
        return trim(htmlspecialchars($_POST[$field]));
    }
    return null;
}
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
function add_attachments($post_id, $file) {
    $tmp_dir = 'resources/uploads/temp';
    if (is_dir($tmp_dir)) {
        remove_dir($tmp_dir);
    }
    mkdir($tmp_dir);
    $path = $file['tmp_name'];
    $hash = md5_file($path);
    $mime = mime_content_type($file['tmp_name']);
    if (strpos($mime, 'video')) {
        $output = 'resources/uploads/' . $hash . '.mp4';
        $output_name = $hash . '.mp4';
        $temp_path = $tmp_dir . '/' . $hash . '.jpg';
        system("ffmpeg -i $path -q:v 0 -vcodec h264 -acodec mp2 -b 5000k $output");
        // thumbnail
        exec("ffmpeg -i $path --ss 00:00:01 -vframes 1 $temp_path");
        create_thumb($temp_path, $hash);
    } else if (strpos($mime, 'image')) {
        $output = 'resources/uploads/' . $hash . '.' . 'webp';
        $output_name = $hash . '.webp';
        if ($mime == 'image/png') {
            $pngimg = imagecreatefrompng($path);
            $w = imagesx($pngimg);
            $h = imagesy($pngimg);
            $im = imagecreatetruecolor($w, $h);
            imagealphablending($im, false);
            imagesavealpha($im, true);

            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $w, $h, $transparent);

            imagecopy($im, $pngimg, 0, 0, 0, 0, $w, $h);
            imagewebp($im, $output, 100);
            imagedestroy($im);
        } else if ($mime == 'image/jpeg') {
            $im = imagecreatefromjpeg($path);
            imagewebp($im, $output, 100);
        } else if ($mime == 'image/gif') {
            $im = imagecreatefromgif($path);
            imagewebp($im, $output, 100);
        } else if ($mime == 'image/heic') {
            die('HEIC not supported');
        } else {
            die('Unsupported image type');
        }
    }
    create_thumb($path, $hash);
    execute('insert into post_media (post_id,file_name) values(' . $post_id . ',\'' . $output_name . '\')');
    remove_dir($tmp_dir);
}
function create_thumb($path, $hash) {
    $img = imagecreatefromstring(file_get_contents($path));

    $w = imagesx($img);
    $h = imagesy($img);
    $r = $w / $h;

    $tw = 280;
    $th = 210;
    $tr = $tw / $th;

    if ($r > $tr) {
        $nw = $h * $tr;
        $nh = $h;
    } else {
        $nw = $w;
        $nh = $w / $tr;
    }

    $img = imagecrop($img, [
        'x' => 0,
        'y' => 0,
        'width' => $nw,
        'height' => $nh
    ]);

    $img = imagescale($img, $tw, $th, IMG_BICUBIC_FIXED);
    imagejpeg($img, 'resources/uploads/thumbnails/' . $hash . '.jpg', 30);
    imagedestroy($img);
}
function value($field) {
    if (!isset($_POST[$field])) return '';
    echo $_POST[$field];
}
function showTab($index) {
    global $tab;
    if ($tab != $index) {
        echo 'class="hidden"';
    } else {
        echo 'class="active"';
    }
}
function activeBtn($index) {
    global $tab;
    if ($tab == $index) {
        echo 'active';
    }
}
if (isset($_POST['submit'])) {
    $tab = get('tab');
    require_once 'require/db_connect.php';
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['user_id'])) $error = 'You must be logged in to post';
    if (!isset($_POST['sub'])) $errors['sub'] = 'Community is required';
    if (!isset($_POST['title'])) $errors['title'] = 'Title is required';
    if (empty($error)) {
        $error = create_post(
            get('title'),
            get('sub'),
            get('content'),
            $tab,
            isset($_FILES['image']) ? $_FILES['image'] : array('error' => 1),
            isset($_FILES['video']) ? $_FILES['video'] : array('error' => 1),
            $_SESSION['user_id']
        );
    }
    if (empty($error) && empty($errors)) {
        header('Location: index.php');
    }
}
function error($name) {
    global $errors;
    if (isset($errors[$name])) {
        echo '<p class="error">'.$errors[$name].'</p>';
    }
}
?>
<main class="multicol">
    <aside class="community__info below" id="sidebar">
        <article class="info titled">
            <h1 class="communtiy__info-about">Community info</h1>
            <p>Please select a community</p>
        </article>
        <article class="community__rules titled">
            <h1>Greendit Rules</h1>
            <ul>
                <li>
                    <details>
                        <summary>1.Posts must be funny</summary>
                        <p>At least an attempt</p>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>2.Posts must be programming realted</summary>
                        <p>Posts must be related to programming or the profession in general</p>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>3.No Reposts</summary>
                        <p>Content that has been already posted less than a month ago will be removed</p>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>4.No low-quality content</summary>
                        <p>Bad posts will be removed</p>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>5.Put effort into titles</summary>
                        <p>Titles are important</p>
                    </details>
                </li>
            </ul>
        </article>
        <article class="titled">
            <h1>Administrators</h1>
            <ul>
                <li class="flair">
                    <a href="/greendit/users/admin">
                        <img class="pfp small" src="resources/pfps/admin.png" alt="admin">u/admin
                    </a>
                </li>
            </ul>
        </article>
    </aside>
    <div class="center">
        <?php
            if (isset($error)) {
                echo '<article class="error">' . $error . '</article>';
            }
        ?>
        <form action="post.php" method="post" id="create-post-form" enctype='multipart/form-data'>
            <article class="composer titled">
                <h1>Create a post</h1>
                <div id="sub-group">
                    <select name="sub" id="sub" required>
                        <option value="" selected hidden disabled>Community</option>
                        <?php
                        require_once('require/db_connect.php');
                        $subs = query('select * from communities order by shortname asc');
                        foreach ($subs as $sub) {
                            $selected = '';
                            if (isset($_GET['id']) && $_GET['id'] == $sub['community_id']) {
                                $selected = 'selected';
                            }
                            if (isset($_POST['sub']) && $_POST['sub'] == $sub['shortname']) {
                                $selected = 'selected';
                            }
                            echo '<option ' . $selected . ' value="' . $sub['shortname'] . '">s/' . $sub['shortname'] . '</option>';
                        }
                        ?>
                    </select>
                    <?php error('sub'); ?>
                </div>
                <div class="tabs">
                    <button aria-label="tab-post" type="button" class="post-tab <?php activeBtn(0); ?>"><i class="fa-solid fa-font"></i></i>Post</button>
                    <button aria-label="tab-image" type="button" class="image-tab <?php activeBtn(1); ?>"><i class="fa-solid fa-images"></i>Images</button>
                    <button aria-label="tab-video" type="button" class="video-tab <?php activeBtn(2); ?>"><i class="fa-solid fa-film"></i>Video</button>
                </div>
                <div id="title-group">
                    <input type="text" name="title" id="title" placeholder="Title" value="<?php value('title'); ?>" required>
                    <?php error('title'); ?>
                </div>
                <div class="post-content">
                    <div data-tab="0" <?php showTab(0); ?>>
                    <div id="content-group">
                        <textarea name="content" id="content" cols="30" rows="10" placeholder="Text (required)"
                        ><?php value('content'); ?></textarea>
                        <?php error('content'); ?>
                    </div>
                    </div>
                    <div data-tab="1" <?php showTab(1); ?>>
                        <div class="file-select">
                            <button aria-label="upload" type="button" name="upload-btn">Upload</button>
                            <input type="file" name="image" id="image-input" class="hidden" accept="image/*">
                        </div>
                        <?php error('image'); ?>
                        <div id="preview" class="hidden">
                        </div>
                    </div>
                    <div data-tab="2" <?php showTab(2); ?>>
                        <div class="file-select">
                            <button aria-label="upload" type="button" name="upload-btn">Upload</button>
                            <input type="file" name="video" id="video-input" class="hidden" accept="video/*">
                        </div>
                        <?php error('video'); ?>
                        <video class="hidden" controls></video>
                        <button aria-label="remove" name="remove-btn" type="button" class="hidden">Remove</button>
                    </div>
                </div>
                <input type="hidden" name="tab" value="<?php echo $tab; ?>" id="tab-val">
                <button aria-label="submit" type="submit" name="submit" class="post-btn">Post</button>
            </article>
        </form>
    </div>
    <script src="js/create-post.js"></script>
</main>