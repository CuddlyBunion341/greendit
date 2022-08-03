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
        $posts = query('select * from posts where user_id = '.$user['user_id']);
        foreach ($posts as $post) {
            $tab_index == 0 ? postHTML($post,true,false) : overviewPostHTML($post);
        }
        if (!$posts && $tab_index == 1) {
            echo '<p>No posts yet!</p>';
        }
    }
    if ($tab_index == 0) {
        $comments = query('select * from comments where user_id = '.$user['user_id']);
        foreach ($comments as $comment) {
            overviewCommentHTML($comment);
        }
        if (!$comments && $tab_index == 2) {
            echo '<p>No comments yet!</p>';
        }
    }

    if ($tab_index == 2) {
        $posts = query('select posts.user_id, posts.post_id, posts.hash, posts.title, communities.shortname as sub from users inner join comments on comments.user_id = users.user_id inner join posts on posts.post_id = comments.post_id inner join communities on posts.community_id = communities.community_id where users.user_id='.$user['user_id'].' group by posts.hash;');
        foreach($posts as $post) {
            $post_comments = query('select * from comments where post_id='.$post['post_id']);
            $author = getField('select username from users where user_id='.$post['user_id']);
            $sub = $post['sub'];
            $post_hash = $post['hash'];
            $title = $post['title'];
            echo '
            <article class="overview-comment">
                <section class="overview-comment__head">
                    '.file_get_contents(__DIR__.'/resources/icons/comment2.svg').'
                    '.linkHTML('users/'.$username,$username).'
                    commented on 
                    '.linkHTML('subs/'.$sub.'/posts/'.$post_hash,$title).'
                    in '.linkHTML('subs/'.$sub,'s/'.$sub).'
                    Posted by '.linkHTML('users/'.$author,$author).'
                </section>
                <section class="overview-comment__comments">
            ';
            foreach ($post_comments as $comment) {
                if ($comment['user_id'] != $user_id) continue;
                $indent = 1;
                $alias = $comment;
                // calculate indent
                while ($alias['parent_id']) {
                    $exists = false;
                    foreach ($post_comments as $other) {
                        if ($other['comment_id'] == $alias['parent_id']) {
                            $alias = $other;
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        exit ('ERROR FETCHING COMMENT');
                    }
                    $indent++;
                    $post_comments;
                }
                echo '
                <div class="comment">
                    '.str_repeat('<div class="indent">',$indent).'
                        <div class="comment__head">
                            '.linkHTML('users/'.$username,$username).'
                            '.formatDate($comment['created_at']).'
                        </div>
                        <p>'.$comment['content'].'</p>
                    '.str_repeat('</div>',$indent).'
                </div>';
            }
            echo '
                </section>
            </article>';
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