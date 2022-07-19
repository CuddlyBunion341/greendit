<?php
    require_once '../config/db_connect.php';
    require_once '../util/feed.php';
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        echo '
            <div class="create-comment">
                <!--<img src="resources/pfp.png" alt="user" class="user-pfp">-->
                <textarea class="comment-content" placeholder="Write a comment..." rows="4"></textarea>
                <br>
                <button class="comment-btn">Post</button>
            </div>
        ';
    }
    $comments = query('select * from comments where post_id = '. $_GET['post_id']);
    if (count($comments) > 0) {
        foreach ($comments as $comment) {
            commentHTML($comment);
        }
    } else {
        echo '<p>No comments yet.</p>';
    }
?>