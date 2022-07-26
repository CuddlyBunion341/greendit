<?php require 'require/header.php';?>
<?php
    require_once 'require/uuid.php';
    function create_post($title, $community, $body, $tab, $file, $user_id) {
        if (empty($title)) return 'Title must not be empty';
        $community_id = getField('select community_id from communities where shortname = \'' . $community . '\'');
        if (empty($community_id)) return 'Please select a community';

        if ($tab == 0) {
            if (empty($body)) return 'Post content must not be empty';
        }
        // ---- Execute Command ------------------------------------------------------------------
        do {
            $hash = random_string(6);
        } while (rows('select * from posts where hash = \'' . $hash .'\'') > 0);
        $sql = "
            insert into posts (title, content, user_id, community_id, hash) 
            values ('$title', '$body', $user_id, $community_id, '$hash')";
        if (execute($sql) !== TRUE) return 'SQL Error';
        // ---- Files ----------------------------------------------------------------------------
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
        // todo: delete post if invalid extension
        $name = md5_file($file['tmp_name']) .'.'.$extension;
        move_uploaded_file($file['tmp_name'],'resources/uploads/'.$name);
        execute('insert into post_media (post_id,file_name) values('.$post_id.',\''.$name.'\')');
    }
    function value($field) {
        if (!isset($_POST[$field])) return '';
        echo $_POST[$field];
    }
    if (isset($_POST['submit'])) {
        require_once 'require/db_connect.php';
        if (!isset($_SESSION)) session_start();
        if (!isset($_SESSION['user_id'])) $error = 'You must be logged in to post';
        if (!isset($_POST['sub'])) $error = 'Community is required';
        if (!isset($_POST['title'])) $error = 'Title is required';
        if (empty($error)) {
            create_post(
                get('title'),
                get('sub'),
                get('content'),
                get('tab'),
                $_FILES['media'],
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
        <div class="container">
            <h1>Create a post</h1>
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
                <button type="button" class="post-tab active">Post</button>
                <button type="button" class="media-tab">Images & Video</button>
            </div>
            <input type="text" name="title" id="title" placeholder="Title" value="<?php value('title'); ?>">
            <div class="post-content">
                <div data-tab="0">
                    <textarea name="content" id="content" cols="30" rows="10" placeholder="Text (required)"><?php value('content'); ?></textarea>
                </div>
                <div data-tab="1" class="hidden">
                    <div class="file-select">
                        <input type="file" name="media" id="media" value="Upload">
                    </div>
                    <div id="preview">
                        <p>No file currently selected for upload</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="tab" value="0" id="tab-val">
            <button type="submit" name="submit">Post</button>
        </div>
    </form>
    <script src="js/create-post.js"></script>
</main>
<?php require 'require/footer.php';?>