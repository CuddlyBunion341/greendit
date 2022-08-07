<nav>
    <a href="index.php">Greendit</a>
    <input type="text" placeholder="search..." id="search">
    <?php
        session_start();
        if (isset($_SESSION['username'],$_SESSION['user_id'])) {
            $username = $_SESSION['username'];
            echo '<a href="users/'.$username.'">u/'. $username . '</a>
            <a href="logout.php">Logout</a>
            <a href="post.php">+</a>
            <a href="create.php">create</a>';
        } else {
            echo '<a href="login.php">Login</a>
            <a href="register.php">SignUp</a>';
        }
    ?>
</nav>
<div id="search-result__wrapper" class="hidden">
    <div id="search-result__content">
        Nothin yet
    </div>
</div>