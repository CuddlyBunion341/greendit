<?php
function getPostData($post) {
    extract($post);
    // ---- Objects --------------------------------------------------------------------------
    $user = row('select * from users where user_id=' . $user_id);
    $community = row('select * from communities where community_id=' . $community_id);
    $media = query('select * from post_media where post_id=' . $post_id);
    // ---- Stats ----------------------------------------------------------------------------
    $comments = rows('select * from comments where post_id=' . $post_id);
    $likes = rows('select * from post_likes where post_id=' . $post_id);
    $dislikes = rows('select * from post_dislikes where post_id=' . $post_id);
    $total_likes = $likes - $dislikes;
    $age = formatDate($created_at);
    // ---- User data ------------------------------------------------------------------------
    $liked = $disliked = $saved = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $liked = exists("select * from post_likes where post_id=$post_id and user_id=$session_user");
        $disliked = exists("select * from post_dislikes where post_id=$post_id and user_id=$session_user");
        $saved = exists("select * from saved_posts where post_id=$post_id and user_id=$session_user");
    }
    return array(
        'username' => $user['username'],
        'sub' => $community['shortname'],
        'removed' => $status == 'removed',
        'hash' => $hash,
        'title' => $title,
        'content' => $content,
        'media' => $media,
        'likes' => $total_likes,
        'comments' => $comments,
        'date' => $created_at,
        'age' => $age,
        'liked' => $liked,
        'disliked' => $disliked,
        'saved' => $saved
    );
}
function postHTML($post) {
    $post_data = getPostData($post);
    extract($post_data);
    if ($removed) {
        $title = '[Removed] ';
        $content = 'This post has been removed.';
    }
}