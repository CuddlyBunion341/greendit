<?php require('templates/header.php'); ?>
<main>
    <h1>User Info</h1>
    <?php
        echo $_SERVER['REQUEST_URI'];
        if (isset($_GET['name'])) {
            require 'config/db_connect.php';
            $sql = 'select * from users where username = "' . $_GET['name'] . '"';
            $result = $conn->query($sql);
            $user = $result->fetch_assoc();
            if ($result->num_rows > 0) {
                echo '
                    <div class="user-info">
                        <div class="user-pfp">
                            <img src="resources/pfp.png" alt="">
                        </div>
                        <div class="user-main">
                            <h2>' . $user['username'] . '</h2>
                        </div>
                    </div>
                ';
            } else {
                echo 'User not found.';
            }
        }
    ?>
</main>
<?php require('templates/footer.php'); ?>