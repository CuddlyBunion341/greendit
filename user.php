<?php require('require/header.php'); ?>
<?php
    require_once 'require/feed.php';

    if (!isset($_GET['name'])) {
        header('Location: /greendit/error/400');
        exit;
    }

    require_once 'require/db_connect.php';
    $user = row('select * from users where username = "'.$_GET['name'].'"');
    if (!$user) {
        header('Location: /greendit/error/418');
        exit;
    }

    // tabbed navigation

    $tabs = array('overview','posts','comments','likes','followers');
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
        <a'.active(4).'href="users/'.$user['username'].'/followers">Followers</a>
    </nav>';

    // user sidebar

    $username = $user['username'];
    $date = $user['created_at'];
    $datediff = time() - strtotime($date);
    $date = round($datediff / (60 * 60 * 24));
    $posts = rows('select * from posts where user_id = '.$user['user_id']);
    $comments = rows('select * from comments where user_id = '.$user['user_id']);
    $user_url = 'users/'.$username;

    $follow_active = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $user_id = $user['user_id'];
        $follow_active = exists("select * from followers where user_id=$user_id and follower_id=$session_user");
    }

    echo '
        <main class="multicol">
        <aside>
            <article class="user titled">
                <section class="user__banner">
                    <img src="resources/pfps/'.$username.'.png" alt="'.$username.'" class="pfp large">
                </section>
                <section class="user__main">
                    <h2>'.$username.'</h2>
                    <a href="'.$user_url.'">u/'.$username.'</a>
                </section>
                <section class="user__stats">
                    <p>Joined '.$date.' '.'days ago</p>
                    <a href="'.$user_url.'/posts">'.$posts.' '.'posts'.'</a>
                    <a href="'.$user_url.'/comments">'.$comments.' '.'comments'.' </a>
                </section>
                <button class="follow-btn'.activeClass($follow_active).'" data-username="'.$username.'"></button>
            </article>
        </aside>
        <div id="feed" class="'.activeClass($tab_index != 0,'growing',false).'">
    ';

    if ($tab_index == 0 || $tab_index == 1)  {
        $posts = query('select * from posts where user_id = '.$user['user_id']);
        foreach ($posts as $post) {
            $tab_index == 0 ? postHTML($post,true,false) : overviewPostHTML($post);
        }
        if (!$posts && $tab_index == 1) {
            echo '<p>No posts yet!</p>';
        }
    }
    if ($tab_index == 0 || $tab_index == 2) {
        $comments = query('select * from comments where user_id = '.$user['user_id']);
        foreach ($comments as $comment) {
            overviewCommentHTML($comment);
        }
        if (!$comments && $tab_index == 2) {
            echo '<p>No comments yet!</p>';
        }
    }

    if ($tab_index == 3) {
        $liked_posts = query('
            select * from post_likes
            inner join posts on posts.post_id = post_likes.post_id
            where post_likes.user_id = '.$user['user_id']);
        foreach ($liked_posts as $post) {
            postHTML($post,true,true);
        }
        if (!$liked_posts) {
            echo '<p>No likes yet!</p>';
        }
    }

    if ($tab_index == 4) {
        $followers = query('
            select follower_id,users.username from followers
            inner join users on users.user_id = followers.follower_id
            where followers.user_id = '.$user['user_id']);
        foreach ($followers as $follower) {
            echo '<p>'.$follower['username'].'</p>';
        }
        if (!$followers) {
            echo '<p>No followers yet!</p>';
        }
    }
?>
</div>
<script src="js/feed.js"></script>
</main>
<?php require('require/footer.php'); ?>