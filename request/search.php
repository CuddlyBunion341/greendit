<?php
if (!isset($_GET['q'])) {
    die('err');
}

require __DIR__ . '/../require/db_connect.php';
require __DIR__ . '/../require/feed.php';

$query = trim(htmlspecialchars($_GET['q']));
?>
<p>Users</p>
<ul>
    <?php
    $users = query("select * from users where username like '%$query%' limit 5");
    foreach ($users as $user) : ?>
        <li>
            <a href="user/<?= $user['username'] ?>">
                u/<?= $user['username'] ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<p>Communities</p>
<ul>
    <?php
    $subs = query("select * from communities where shortname like '%$query%' or name like '%$query%' limit 5");

    foreach ($subs as $sub) : ?>
        <li>
            <a href="subs/<?= $sub['shortname']?>">
                <?= $sub['name'] ?><br>
                s/<?= $sub['shortname'] ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<p>Posts</p>
<ul>
    <?php
    $posts = query("select * from posts inner join communities on posts.community_id = communities.community_id where title like '%$query%' limit 5");
    foreach ($posts as $post) : ?>
    <li>
        <a href="subs/<?= $post['shortname'] ?>/posts/<?= $post['hash'] ?>">
        <?= $post['title'] ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>