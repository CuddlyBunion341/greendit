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
        'post_date' => $post['created_at'],
        'post_hash' => $post['hash'],
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
    <article class="comment" data-hash="<?= $hash ?>" id="comment-<?= $hash ?>">
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

function overview_commentHTML($comment) {
    $comment_data = get_comment_data($comment);
    extract($comment_data);
    ?>
        <article class="overview-comment" data-hash="<?= $post_hash ?>">
            <section class="overview-comment__head">
                <?= linkHTML('subs/' . $sub, "s/$sub") ?>
                Posted by <?= linkHTML('users/' . $post_author, $post_author) ?>
                <?= formatDate($post_date) ?>
            </section>
            <section class="overview-comment__title">
                <h2><?= $title ?></h2>
            </section>
            <section class="overview-comment__body" data-hash="<?= $hash ?>">
                <div class="comment__head">
                    <?= linkHTML('users/' . $username, $username) ?>
                    <?= formatDate($date) ?>
                </div>
                <div class="comment__body">
                    <p><?= $content ?></p>
                </div>
            </section>
        </article>
    <?php
}

function user_commentsHTML($post, $user, $head = true) {
    $post_comments = query('select * from comments where post_id=' . $post['post_id']);
    if (!$post_comments) return;

    $username = $user['username'];
    $sub = getField('select shortname from communities where community_id=' . $post['community_id']);
    echo '<article class="user-comment-wrapper' . active(!$head, 'beheaded') . '">';
    // ---- Header ---------------------------------------------------------------------------
    if ($head) :
        $author = getField('select username from users where user_id=' . $post['user_id']);
        $hash = $post['hash'];
        $title = $post['title'];
    ?>
        <section class="user-comment-wrapper__head">
            <?= icon('comment2') ?>
            <?= linkHTMl('users/' . $username, $username) ?>
            commented on
            <?= linkHTML('subs/' . $sub . '/posts/' . $hash, $title) ?>
            in <?= linkHTML('subs/' . $sub, 's/' . $sub) ?>
            Posted by <?= linkHTML('users/' . $author, 'u/' . $author) ?>
        </section>
    <?php
    endif;
    // ---- Body -----------------------------------------------------------------------------
    echo '<section class="user-comment-wrapper__comments" data-hash="' . $post['hash'] . '" data-sub="' . $sub . '">';
    foreach ($post_comments as $comment) :
        if ($comment['user_id'] != $user['user_id']) continue;
        if ($comment['status'] == 'removed') continue;
        $indent = 1;
        $alias = $comment;
        while ($alias['parent_id']) {
            foreach ($post_comments as $other) {
                if ($other['comment_id'] == $alias['parent_id']) {
                    $alias = $other;
                    $indent++;
                    break;
                }
            }
        }
    ?>
        <div class="user-comment" data-hash="<?= $comment['hash'] ?>">
            <?= str_repeat('<div class="indent">', $indent) ?>
            <div class="user-comment__head">
                <?= linkHTML('users/' . $username, $username) ?>
                <?= formatDate($comment['created_at']) ?>
            </div>
            <p><?= $comment['content'] ?></p>
            <?= str_repeat('</div>', $indent) ?>
        </div>
<?php
    endforeach;
    echo '
    </section>
</article>';
}
?>