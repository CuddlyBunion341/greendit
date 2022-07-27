<?php require('require/header.php'); ?>
<link rel="stylesheet" href="css/community.css">
<?php
    if (!isset($_GET['name'])) {
        header('Location: /greendit/error/419');
        exit;
    }
    $name = $_GET['name'];
    require_once('require/db_connect.php');
    $community = row('select * from communities where shortname = "' . $name . '"');
    if (!$community) {
        header('Location: /greendit/error/419');
        exit;
    }
    function plural($word,$num) {
        if ($num == 1) {
            return $word;
        }
        return $word.'s';
    }
    $posts = rows('select * from posts where community_id = ' . $community['community_id']);
    $users = rows('select * from joined_communities where community_id = ' . $community['community_id']);
    echo '
    <div class="community-header">
        <img class="community-background" src="resources/community.png" alt="">
        <div class="community-banner">
            <img class="community-pfp" src="resources/com.png" alt="">
            <div class="community-banner-main">
                <div class="top">
                    <h2>' . $community['name'] . '</h2>
                </div>
                <a href="subs/' . $community['shortname'] . '">s/' . $community['shortname'] . '</a>
            </div>
        </div>
        <div class="community-main">
            <div class="community-stats">
                <p>Created ' . $community['created_at'] . '</p>
                <p>' . $posts . ' '.plural('post',$posts).'</p>
                <p>' . $users . ' '.plural('member',$users).'</p>
            </div>
        </div>
    </div>
    ';
?>
<main>
    <h2>Popular posts</h2>
    <div id="feed">
        <?php
            require 'require/feed.php';
            if (!isset($_GET['post'])) {
                // show all posts
                if (isset($_SESSION['user_id'])) {
                    echo '
                    <div class="create-post">
                        <a href="user/'.$_SESSION['username'].'"><img class="user-pfp" src="resources/pfps/'.$_SESSION['username'].'.png"></a>
                        <button onclick="window.location=\'/greendit/post.php?id='.$community['community_id'].'\'">Create post...</button>
                    </div>
                    ';
                }
                $sql = 'select * from posts where community_id='.$community['community_id'].' order by post_id desc';
                $posts = query($sql);
    
                foreach ($posts as $post) {
                    postHTML($post,false);
                }
                if (count($posts) == 0) {
                    echo '<div class="feed-text">No posts yet!</div>';
                }
            } else {
                // show specific post
                if (isset($_GET['post'])) {
                    $post = row('select * from posts where hash = \'' . $_GET['post'] . '\'');
                    if (!$post) {
                        echo 'Post not found :/';
                    }
                }
                // } else if (isset($_GET['comment'])) {
                //     $comment = row('select * from comments where hash = \'' . $_GET['comment'] . '\'');
                //     if (!$comment) {
                //         echo 'Comment not found :/';
                //     }
                //     $post = row('select * from posts where post_id = ' . $comment['post_id']);
                // }
                if ($post) {
                    postHTML($post,false);
                    $comments = query('select * from comments where post_id = ' . $post['post_id']);
                    echo '<div class="comment-wrapper" data-hash="'.$post['hash'].'">';
                    if (isset($_SESSION['user_id'])) {
                        $username = $_SESSION['username'];
                        echo '
                            <form class="create-comment">
                                <img src="resources/pfps/'.$username.'.png" alt="user" class="user-pfp">
                                <input type="text" class="comment-content" placeholder="Write a comment..." rows="4">
                                <button name="comment-btn" type="submit" name="submit" class="comment-btn">Post</button>
                            </form>
                        ';
                    }
                    if (count($comments) > 0) {
                        foreach ($comments as $comment) {
                            commentHTML($comment);
                        }
                    } else {
                        echo '<p>No comments yet.</p>';
                    }
                    echo '</div>';
                    if (isset($_GET['comment'])) {
                        echo '
                        <script>
                            document.addEventListener("DOMContentLoaded",() => {
                                const comment = document.querySelector("#comment-'.$_GET['comment'].'");
                                comment.scrollIntoView();
                            });
                        </script>
                        ';
                    }
                }
            }
        ?>
        <script src="js/feed.js"></script>
    </div>
</main>
<?php require('require/footer.php'); ?>