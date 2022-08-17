<?php
require __DIR__ . '/../require/db_connect.php';
require __DIR__ . '/../require/feed.php';

session_start(); // very important

$start = 0;
$limit = 10;
$sub = '*';

if (isset($_POST['start'])) {
    $start = (int)$_POST['start'];
}
if (isset($_POST['limit'])) {
    $limit = (int)$_POST['limit'];
}
if (isset($_POST['sub'])) {
    $sub = htmlspecialchars($_POST['sub']);
}

if ($sub == '*') {
    $posts = query("
        select * from posts 
        order by posts.created_at desc 
        limit $start, $limit");
} else {
    $posts = query("
        select * from posts
        inner join communities on communities.community_id = posts.community_id
        where communities.shortname = '$sub'
        order by posts.created_at desc 
        limit $start, $limit");
}

if (count($posts) > 0) {
    foreach ($posts as $post) {
        postHTML($post);
    }
} else {
    http_response_code(204);
    exit('<p>No posts found</p>');
}