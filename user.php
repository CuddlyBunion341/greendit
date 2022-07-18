<?php require('templates/header.php'); ?>
<main>
    <?php
        function plural($word,$num) {
            if ($num == 1) {
                return $word;
            }
            return $word.'s';
        }

        if (isset($_GET['name'])) {
            require_once 'config/db_connect.php';
            $user = row('select * from users where username = "' . $_GET['name'] . '"');
            if ($user) {
                $date = $user['created_at'];
                $datediff = time() - strtotime($date);
                $date = round($datediff / (60 * 60 * 24));
                $posts = rows('select * from posts where user_id = ' . $user['user_id']);
                $comments = rows('select * from comments where user_id = ' . $user['user_id']);
                $user_url = 'user/' . $user['username'];
                echo '
                    <div class="user-info">
                        <div class="user-pfp">
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
            } else {
                echo 'User not found.';
            }
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
            <a'.active(0).'href="user/'.$user['username'].'/overview">Overview</a>
            <a'.active(1).'href="user/'.$user['username'].'/posts">Posts</a>
            <a'.active(2).'href="user/'.$user['username'].'/comments">Comments</a>
            <a'.active(3).'href="user/'.$user['username'].'/likes">Likes</a>
        </nav>
        ';

        require_once 'util/feed.php';
        if ($tab_index == 0 || $tab_index == 1)  {
            $posts = query('select * from posts where user_id = ' . $user['user_id']);
            foreach ($posts as $post) {
                postHTML($post);
            }
        }
        if ($tab_index == 0 || $tab_index == 2) {
            $comments = query('select * from comments where user_id = ' . $user['user_id']);
            foreach ($comments as $comment) {
                commentHTML($comment);
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
                postHTML($post);
            }
        }
    ?>
</main>
<?php require('templates/footer.php'); ?>