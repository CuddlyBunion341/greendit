<?php require 'require/header.php'; ?>
<?php
    require __DIR__ . '/require/db_connect.php';
    require __DIR__ . '/require/feed.php';
    $tabs = array('all','posts','comments','subs','users');
    $tab_index = isset($_GET['tab']) ? array_search($_GET['tab'],$tabs) : 0;

    function activeTab($index) {
        global $tab_index;
        if ($index == $tab_index) {
            return ' class="active" ';
        }
        return ' ';
    }
    $query = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';
?>
<nav class="tabbs">
    <a <?= activeTab(0); ?> href="search/<?= $query ?>/all">All</a>
    <a <?= activeTab(1); ?> href="search/<?= $query ?>/posts">Posts</a>
    <a <?= activeTab(2); ?> href="search/<?= $query ?>/comments">Comments</a>
    <a <?= activeTab(3); ?> href="search/<?= $query ?>/subs">Communities</a>
    <a <?= activeTab(4); ?> href="search/<?= $query ?>/users">Users</a>
</nav>
<main class="single">
    <div id="feed">
        <?php
            if ($tab_index == 0) {
                // todo: sidebar with users and communities
            }
            if ($tab_index == 1 || $tab_index == 0) {
                $results = query('select * from posts where title like "%'.$query.'%" or content like "%'.$query.'%"');
                foreach($results as $result) {
                    overviewPostHTML($result);
                }
            }
            if ($tab_index == 2 || $tab_index == 0) {
                $results = query('select * from comments where content like "%'.$query.'%"');
                foreach($results as $result) {
                    overview_commentHTML($result);
                }
            }
            if ($tab_index == 3) {
                $results = query('select * from communities where shortname like "%'.$query.'%" or name like "%'.$query.'%"');
                foreach($results as $result) {
                    // todo community overview
                }
            }
            if ($tab_index == 4) {
                $results = query('select * from users where username like "%'.$query.'%"');
                foreach($results as $result) {
                    // todo user overview
                }
            }
        ?>
    </div>
</main>

<?php require 'require/footer.php'; ?>

