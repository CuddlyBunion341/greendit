<nav>
    <a href="index.php">Greendit</a>
    <?php
        session_start();
        if (isset($_SESSION['username'],$_SESSION['user_id'])) {
            $username = $_SESSION['username'];
            echo '<a href="users/'.$username.'">u/'. $username . '</a>
            <a href="logout.php">Logout</a>
            <a href="post.php"><i class="fa-solid fa-plus"></i></a>';
        } else {
            echo '<a href="login.php">Login</a>
            <a href="register.php">SignUp</a>';
        }
    ?>
</nav>