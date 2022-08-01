<?php require('require/header.php'); ?>
<main>
<h1>Welcome to <span class="green">Greendit</span></h1>
    <p>
        Greendit is a web application similar to Reddit.
        It is a place where you can post and comment on posts.
    </p>
    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dignissimos ex debitis veritatis magnam dolor ea esse,
        ratione iusto et aliquid sequi sint enim aspernatur facere tempore accusantium impedit quibusdam sunt!</p>

    <h2>Trending communities</h2>
    <ul class="trending">
    <?php
        require_once 'require/db_connect.php';
        $communities = query('
            select name, shortname, count(post_id) as num_posts from communities 
            inner join posts on communities.community_id = posts.community_id 
            group by communities.community_id order by num_posts desc limit 10');
        if (count($communities)) {
            foreach($communities as $community) {
                $community_name = $community['name'];
                $sub_name = $community['shortname'];
                $num_posts = $community['num_posts'];
                echo '
                    <li>
                        <div class="trend">
                            <div class="pfp">
                                <img src="resources/com.png" alt="">
                            </div>
                            <div class="main">
                                <a href="subs/'.$sub_name.'">s/'.$sub_name.'</a>
                                <p>'.$num_posts.' posts</p>
                            </div>
                        </div>
                    </li>
                ';
            }
        } else {
            echo "No communities found.";
        }
    ?>
    </ul>
    <h2>Active users</h2>
    <ul class="trending">
    <?php
        require_once 'require/db_connect.php';
        $users = query('
            select username, count(post_id) as num_posts from users 
            inner join posts on users.user_id = posts.user_id 
            group by users.user_id order by num_posts desc limit 10');
        if (count($users) > 0) {
            foreach($users as $user) {
                $username = $user['username'];
                $num_posts = $user['num_posts'];
                if ($num_posts > 0) {
                    echo '
                    <li>
                        <div class="trend">
                            <div class="pfp">
                                <img src="resources/pfps/'.$username.'.png">
                            </div>
                            <div class="main">
                                <a href="users/'.$username.'">'.$username.'</a>
                                <p>'.$num_posts.' posts</p>
                            </div>
                        </div>
                    <li>
                    ';
                }
            }
        }
    ?>
    </ul>

    <h2>Popular posts</h2>
    <div id="feed">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo '
                <div class="create-post">
                    <a href="users/'.$_SESSION['username'].'"><img class="user-pfp" src="resources/pfps/'.$_SESSION['username'].'.png"></a>
                    <button onclick="window.location=\'/greendit/post.php\'">Create post...</button>
                </div>
                ';
            }
            require 'require/feed.php';
            $sql = 'select * from posts order by post_id desc';
            $posts = query($sql);

            foreach ($posts as $post) {
                postHTML($post);
            }
        ?>
        <script src="js/feed.js"></script>
    </div>
</main>
<?php require('require/footer.php'); ?>