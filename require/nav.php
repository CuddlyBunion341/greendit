<nav>
    <ul>
        <li><a href="index.php">Greendit</a></li>
        <?php $val = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : ''; ?>
        <li class="search-wrapper"><input type="search" placeholder="search..." id="search" autocomplete="off" value="<?= $val ?>"></li>
        <?php
        session_start();
        if (isset($_SESSION['username'], $_SESSION['user_id'])) :
            $username = $_SESSION['username'];
        ?>
            <li class="menu-icon"><label for="check">
                    &#9776;
                </label></li>
            <input type="checkbox" name="check" id="check">
            <ul class="menu">
                <li><a href="post.php">Post</a></li>
                <li><a href="create.php">Create</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="users/<?= $username ?>">u/<?= $username ?></a></li>
            </ul>

        <?php else : ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">SignUp</a></li>
        <?php endif; ?>
    </ul>
</nav>