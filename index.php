<?php require('require/header.php'); ?>
<main class="multicol">
    <aside>
        <article class="titled">
            <h1>About Greendit</h1>
            <p>Greendit is lighweight and open source version of reddit. This website was built from scratch without any frameworks or external libraries and uses PHP and a MySQL database for its backend. It was created by CuddlyBunion341</p>
            <ul class="socials">
                <li><a href="https://github.com/CuddlyBunion341/greendit">
                    <i class="fa-brands fa-github"></i>
                </a></li>
                <li><a href="https://www.youtube.com/channel/UC8EszsyKVlEzu7ftpfZSVrQ">
                    <i class="fa-brands fa-youtube"></i>
                </a></li>
                <li><a href="#">
                    <i class="fa-brands fa-discord"></i>
                </a></li>
                <li><a href="mailto:00cb341@gmail.com">
                    <i class="fa-regular fa-envelope"></i>
                </a></li>
            </ul>
        </article>
        <article class="titled">
            <h1>Trending communities</h1>
            <ul class="trending">
                <?php
                require_once 'require/db_connect.php';
                $communities = query('
            select name, shortname, count(post_id) as num_posts from communities 
            inner join posts on communities.community_id = posts.community_id 
            group by communities.community_id order by num_posts desc limit 10');
                if (count($communities)) {
                    foreach ($communities as $community) {
                        $community_name = $community['name'];
                        $sub_name = $community['shortname'];
                        $num_posts = $community['num_posts'];
                        echo '
                    <li>
                        <div class="trend">
                            <img class="pfp small" src="resources/com.png" alt="' . $sub_name . '">
                            <div class="main">
                                <a href="subs/' . $sub_name . '">s/' . $sub_name . '</a>
                                <p>' . $num_posts . ' posts</p>
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
        </article>
        <article class="titled">
            <h1>Active users</h1>
            <ul class="trending">
                <?php
                require_once 'require/db_connect.php';
                $users = query('
            select username, count(post_id) as num_posts from users 
            inner join posts on users.user_id = posts.user_id 
            group by users.user_id order by num_posts desc limit 10');
                if (count($users) > 0) {
                    foreach ($users as $user) {
                        $username = $user['username'];
                        $num_posts = $user['num_posts'];
                        if ($num_posts > 0) {
                            echo '
                    <li>
                        <div class="trend">
                            <img class="pfp small" src="resources/pfps/' . $username . '.png" alt="' . $username . '">
                            <div class="main">
                                <a href="users/' . $username . '">' . $username . '</a>
                                <p>' . $num_posts . ' posts</p>
                            </div>
                        </div>
                    </li>
                    ';
                        }
                    }
                }
                ?>
            </ul>
        </article>
    </aside>

    <div id="feed">
        <article>
            <h2>Popular posts</h2>
        </article>
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '
                <article class="create-post">
                    <a href="users/' . $_SESSION['username'] . '"><img class="user-pfp" src="resources/pfps/' . $_SESSION['username'] . '.png"></a>
                    <button onclick="window.location=\'/greendit/post.php\'">Create post...</button>
                </article>
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