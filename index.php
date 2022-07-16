<?php require('templates/header.php'); ?>
<main>
<h1>Welcome to <span class="green">Greendit</span></h1>
    <p>
        Greendit is a web application similar to Reddit.
        It is a place where you can post and comment on posts.
    </p>
    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dignissimos ex debitis veritatis magnam dolor ea esse,
        ratione iusto et aliquid sequi sint enim aspernatur facere tempore accusantium impedit quibusdam sunt!</p>

    <h2>Popular posts</h2>
    <div id="feed">
        <?php
            require 'config/db_connect.php';
            $sql = "SELECT * FROM posts ORDER BY post_id DESC";
            $result = $conn->query($sql);
            $posts = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($posts as $post) {
                // user
                $sql = "SELECT * FROM users WHERE user_id = " . $post['user_id'];
                $result = $conn->query($sql);
                $user = $result->fetch_assoc();
                // comments
                $sql = "SELECT * FROM comments WHERE post_id = " . $post['post_id'];
                $result = $conn->query($sql);
                $comments = $result->fetch_all(MYSQLI_ASSOC);
                // date
                $date = $post['created_at'];
                $datediff = time() - strtotime($date);

                $date = round($datediff / (60 * 60 * 24));
                // todo: likes
                echo '
                    <div class="post">
                            <div class="left">
                                <div class="arrow-wrapper">
                                    <button class="upvote"><img src="resources/upvote_full.svg"></button>
                                    <span class="like-count">0</span>
                                    <button class="downvote"><img src="resources/upvote.svg"></button>
                                </div>
                            </div>
                            <div class="right">
                                <div class="head">
                                    <a href="subs/main">s/main</a>&nbsp;
                                    posted by&nbsp;
                                    <a href="user/'.$user['username'].'">
                                    u/'. $user['username'] . '
                                    </a>'. $date . ' days(s) ago
                                </div>
                                <h2>' . $post['title'] . '</h2>
                                <p>' . $post['content'] . '</p>
                                <div class="footer">
                                    <button>' . count($comments) . ' comments</button>
                                    <button>Save</button>
                                    <button>Share</button>
                                    <button>Report</button>
                                </div>  
                        </div>
                    </div>
                ';
            }
        ?>
    </div>
</main>
<?php require('templates/footer.php'); ?>