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

function post_footer($saved = false, $comments = 0) {
    ?>
    <section class="footer">
        <button aria-label="comment" name="comment-btn" class="comment-btn">
            <?= icon('comment') ?>
            <?= $comments ?> comments
        </button>
        <button aria-label="save" name="save-btn" class="save-btn<?= active($saved) ?>">
            <?= icon('bookmark') ?>
        </button>
        <button aria-label="share" name="share-btn" class="share-btn">
            <?= icon('share') ?> Share
        </button>
    </section>
    <?php
}

function postHTML($post, $show_community = true, $show_user = true) {
    $post_data = getPostData($post);
    extract($post_data);
    if ($removed) return;
?>
    <article class="post" data-hash="<?= $hash ?>" data-sub="<?= $sub ?>">
        <section class="left">
            <?= arrow_wrapper($liked, $disliked, $likes, false, false) ?>
        </section>
        <section class="right">
            <header class="head">
                <?php if ($show_community) :
                    if (!$show_user) echo 'posted in '; ?>
                    <a href="subs/'<?= $sub ?>">s/<?= $sub ?></a>&nbsp;
                <?php endif;
                if ($show_user) : ?>
                    posted by
                    <a href="users/<?= $username ?>">u/<?= $username ?></a>
                <?php endif; ?>
                <?= $age ?>
            </header>
            <h2><?= $title ?></h2>
            <?php if (count($media) == 0) : ?>
                <p><?= markdownify($content) ?></p>
            <?php elseif (count($media) > 0) :
                $file_name = $media[0]['file_name'];
                $extension = explode('.', $file_name)[1];
                $images = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                $videos = array('mp4', 'webm', 'ogv');
                if (in_array($extension, $images)) : ?>
                    <div class="image-collage">
                        <?php foreach ($media as $image) : 
                            $path = 'resources/uploads/' . $image['file_name'];
                            if (!file_exists($path)) $path = 'resources/not_found.png';
                        ?>
                            <img src="<?= $path ?>" alt="TODO: media caption">
                        <?php endforeach; ?>
                    </div>
                <?php elseif (in_array($extension, $videos)) : ?>
                    <div class="video-container">
                        <video controls>
                            <source src="resources/uploads/<?= $media[0]['file_name'] ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
            <?php
                endif;
            endif;
            post_footer($saved, $comments);
            ?>
        </section>
    </article>
<?php
}

function overviewPostHTML($post) {
    $post_data = getPostData($post);
    extract($post_data);
    if ($removed) return;
    ?>
        <article class="post overview" data-hash="<?= $hash ?>" data-sub="<?= $sub ?>">
            <section class="left">
                <?= arrow_wrapper($liked, $disliked, $likes, false, false) ?>
            </section>
            <section class="thumb">
                <?php
                    if (!$media) {
                        echo icon('post');
                    } else {
                        $file_name = $media[0]['file_name'];
                        $file_hash = explode('.', $file_name)[0];
                        $thumb_path = 'resources/uploads/thumbnails/' . $file_hash . '.jpg';
                        if (!file_exists($thumb_path)) $thumb_path = 'resources/not_found.png';
                        echo '<img src="' . $thumb_path . '" alt="TODO: media caption">';
                    }
                ?>
            </section>
            <section class="right">
                <header class="head">
                    posted in <a href="subs/<?= $sub ?>">s/<?= $sub ?></a>&nbsp; <?= $age ?>
                </header>
                <h2><?= $title ?></h2>
                <?= post_footer($saved, $comments) ?>
            </section>
        </article>
    <?php
}