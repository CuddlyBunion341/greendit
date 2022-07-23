<?php require('require/header.php'); ?>
<link rel="stylesheet" href="css/community.css">
<?php
    if (!isset($_GET['name'])) {
        header('Location: /greendit/error/419');
        exit;
    }
    $name = $_GET['name'];
    require_once('config/db_connect.php');
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
            if (!isset($_GET['post_hash'],$_GET['comment_hash'])) {
                // show all posts
                if (isset($_SESSION['user_id'])) {
                    echo '
                    <div class="create-post">
                        <a href="user/'.$_SESSION['username'].'"><img class="user-pfp" src="resources/pfp.png"></a>
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
            }
            // show specific post
            else if (isset($_GET['post_hash'])) {
                $post = row('select * from posts where post_hash = ' . $_GET['post_hash']);
                if (!$post) {
                    http_response_code(404); // todo: post not found
                    exit;
                }
            } else if (isset($_GET['comment_hash'])) {
                $comment = row('select * from comments where comment_hash = ' . $_GET['comment_hash']);
                if (!$comment) {
                    http_response_code(404); // todo: comment not found
                    exit;
                }
                $post = row('select * from posts where post_id = ' . $comment['post_id']);
            }
            // hope post exists...
            postHTML($post,false);
            $comments = query('select * from comments where post_id = ' . $post['post_id']);
            foreach ($comments as $comment) {
                commentHTML($comment);
            }
        ?>
        <script src="js/feed.js"></script>
    </div>
</main>
<?php require('require/footer.php'); ?>