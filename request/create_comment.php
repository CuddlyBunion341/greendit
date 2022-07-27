<?php
    require_once '../require/db_connect.php';
    require_once '../require/feed.php';
    require_once '../require/uuid.php';
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        if (!isset($_POST['content'],$_POST['post'])) {
            echo 'Missing content or post_id.';
            http_response_code(400);
            exit();
        }
        $content = trim(htmlspecialchars($_POST['content']));
        if (empty($content)) {
            echo 'Content cannot be empty';
            http_response_code(400);
            exit();
        }
        $comment = row('select * from posts where hash =\'' . $_POST['post'] . '\'');
        $post_id = $comment['post_id'];
        if ($comment['status'] == 'removed') {
            http_response_code(400);
            exit();
        }
        $hash = random_string(6);
        // todo: test if hash is unique
        execute('insert into comments (user_id, post_id, content, hash) values ('.$user_id.', '.$post_id.', \''.$content.'\', \''.$hash.'\')');
        $comment_id = $conn->insert_id;
        $comment = row('select * from comments where comment_id = '.$comment_id);
        if (!$comment) {
            http_response_code(500);
            echo 'Error creating comment.';
            exit();
        }
        commentHTML($comment);
    } else {
        http_response_code(401);
    }
?>