<?php require 'require/header.php';?>
<?php
    require_once 'require/uuid.php';
    $tab = 0;
    function create_post($title, $community, $body, $tab, $file, $user_id) {
        if (empty($title)) return 'Title must not be empty';
        $community_id = getField('select community_id from communities where shortname = \'' . $community . '\'');
        if (empty($community_id)) return 'Please select a community';

        if ($tab == 0) {
            if (empty($body)) return 'Post content must not be empty';
        }
        if ($tab == 1) {
            if ($file['error'] != 0) return 'Attachments cannot be empty';
            $valid_extensions = array('png','jpg','gif','tiff','bmp','webp');
            $extension = pathinfo($file['name'])['extension'];
            if (!(in_array($extension,$valid_extensions))) return 'Uploads must be images';
        }
        do {
            $hash = random_string(6);
        } while (rows('select * from posts where hash = \'' . $hash .'\'') > 0);
        $sql = "
            insert into posts (title, content, user_id, community_id, hash) 
            values ('$title', '$body', $user_id, $community_id, '$hash')";
        if (execute($sql) !== TRUE) return 'SQL Error';
        if ($tab == 1) {
            $post_id = getField('select post_id from posts where hash = \'' . $hash . '\'');
            add_attachments($post_id, $file);
        }
    }
    function get($field) {
        if (isset($_POST[$field])) {
            return trim(htmlspecialchars($_POST[$field]));
        }
        return NULL;
    }
    function add_attachments($post_id, $file) {
        $extension = pathinfo($file['name'])['extension'];
        $name = md5_file($file['tmp_name']) .'.'.$extension;
        move_uploaded_file($file['tmp_name'],'resources/uploads/'.$name);
        execute('insert into post_media (post_id,file_name) values('.$post_id.',\''.$name.'\')');
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
        if (!isset($_POST['sub'])) $error = 'Community is required';
        if (!isset($_POST['title'])) $error = 'Title is required';
        if (empty($error)) {
            $error = create_post(
                get('title'),
                get('sub'),
                get('content'),
                $tab,
                $_FILES['image'],
                $_SESSION['user_id']
            );
        }
        if (empty($error)) {
            header('Location: index.php');
        }        
    }
?>
<main>
    <link rel="stylesheet" href="css/post.css">
    <form action="post.php" method="post" class="create-post-form" enctype='multipart/form-data'>
        <h1>Create a post</h1>
        <div class="composer">
            <?php
                if(isset($error)) {
                    echo '<p class="error">'.$error.'</p>';
                }
            ?>
            <select name="sub" id="sub">
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
                        echo '<option '.$selected.' value="'.$sub['shortname'].'">s/'.$sub['shortname'].'</option>';
                    }
                ?>
            </select>
            <div class="tabs">
                <button type="button" class="post-tab <?php activeBtn(0); ?>">Post</button>
                <button type="button" class="image-tab <?php activeBtn(1); ?>">Images</button>
                <button type="button" class="video-tab <?php activeBtn(2); ?>">Video</button>
            </div>
            <input type="text" name="title" id="title" placeholder="Title" value="<?php value('title'); ?>">
            <div class="post-content">
                <div data-tab="0" <?php showTab(0); ?>>
                    <textarea name="content" id="content" cols="30" rows="10" placeholder="Text (required)"><?php value('content'); ?></textarea>
                </div>
                <div data-tab="1" <?php showTab(1); ?>>
                    <div class="file-select">
                        <button type="button" name="upload-btn">Upload</button>
                        <input type="file" name="image" id="image-input" class="hidden" accept="image/*">
                    </div>
                    <div id="preview">
                    </div>
                </div>
                <div data-tab="2" <?php showTab(2); ?>>
                    <div class="file-select">
                        <button type="button" name="upload-btn">Upload</button>
                        <input type="file" name="video" id="video-input" class="hidden" accept="video/*">
                    </div>
                    <video class="hidden" controls></video>
                </div>
            </div>
            <input type="hidden" name="tab" value="<?php echo $tab; ?>" id="tab-val">
            <button type="submit" name="submit">Post</button>
        </div>
    </form>
    <script src="js/create-post.js"></script>
</main>
<?php require 'require/footer.php';?>