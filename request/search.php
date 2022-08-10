<?php
if (!isset($_GET['q'])) {
    http_response_code(400);
    exit('Missing query');
}

require __DIR__ . '/../require/db_connect.php';
require __DIR__ . '/../require/feed.php';

$query = trim(htmlspecialchars($_GET['q']));
?>

<h2>Searching for: <span class="query"><?= $query ?></span></h2>
<?php
$users = query("select * from users where username like '%$query%' limit 5");
if ($users) :
?>
    <p>Users</p>
    <ul>
        <?php foreach ($users as $user) : ?>
            <li>
                <a href="users/<?= $user['username'] ?>">
                    <img class="pfp small" src="resources/pfps/<?= $user['username'] ?>.png" alt="<?= $user['username'] ?>">
                    u/<?= $user['username'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php
$subs = query("select * from communities where shortname like '%$query%' or name like '%$query%' limit 5");
if ($subs) :
?>
    <p>Communities</p>
    <ul>
        <?php foreach ($subs as $sub) : ?>
            <li class="multirow">
                <a href="subs/<?= $sub['shortname'] ?>">
                    <img class="pfp small" src="resources/com.png" alt="<?= $sub['shortname'] ?>">
                    <div class="main">
                        <p><?= $sub['name'] ?></p>
                        <p>s/<?= $sub['shortname'] ?></p>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php
$posts = query("select * from posts inner join communities on posts.community_id = communities.community_id where title like '%$query%' limit 5");
if ($posts) :
?>
    <p>Posts</p>
    <ul>
        <?php foreach ($posts as $post) : ?>
            <li>
                <a href="subs/<?= $post['shortname'] ?>/posts/<?= $post['hash'] ?>">
                    <?= $post['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php elseif (!$posts && !$users && !$subs) : ?>
    <p>No results found</p>
<?php endif; ?>