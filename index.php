<?php require('templates/header.php'); ?>
<h1>Welcome to Greendit</h1>
    <p>
        Greendit is a web application similar to Reddit.
        It is a place where you can post and comment on posts.
    </p>
    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dignissimos ex debitis veritatis magnam dolor ea esse,
        ratione iusto et aliquid sequi sint enim aspernatur facere tempore accusantium impedit quibusdam sunt!</p>
    <div id="feed">
        <?php
            require 'config/db_connect.php';
            $sql = "SELECT * FROM posts ORDER BY post_id DESC";
            $result = $conn->query($sql);
            $posts = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($posts as $post) {
                echo '
                    <div class="post">
                        <h2>' . $post['title'] . '</h2>
                        <p>' . $post['content'] . '</p>
                    </div>
                ';
            }
        ?>
    </div>
<?php require('templates/footer.php'); ?>