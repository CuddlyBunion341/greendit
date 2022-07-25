<?php require 'require/header.php';?>
<?php
    require_once 'require/uuid.php';
    function create_post($title, $body, $user_id, $community) {
        if (empty(prepare($title))) return 'Title must not be empty';
        if (empty(prepare($body))) return 'Content must not be empty';
        $community_id = getField('select community_id from communities where shortname = \'' . prepare($community) . '\'');
        if (empty(prepare($community_id))) return 'Please select a community';
        do {
            $hash = random_string(6);
        } while (rows('select * from posts where hash = \'' . $hash .'\'') > 0);
        $sql = "
            insert into posts (title, content, user_id, community_id, hash) 
            values ('$title', '$body', $user_id, $community_id, '$hash')";
        if (execute($sql) !== TRUE) return 'SQL Error';
    }
    function prepare(&$field) {
        if (!isset($field)) return null;
        $field = trim(htmlspecialchars($field));
        return $field;
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
        if (!isset($_POST['content'])) $error = 'Content is required';
        if (empty($error)) {
            $error = create_post($_POST['title'], $_POST['content'], $_SESSION['user_id'], $_POST['sub']);
        }
        if (empty($error)) {
            header('Location: index.php');
        }        
    }
?>
<main>
    <link rel="stylesheet" href="css/post.css">
    <form action="post.php" method="post" class="create-post-form">
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
                    <label for="upload">File: </label>
                    <input type="file" name="upload" id="upload">
                </div>
            </div>
            <button type="submit" name="submit">Post</button>
        </div>
    </form>
    <script src="js/create-post.js"></script>
</main>
<?php require 'require/footer.php';?>