<?php require('require/header.php'); ?>
<?php
    function plural($word,$num) {
        if ($num == 1) {
            return $word;
        }
        return $word.'s';
    }

    if (isset($_GET['name'])) {
        require_once 'require/db_connect.php';
        $user = row('select * from users where username = "' . $_GET['name'] . '"');
        if (!$user) {
            header('Location: /greendit/error/418');
            exit;
        }
        $date = $user['created_at'];
        $datediff = time() - strtotime($date);
        $date = round($datediff / (60 * 60 * 24));
        $posts = rows('select * from posts where user_id = ' . $user['user_id']);
        $comments = rows('select * from comments where user_id = ' . $user['user_id']);
        $user_url = 'users/' . $user['username'];
        echo '
            <main>
            <div class="user-info">
                <div class="user-banner">
                    <img src="resources/pfp.png" alt="">
                </div>
                <div class="user-main">
                    <h2>' . $user['username'] . '</h2>
                    <a href="'.$user_url.'">u/' . $user['username'] . '</a>
                    <p>Joined ' . $date . ' '.plural('day',$date).' ago</p>
                </div>
                <div class="user-stats">
                    <a href="'.$user_url.'/posts">' . $posts . ' '.plural('post',$posts).'</a><br>
                    <a href="'.$user_url.'/comments">' . $comments . ' '.plural('comment',$comments).' </a>
                </div>
                <button class="follow-btn">Follow</button>
            </div>
        ';
    }

    $tabs = array('overview','posts','comments','likes');
    $tab_index = isset($_GET['tab']) ? array_search($_GET['tab'],$tabs) : 0;

    function active($index) {
        global $tab_index;
        if ($index == $tab_index) {
            return ' class="active" ';
        }
        return ' ';
    }
    echo '
    <nav class="tabbs">
        <a'.active(0).'href="users/'.$user['username'].'/overview">Overview</a>
        <a'.active(1).'href="users/'.$user['username'].'/posts">Posts</a>
        <a'.active(2).'href="users/'.$user['username'].'/comments">Comments</a>
        <a'.active(3).'href="users/'.$user['username'].'/likes">Likes</a>
    </nav>
    <div id="feed" class="ignore-css">
    ';

    require_once 'require/feed.php';
    if ($tab_index == 0 || $tab_index == 1)  {
        $posts = query('select * from posts where user_id = ' . $user['user_id']);
        foreach ($posts as $post) {
            postHTML($post,true,false);
        }
        if (!$posts && $tab_index == 1) {
            echo '<p>No posts yet!</p>';
        }
    }
    if ($tab_index == 0 || $tab_index == 2) {
        $comments = query('select * from comments where user_id = ' . $user['user_id']);
        foreach ($comments as $comment) {
            commentHTML($comment);
        }
        if (!$comments && $tab_index == 2) {
            echo '<p>No comments yet!</p>';
        }
    }

    if ($tab_index == 3) {
        $sql = '
        select *
        from post_likes
        inner join posts on posts.post_id = post_likes.post_id
        where post_likes.user_id = ' . $user['user_id'] . ';';
        $liked_posts = query($sql);
        foreach ($liked_posts as $post) {
            postHTML($post,true,true);
        }
        if (!$liked_posts) {
            echo '<p>No likes yet!</p>';
        }
    }
?>
</div>
<script src="js/feed.js"></script>
</main>
<?php require('require/footer.php'); ?>