<?php
    require_once '../config/db_connect.php';
    require_once '../util/feed.php';
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        if (!isset($_POST['content'],$_POST['post_id'])) {
            http_response_code(400);
            echo '<p>Missing content or post_id.</p>';
            exit();
        }
        $content = $_POST['content'];
        $post_id = $_POST['post_id'];
        execute('insert into comments (user_id, post_id, content) values (\''.$user_id.'\', \''.$post_id.'\', \''.$content.'\')');
        $comment_id = $conn->insert_id;
        $comment = row('select * from comments where comment_id = '.$comment_id);
        if (!$comment) {
            http_response_code(500);
            echo '<p>Error creating comment.</p>';
            exit();
        }
        commentHTML($comment);
    }
?>