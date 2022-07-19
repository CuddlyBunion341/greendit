<?php
    require_once '../config/db_connect.php';
    require_once '../util/feed.php';
    $comments = query('select * from comments where post_id = '. $_GET['post_id']);
    if (count($comments) > 0) {
        foreach ($comments as $comment) {
            commentHTML($comment);
        }
    } else {
        echo '<p>No comments yet.</p>';
    }
?>