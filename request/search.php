<?php
    if (!isset($_GET['q'])) {
        die('err');
    }

    require __DIR__ . '/../require/db_connect.php';
    
    $query = trim(htmlspecialchars($_GET['q']));

    // users
    $users = query("select * from users where username like '%$query%' limit 10");
    echo '<pre>';
    foreach($users as $user) {
        print_r($user);
    }

    // posts
    $posts = query("select * from posts where title like '%$query%' or content like '%query%' limit 10");

    foreach($posts as $post) {
        print_r($post);
    }

    // communities
    $subs = query("select * from communities where shortname like '%$query%' or name like '%$query%'");

    foreach($subs as $sub) {
        print_r($sub);
    }

    echo '</pre>'
?>