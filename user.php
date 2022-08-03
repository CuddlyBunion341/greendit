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

    $tabs = array('overview','posts','comments','liked');
    $tab_index = isset($_GET['tab']) ? array_search($_GET['tab'],$tabs) : 0;

    function active($index) {
        global $tab_index;
        if ($index == $tab_index) {
            return ' class="active" ';
        }
        return ' ';
    }
    $username = $user['username'];
    echo '
    <nav class="tabbs">
        <a'.active(0).'href="users/'.$username.'/overview">Overview</a>
        <a'.active(1).'href="users/'.$username.'/posts">Posts</a>
        <a'.active(2).'href="users/'.$username.'/comments">Comments</a>
        <a'.active(3).'href="users/'.$username.'/liked">Liked</a>
    </nav>';

    // user sidebar

    $date = $user['created_at'];
    $datediff = time() - strtotime($date);
    $date = round($datediff / (60 * 60 * 24));
    $posts = rows('select * from posts where user_id = '.$user['user_id']);
    $comments = rows('select * from comments where user_id = '.$user['user_id']);
    $user_url = 'users/'.$username;
    $user_id = $user['user_id'];

    $follow_active = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $follow_active = exists("select * from followers where user_id=$user_id and follower_id=$session_user");
    }

    {
        $followers = query('
        select follower_id,users.username from followers
        inner join users on users.user_id = followers.follower_id
        where followers.user_id = '.$user_id);
        if (!$followers) {
            $followers_article = '';
        } else {
            $followers_article = '
            <article class="titled">
                <h1>Followers</h1>
                <ul>';
            foreach ($followers as $follower) {
                $name = $follower['username'];
                $followers_article .= '
                <li class="flair">
                    <a href="/greendit/users/'.$name.'">
                        <img class="pfp small" src="resources/pfps/'.$name.'.png" alt="'.$name.'">'.$name.'
                    </a>
                </li>
                ';
            }
            $followers_article .= '</ul></article>';
        }
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
            '.$followers_article.'
        </aside>
        <div id="feed" class="'.activeClass($tab_index == 1 || $tab_index == 2,'growing',false).'">
    ';

    if ($tab_index == 0 || $tab_index == 1)  {
        $posts = query('
            select *,posts.created_at as date from posts 
            where user_id='.$user_id.' 
            UNION
            select posts.*,comments.created_at as date from users
            inner join comments on comments.user_id = users.user_id
            inner join posts on posts.post_id = comments.post_id
            where users.user_id='.$user_id.' and posts.user_id!='.$user_id.' 
            group by posts.post_id
            order by date desc');
        foreach ($posts as $post) {
            if ($post['user_id'] != $user_id) {
                userCommentsHTML($post, $user, true);
            } else {
                postHTML($post, true, false);
                $post_id = $post['post_id'];
                if (exists("select * from comments where post_id=$post_id and user_id=$user_id")) {
                    userCommentsHTML($post, $user, false);
                }
            }
        }
    }
    if ($tab_index == 1) {
        $posts = query('select * from posts where user_id='.$user_id);
        foreach ($posts as $post) {
            overviewPostHTML($post);
        }
    }

    if ($tab_index == 2) {
        $posts = query('select posts.* from users 
            inner join comments on comments.user_id = users.user_id 
            inner join posts on posts.post_id = comments.post_id
            where users.user_id='.$user_id.' group by posts.post_id');
        foreach($posts as $post) {
            userCommentsHTML($post, $user, true);
        }
    }

    if ($tab_index == 3) {
        $liked_posts = query('
            select * from post_likes
            inner join posts on posts.post_id = post_likes.post_id
            where post_likes.user_id = '.$user_id);
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