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
        require 'config/db_connect.php';
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
                            <img src="resources/pfp.png" alt="">
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
            $sql = 'select * from posts order by post_id desc';
            $result = $conn->query($sql);
            $posts = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($posts as $post) {
                // user
                $sql = 'select * from users where user_id = ' . $post['user_id'];
                $result = $conn->query($sql);
                $user = $result->fetch_assoc();
                // community
                $sql = 'select * from communities where community_id = ' . $post['community_id'];
                $result = $conn->query($sql);
                $community = $result->fetch_assoc();
                // comments
                $sql = 'select * from comments where post_id = ' . $post['post_id'];
                $result = $conn->query($sql);
                $comments = $result->fetch_all(MYSQLI_ASSOC);
                // date
                $date = $post['created_at'];
                $datediff = time() - strtotime($date);
                $date = round($datediff / (60 * 60 * 24));
                // likes
                $sql = 'select count(*) from post_likes where post_id = ' . $post['post_id'];
                $result = $conn->query($sql);
                $likes = $result->fetch_row()[0];
                // dislikes
                $sql = 'select count(*) from post_dislikes where post_id = ' . $post['post_id'];
                $result = $conn->query($sql);
                $dislikes = $result->fetch_row()[0];
                // total likes
                $totalLikes = $likes - $dislikes;
                // liked / disliked
                $liked = 0;
                $disliked = 0;
                if (isset($_SESSION['user_id'])) {
                    // mylike
                    $sql = 'select * from post_likes where post_id = ' . $post['post_id'] . ' AND user_id = ' . $_SESSION['user_id'];
                    $result = $conn->query($sql);
                    $liked = mysqli_num_rows($result);
                    // mydislike
                    $sql = 'select * from post_dislikes where post_id = ' . $post['post_id'] . ' AND user_id = ' . $_SESSION['user_id'];
                    $result = $conn->query($sql);
                    $disliked = mysqli_num_rows($result);
                }
                echo '
                    <div class="post" data-id="'.$post['post_id'].'">
                            <div class="left">
                                <div class="arrow-wrapper">
                                    <button class="upvote"><img src="resources/upvote'.($liked?'_full':'').'.svg"></button>
                                    <span class="like-count">'.$totalLikes.'</span>
                                    <button class="downvote"><img src="resources/downvote'.($disliked?'_full':'').'.svg"></button>
                                </div>
                            </div>
                            <div class="right">
                                <div class="head">
                                    <a href="subs/'.$community['shortname'].'">s/'.$community['shortname'].'</a>&nbsp;
                                    posted by&nbsp;
                                    <a href="user/'.$user['username'].'">
                                    u/'. $user['username'] . '
                                    </a>'. $date . ' days(s) ago
                                </div>
                                <h2>' . $post['title'] . '</h2>
                                <p>' . $post['content'] . '</p>
                                <div class="footer">
                                    <button class="comment-btn">' . count($comments) . ' comments</button>
                                    <button class="save-btn">Save</button>
                                    <button class="share-btn">Share</button>
                                    <button class="report-btn">Report</button>
                                </div>  
                        </div>
                    </div>
                ';
            }
        ?>
        <script src="scripts/js/feed.js"></script>
    </div>
</main>
<?php require('templates/footer.php'); ?>