<nav>
    <a href="index.php">Greendit</a>
    <input type="text" placeholder="search..." id="search" autocomplete="off">
    <?php
    session_start();
    if (isset($_SESSION['username'], $_SESSION['user_id'])) :
        $username = $_SESSION['username'];
    ?>
        <label for="check" class="menu-icon">
            &#9776;
        </label>
        <input type="checkbox" name="check" id="check">
        <div class="menu">
            <a href="post.php">Post</a>
            <a href="create.php">Create</a>
            <a href="logout.php">Logout</a>
            <a href="users/<?= $username ?>">u/<?= $username ?></a>
        </div>

    <?php else : ?>
        <a href="login.php">Login</a>
        <a href="register.php">SignUp</a>
    <?php endif; ?>

</nav>
<div id="search-result__wrapper" class="hidden">
    <div id="search-result__content" class="article"></div>
</div>