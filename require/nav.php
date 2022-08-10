<nav>
    <a href="index.php">Greendit</a>
    <input type="text" placeholder="search..." id="search" autocomplete="off">
    <?php
    session_start();
    if (isset($_SESSION['username'], $_SESSION['user_id'])) :
        $username = $_SESSION['username'];
    ?>
        <a href="users/<?= $username ?>">u/<?= $username ?></a>
        <a href="logout.php">Logout</a>
        <a href="post.php">Post</a>
        <a href="create.php">Create</a>';
    <?php else : ?>
        <a href="login.php">Login</a>
        <a href="register.php">SignUp</a>';
    <?php endif; ?>

</nav>
<div id="search-result__wrapper" class="hidden">
    <div id="search-result__content" class="article"></div>
</div>