<?php
function get_user_data($user) {
    extract($user);
    // ---- Objects --------------------------------------------------------------------------
    $followers = query('select * from followers where user_id=' . $user_id);
    // ---- Stats ----------------------------------------------------------------------------
    $posts = rows('select * from posts where user_id=' . $user_id);
    $comments = rows('select * from comments where user_id=' . $user_id);
    // ---- User data ------------------------------------------------------------------------
    $following = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $following = exists("select * from followers where user_id=$user_id and follower_id=$session_user");
    }
    return array(
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'created_at' => $created_at,
        'posts' => $posts,
        'comments' => $comments,
        'followers' => $followers,
        'follower_count' => count($followers),
        'following' => $following
    );
}

function overview_userHTML($user) {
    $user_data = get_user_data($user);
    extract($user_data);

?>
    <article class="overview-user" data-name="<?= $username ?>">
        <img src="resources/pfps/<?= $username ?>.png" alt="<?= $username ?>" class="pfp small">
        <div class="main">
            <p><?= $username ?></p>
            <a href="users/<?= $username ?>">u/<?= $username ?></a>
        </div>
        <button class="follow-btn<?= active($following) ?>" data-name="<?= $username ?>"></button>
    </article>
<?php
}
?>