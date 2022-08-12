<?php require 'require/header.php'; ?>
<?php
    require __DIR__ . '/require/db_connect.php';
    require __DIR__ . '/require/feed.php';
    $tabs = array('all','posts','comments','subs','users');
    $tab_index = isset($_GET['tab']) ? array_search($_GET['tab'],$tabs) : 0;

    function active($index) {
        global $tab_index;
        if ($index == $tab_index) {
            return ' class="active" ';
        }
        return ' ';
    }
    $query = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';
?>
<nav class="tabbs">
    <a <?= active(0); ?> href="search/<?= $query ?>/all">All</a>
    <a <?= active(1); ?> href="search/<?= $query ?>/posts">Posts</a>
    <a <?= active(2); ?> href="search/<?= $query ?>/comments">Comments</a>
    <a <?= active(3); ?> href="search/<?= $query ?>/subs">Communities</a>
    <a <?= active(4); ?> href="search/<?= $query ?>/users">Users</a>
</nav>
<main>

</main>

<?php require 'require/footer.php'; ?>

