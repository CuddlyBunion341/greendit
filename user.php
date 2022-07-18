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
            require 'config/db_connect.php';
            $sql = 'select * from users where username = "' . $_GET['name'] . '"';
            $result = $conn->query($sql);
            $user = $result->fetch_assoc();
            if ($result->num_rows > 0) {
                $date = $user['created_at'];
                $datediff = time() - strtotime($date);
                $date = round($datediff / (60 * 60 * 24));
                // posts
                $sql = 'select * from posts where user_id = ' . $user['user_id'];
                $result = $conn->query($sql);
                $posts = $result->num_rows;
                // comments
                $sql = 'select * from comments where user_id = ' . $user['user_id'];
                $result = $conn->query($sql);
                $comments = $result->num_rows;

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
    ?>
</main>
<?php require('templates/footer.php'); ?>