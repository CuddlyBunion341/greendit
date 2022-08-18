<?php
function get_comment_data($comment) {
    extract($comment);
    // ---- Objects --------------------------------------------------------------------------
    $user = row('select * from users where user_id=' . $user_id);
    $post = row('select * from posts where post_id=' . $post_id);
    $community = row('select * from communities where community_id=' . $post['community_id']);
    $post_author = row('select * from users where user_id=' . $post['user_id']);
    // ---- Stats ----------------------------------------------------------------------------
    $likes = rows('select * from comment_likes where comment_id=' . $comment_id);
    $dislikes = rows('select * from comment_dislikes where comment_id=' . $comment_id);
    $total_likes = $likes - $dislikes;
    $age = formatDate($created_at);
    // ---- User data ------------------------------------------------------------------------
    $liked = $disliked = $saved = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $liked = exists("select * from comment_likes where comment_id=$comment_id and user_id=$session_user");
        $disliked = exists("select * from comment_dislikes where comment_id=$comment_id and user_id=$session_user");
        $saved = exists("select * from saved_comments where comment_id=$comment_id and user_id=$session_user");
    }
    return array(
        'username' => $user['username'],
        'content' => $content,
        'removed' => $status == 'removed',
        'hash' => $hash,
        'sub' => $community['shortname'],
        'post' => $post,
        'date' => $created_at,
        'age' => $age,
        'title' => $post['title'],
        'post_author' => $post_author['username'],
        'likes' => $total_likes,
        'liked' => $liked,
        'disliked' => $disliked,
        'saved' => $saved
    );
}
function post_commentHTML($comment) {
    $comment_data = get_comment_data($comment);
    extract($comment_data);
    if ($removed) return;
?>
    <article class="comment" data-hash="<?= $hash ?>" id="comment-'<?= $hash ?>">
        <section class="comment__header">
            <a href="users/<?= $username ?>">
                <img src="resources/pfps/<?= $username ?>.png" class="pfp small"><?= $username ?></a>
            <span class="light"><?= $age ?></span>
        </section>
        <p class="comment__content"><?= $content ?></p>
        <section class="comment__footer">
            <?= arrow_wrapper($liked, $disliked, $likes, true, $removed) ?>
            <button aria-label="comment" name="comment-btn" name="reply" class="comment-btn">Reply</button>
            <button aria-label="save" name="save-btn" name="save" class="save-btn<?= active($saved) ?>"></button>
            <button aria-label="share" name="share-btn" name="share" class="share-btn">Share</button>
        </section>
    </article>
<?php
}
?>