<?php
    require '../config/db_connect.php';
    $sql = 'SELECT COUNT(*) FROM post_likes WHERE post_id = ' . 1;
    $result = $conn->query($sql);
    $likes = $result->fetch_row()[0];
    // dislikes
    $sql = 'SELECT COUNT(*) FROM post_dislikes WHERE post_id = ' . 1;
    $result = $conn->query($sql);
    $dislikes = $result->fetch_row()[0];

    echo $likes . '<br>';
    echo $dislikes . '<br>';
?>