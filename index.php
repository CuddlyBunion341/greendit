<?php require('templates/header.php'); ?>
<main>
<h1>Welcome to <span class="green">Greendit</span></h1>
    <p>
        Greendit is a web application similar to Reddit.
        It is a place where you can post and comment on posts.
    </p>
    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dignissimos ex debitis veritatis magnam dolor ea esse,
        ratione iusto et aliquid sequi sint enim aspernatur facere tempore accusantium impedit quibusdam sunt!</p>

    <h2>Trending communities</h2>
    <?php
        require_once 'config/db_connect.php';
        $sql = 'select * from communities limit 5';
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $community_id = $row['community_id'];
                $community_name = $row['name'];
                $sub_name = $row['shortname'];
                $sql = 'select count(*) as num_posts from posts where community_id='.$community_id;
                $result2 = $conn->query($sql);
                $row2 = $result2->fetch_assoc();
                $num_posts = $row2['num_posts'];
                echo '
                    <div class="community">
                        <div class="community-pfp">
                            <img src="resources/com.png" alt="">
                        </div>
                        <div class="main">
                            <a href="subs/'.$sub_name.'">s/'.$sub_name.'</a>
                            <p>'.$num_posts.' posts</p>
                        </div>
                    </div>
                ';
            }
        } else {
            echo "No communities found.";
        }
    ?>

    <h2>Popular posts</h2>
    <div id="feed">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo '
                <div class="create-post">
                    <a href="user/'.$_SESSION['username'].'"><img class="user-pfp" src="resources/pfp.png"></a>
                    <button onclick="window.location=\'/greendit/post.php\'">Create post...</button>
                </div>
                ';
            }
            require 'util/feed.php';
            $sql = 'select * from posts order by post_id desc';
            $posts = query($sql);

            foreach ($posts as $post) {
                postHTML($post);
            }
        ?>
        <script src="scripts/js/feed.js"></script>
    </div>
</main>
<?php require('templates/footer.php'); ?>