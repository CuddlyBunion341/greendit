<nav>
    <a href="index.php" class="green">Greendit</a>
    <?php
        session_start();
        if (isset($_SESSION['username'],$_SESSION['user_id'])) {
            $username = $_SESSION['username'];
            echo '<a href="user/'.$username.'">u/'. $username . '</a>';
            echo '<a href="logout.php">Logout</a>';
        } else {
            echo '<a href="login.php">Login</a>';
            echo '<a href="register.php">SignUp</a>';
        }
    ?>
</nav>