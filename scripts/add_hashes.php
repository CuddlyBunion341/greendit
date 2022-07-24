<?php
    function random_string($len=6) {
        $random = '';
        for ($i = 0; $i < $len; $i++) {
            $random .= chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }
    
    function unique_hash() {
        do {
            $rand = random_string();
            $posts = rows('select * from posts where hash = \''.$rand.'\'');
            $comments = rows('select * from comments where hash = \''.$rand.'\'');
        } while ($posts != 0 || $comments != 0 || strpos($rand,'sex'));
        return $rand;
    }

    require_once('../require/db_connect.php');
    $posts = query('select * from posts');
    foreach ($posts as $post) {
        $rand = unique_hash();
        echo $rand;
        echo '<br>';
        execute('update posts set hash = \'' . $rand . '\' where post_id = '.$post['post_id']);
    }
    $comments = query('select * from comments');
    foreach ($comments as $comment) {
        $rand = unique_hash();
        echo $rand;
        echo '<br>';
        execute('update comments set hash = \'' . $rand . '\' where comment_id = '.$comment['comment_id']);
    }
?>