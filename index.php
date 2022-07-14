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
                // $date = date("d.m.Y", strtotime($date));
                $now = time(); // or your date as well
                $your_date = strtotime($date);
                $datediff = $now - $your_date;

                $date = round($datediff / (60 * 60 * 24));
                // todo: likes
                echo '
                    <div class="post">
                        <h2>' . $post['title'] . '</h2>
                        <p>' . $post['content'] . '</p>
                        <p> by ' . $user['username'] . ' ' . $date . ' day(s) ago</p>
                        <p>' . count($comments) . ' comments</p>
                    </div>
                ';
            }
        ?>
    </div>
</main>
<?php require('templates/footer.php'); ?>