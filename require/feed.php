<?php
if (!isset($_SESSION)) session_start();
if (!isset($conn)) require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/util.php';
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
function getCommentData($comment) {
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
function getGreenditData() {
    $users = rows('select * from users');
    $posts = rows('select * from posts');
    $comments = rows('select * from comments');
    $communities = rows('select * from communities');
    $post_media = rows('select * from post_media');
    $post_likes = rows('select * from post_likes');
    $comment_likes = rows('select * from comment_likes');
    return array(
        'users' => $users,
        'posts' => $posts,
        'comments' => $comments,
        'communities' => $communities,
        'post_media' => $post_media,
        'post_likes' => $post_likes,
        'total_likes' => $post_likes + $comment_likes,
        'comment_likes' => $comment_likes
    );
}
function getCommunityData($community_name) {
    // ---- Objects --------------------------------------------------------------------------
    $community = row('select * from communities where shortname="' . $community_name . '"');
    extract($community);
    $owner = row('select * from users where user_id=' . $user_id);
    // ---- Stats ----------------------------------------------------------------------------
    $posts = rows('select * from posts where community_id=' . $community_id);
    $comments = rows('select * from comments inner join posts on comments.post_id=posts.post_id where posts.community_id=' . $community_id);
    $members = rows('select * from joined_communities where community_id=' . $community_id);
    // $moderators = rows('select * from moderators where community_id=' . $community_id); // TODO: implement
    // ---- User data ------------------------------------------------------------------------
    $joined = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $joined = exists("select * from joined_communities where community_id=$community_id and user_id=$session_user");
    }
    return array(
        'name' => $community['name'],
        'shortname' => $community['shortname'],
        'description' => $community['description'],
        'date' => $community['created_at'],
        'owner' => $owner['username'],
        'posts' => $posts,
        'comments' => $comments,
        'members' => $members,
        'joined' => $joined
    );
}
function getUserCommentData($comment) {
    extract($comment);
    $user = row('select * from users where user_id=' . $user_id);

    // todo
}
function userCommentsHTML($post, $user, $head = true) {
    $post_comments = query('select * from comments where post_id=' . $post['post_id'] . ' order by created_at desc');
    if (!$post_comments) {
        echo 'comment';
        return;
    };
    $user_id = $user['user_id'];
    $username = $user['username'];
    $sub = getField('select shortname from communities where community_id=' . $post['community_id']);
    echo '<article class="user-comment-wrapper' . activeClass(!$head, 'beheaded') . '">';
    if ($head) {
        $author = getField('select username from users where user_id=' . $post['user_id']);
        $hash = $post['hash'];
        $title = $post['title'];
        echo '
            <section class="user-comment-wrapper__head">
                ' . file_get_contents(__DIR__ . '/../resources/icons/comment2.svg') . '
                ' . linkHTML('users/' . $username, $username) . '
                commented on 
                ' . linkHTML('subs/' . $sub . '/posts/' . $hash, $title) . '
                in ' . linkHTML('subs/' . $sub, 's/' . $sub) . '
                Posted by ' . linkHTML('users/' . $author, $author) . '
            </section>
            ';
    }
    echo '<section class="user-comment-wrapper__comments" data-hash="' . $post['hash'] . '" data-sub="' . $sub . '">';
    foreach ($post_comments as $comment) {
        if ($comment['user_id'] != $user_id) continue;
        $indent = 1;
        $alias = $comment;
        while ($alias['parent_id']) {
            foreach ($post_comments as $other) {
                if ($other['comment_id'] == $alias['parent_id']) {
                    $alias = $other;
                }
            }
            $indent++;
        }
        echo '
            <div class="user-comment" data-hash="' . $comment['hash'] . '">
                ' . str_repeat('<div class="indent">', $indent) . '
                <div class="user-comment__head">
                    ' . linkHTML('users/' . $username, $username) . '
                    ' . formatDate($comment['created_at']) . '
                </div>
                <p>' . $comment['content'] . '</p>
                ' . str_repeat('</div>', $indent) . '                
            </div>';
    }
    echo '
            </section>
        </article>';
}
function overviewComment($user, $comment, $indent) {
    $username = $user['username'];
    return '
        <div class="comment">
            ' . str_repeat('<div class="indent">', $indent) . '
                <div class="comment__head">
                    ' . linkHTML('users/' . $username, $username) . '
                    ' . formatDate($comment['created_at']) . '
                </div>
                <p>' . $comment['content'] . '</p>
            ' . str_repeat('</div>', $indent) . '
        </div>
        ';
}
function postHTML($post, $show_community = true, $show_user = true) {
    $post_data = getPostData($post);
    extract($post_data);
    if ($removed) {
        $title = '[Removed]';
        $content = '[Removed]';
    } else {
        $post_media = $media;
        if (count($post_media) > 0) {
            $file_name = $post_media[0]['file_name'];
            $extension = explode('.', $file_name)[1];
            $image_extensions = array('png', 'jpg', 'jpeg', 'tiff', 'bmp', 'webp', 'gif');
            $video_extensions = array('mp4', 'wav', 'mov');
            if (in_array($extension, $image_extensions)) {
                // create image collage
                $content = '<div class="image-collage">';
                foreach ($post_media as $media) {
                    $content .= '<img src="resources/uploads/' . $media['file_name'] . '" alt="TODO: description">';
                }
                $content .= '</div>';
            } else if (in_array($extension, $video_extensions)) {
                // todo: create functional video player
                $content = '
                    <div class="video-container">
                        <video controls>
                            <source src="resources/uploads/' . $post_media[0]['file_name'] . '">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    ';
            }
        }
    }
    // ---- Post header ----------------------------------------------------------------------
    $post_head = '<div class="head">';
    if ($show_community) {
        if (!$show_user) $post_head .= 'posted in ';
        $post_head .= '<a href="subs/' . $sub . '">s/' . $sub . '</a>&nbsp;';
    }
    if ($show_user) {
        $post_head .= 'posted by
            <a href="users/' . $username . '">
            u/' . $username . '
            </a>';
    }
    $post_head .= $age . '</div>';
    echo '
        <article class="post" data-hash="' . $hash . '" data-sub="' . $sub . '">
            <section class="left">
                ' . arrow_wrapper($liked, $disliked, $likes, false, $removed) . '
            </section>
            <section class="right">
                ' . $post_head . '
                <h2>' . $title . '</h2>
                ' . $content . '
                ' . footerHTML($saved, $comments) . '
            </section>
        </article>
        ';
}
function commentHTML($comment) {
    $comment_data = getCommentData($comment);
    extract($comment_data);
    if ($removed) {
        $content = '[Removed]';
    }
    $pfp = 'resources/pfps/' . $username . '.png';
    echo '
        <article class="comment" data-hash="' . $hash . '" id="comment-' . $hash . '">
            <section class="comment__header">
                <a href="users/' . $username . '">
                    <img src="' . $pfp . '" class="pfp small">' . $username . '</a>
                <span class="light">' . $age . '</span>
            </section>
            <p class="comment__content">' . $content . '</p>
            <section class="comment__footer">
                ' . arrow_wrapper($liked, $disliked, $likes, true, $removed) . '
                <button aria-label="comment" name="comment-btn" name="reply" class="comment-btn">Reply</button>
                <button aria-label="save" name="save-btn" name="save" class="save-btn' . activeClass($saved) . '"></button>
                <button aria-label="share" name="share-btn" name="share" class="share-btn">Share</button>
            </section>
        </article>
        ';
}
function activeClass($bool, $class = 'active', $whitespace = true) {
    if ($bool) {
        return ($whitespace ? ' ' : '') . $class;
    }
    return '';
}
function arrow_wrapper($liked = false, $disliked = false, $count = 0, $horizontal = false, $disabled = false) {
    return '
        <section class="arrow-wrapper' . activeClass($horizontal, 'horizontal') . '">
            <button aria-label="upvote" name="upvote-btn" name="upvote" class="upvote' . activeClass($liked) . '" ' . activeClass($disabled, 'disabled') . '>
                ' . file_get_contents(__DIR__ . '/../resources/upvote.svg') . '
            </button>
            <span class="like-count">
                ' . $count . '
            </span>
            <button aria-label="downvote" name="downvote-btn" name="downvote" class="downvote' . activeClass($disliked) . '" ' . activeClass($disabled, 'disabled') . '>
                ' . file_get_contents(__DIR__ . '/../resources/upvote.svg') . '
            </button>
        </section>
        ';
}
function overviewPostHTML($post) {
    $post_data = getPostData($post);
    extract($post_data);
    if ($removed) return;

    if ($media) {
        $file_name = $media[0]['file_name'];
        $file_hash = explode('.', $file_name)[0];
        $thumbnail_path = 'resources/uploads/thumbnails/' . $file_hash . '.jpg';
        $thumbnail = '<img src="' . $thumbnail_path . '" alt="TODO: description">';
    }

    echo  '
        <article class="post overview" data-hash="' . $hash . '" data-sub="' . $sub . '">
            <section class="left">
                ' . arrow_wrapper($liked, $disliked, $likes) . '
            </section>
            <section class="thumb">
                ' . (isset($thumbnail) ? $thumbnail : file_get_contents(__DIR__ . '/../resources/icons/post.svg')) . '
            </section>
            <section class="right">
                <div class="head">
                    posted in <a href="subs/' . $sub . '">s/' . $sub . '</a>
                    ' . $age . '
                </div>
                <h2>' . $title . '</h2>
                ' . footerHTML($saved, $comments) . '
            </section>
        </article>
        ';
}
function overviewCommentHTML($comment) {
    $comment_data = getCommentData($comment);
    extract($comment_data);
    if ($removed) return;

    echo '
        <article class="comment overview" data-hash="' . $hash . '">
            <section class="header">
                ' . file_get_contents(__DIR__ . '/../resources/icons/comment.svg') . '
                <a href="users/' . $username . '">' . $username . '</a>
                commented on <a href="subs/' . $sub . '/posts/' . $post['hash'] . '">' . $title . '</a> 
                in <a href="subs/' . $sub . '">s/' . $sub . '</a>
                Posted by <a href="users/' . $post_author . '">u/' . $post_author . '</a>
            </section>
            <section class="content">
                <div class="indent">
                    <div class="header">
                        <a href="users/' . $username . '">' . $username . '</a>
                        XXX days ago
                    </div>
                    <p>' . $content . '</p>
                    <div class="footer">
                        <button aria-label="reply" name="reply-btn" class="reply-btn">Reply</button>
                        <button aria-label="save" name="save-btn" class="save-btn' . activeClass($saved) . '"></button>
                        <button aria-label="share" name="share-btn" class="share-btn">Share</button>
                    </div>
                </div>
            </section>
        </article>
        ';
}
function footerHTML($saved, $comments) {
    return '
            <section class="footer">
                <button aria-label="comment" name="comment-btn" class="comment-btn">
                    ' . file_get_contents(__DIR__ . '/../resources/icons/comment.svg') . '
                    ' . $comments . ' comments
                </button>
                <button aria-label="save" name="save-btn" class="save-btn' . activeClass($saved) . '">
                    ' . file_get_contents(__DIR__ . '/../resources/icons/bookmark.svg') . '
                </button>
                <button aria-label="share" name="share-btn" class="share-btn">
                    ' . file_get_contents(__DIR__ . '/../resources/icons/share.svg') . '
                    Share
                </button>
            </section>
        ';
}
function communitySidebarHTML($name) {
    $sub_data = getCommunityData($name);
    extract($sub_data);

    echo '
        <article class="info titled">
            <h1 class="community_info-about">About s/' . $shortname . '</h1>
            <ul>
                <li><p>' . $description . '</p></li>
                <li><span class="members">' . $members . '</span> Members</li>
                <li><span class="posts">' . $posts . '</span> Posts</li>
                <li>Created <span class="date">' . $date . '</span></li>
            </ul>
        </article>
        <article class="community__rules titled">
        <h1>s/' . $shortname . ' Rules</h1>
        <ul>
            <li>
                <details>
                    <summary>1.Posts must be funny</summary>
                    <p>At least an attempt</p>
                </details>
            </li>
            <li>
                <details>
                    <summary>2.Posts must be programming realted</summary>
                    <p>Posts must be related to programming or the profession in general</p>
                </details>
            </li>
            <li>
                <details>
                    <summary>3.No Reposts</summary>
                    <p>Content that has been already posted less than a month ago will be removed</p>
                </details>
            </li>
            <li>
                <details>
                    <summary>4.No low-quality content</summary>
                    <p>Bad posts will be removed</p>
                </details>
            </li>
            <li>
                <details>
                    <summary>5.Put effort into titles</summary>
                    <p>Titles are important</p>
                </details>
            </li>
        </ul>
        </article>
        <article class="titled">
            <h1>Moderators</h1>
            <ul>
                <li class="flair">
                    <a href="/greendit/users/' . $owner . '">
                        <img class="pfp small" src="resources/pfps/'.$owner.'.png" 
                        alt="' . $owner . '">u/' . $owner . '
                    </a>
                </li>
            </ul>
        </article>
    ';
}