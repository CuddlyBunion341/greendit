<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($conn)) require_once 'require/db_connect.php'; // todo: refactor this line to work with requests
    function postHTML($post, $show_community = true, $show_user = true) {
        $user = row('select * from users where user_id=' . $post['user_id']);
        $community = row('select * from communities where community_id=' . $post['community_id']);
        $comments = rows('select * from comments where post_id=' . $post['post_id']);
        $likes = rows('select * from post_likes where post_id=' . $post['post_id']);
        $dislikes = rows('select * from post_dislikes where post_id=' . $post['post_id']);
        $totalLikes = $likes - $dislikes;
        // ---- Date -----------------------------------------------------------------------------
        $date = $post['created_at'];
        $datediff = time() - strtotime($date);
        $date = round($datediff / (60 * 60 * 24));
        // ---- likes / dislikes -----------------------------------------------------------------
        $liked = 0;
        $disliked = 0;
        if (isset($_SESSION['user_id'])) {
            $liked = rows('select * from post_likes where post_id=' . $post['post_id'] . ' and user_id=' . $_SESSION['user_id']);
            $disliked = rows('select * from post_dislikes where post_id=' . $post['post_id'] . ' and user_id=' . $_SESSION['user_id']);
        }
        // ---- Saved ----------------------------------------------------------------------------
        $saved = rows('select * from saved_posts where post_id='.$post['post_id'].' and user_id='.$_SESSION['user_id']);
        $save_active = $saved > 0 ? ' active' : '';
        // ---- Media ----------------------------------------------------------------------------
        $title = $post['title'];
        $disabled = '';
        if ($post['status'] == 'removed') {
            $title = '[Removed]';
            $content = '[Removed]';
            $disabled = 'disabled';
        } else {
            $post_media = query('select * from post_media where post_id = ' . $post['post_id']);
            if (count($post_media) > 0) {
                $file_name = $post_media[0]['file_name'];
                $extension = explode('.',$file_name)[1];
                $image_extensions = array('png','jpg','jpeg','tiff','bmp');
                $video_extensions = array('mp4','wav','mov');
                if (in_array($extension, $image_extensions)) {
                    // create image collage
                    $content = '<div class="image-collage">';
                    foreach ($post_media as $media) {
                        $content .= '<img src="resources/uploads/' . $media['file_name'] . '">';
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
            } else {
                // text content
                $content = '<p>'.$post['content'].'</p>';
            }
        }
        // ---- Post header ----------------------------------------------------------------------
        $post_head = '<div class="head">';
        if ($show_community) {
            if (!$show_user) $post_head .= 'posted in ';
            $post_head .= '<a href="subs/'.$community['shortname'].'">s/'.$community['shortname'].'</a>&nbsp;';
        }
        if ($show_user) {
            $post_head .= 'posted by
            <a href="users/'.$user['username'].'">
            u/'. $user['username'] . '
            </a>';
        }
        $post_head .= $date . ' day(s) ago </div>';
        echo '
        <div class="post" data-hash="'.$post['hash'].'">
            <div class="left">
                <div class="arrow-wrapper">
                    <button name="upvote-btn" class="upvote'.($liked?' active':'').'" '.$disabled.'>
                        '.file_get_contents('resources/upvote.svg').'
                    </button>
                    <span class="like-count">'.$totalLikes.'</span>
                    <button name="downvote-btn" class="downvote'.($disliked?' active':'').'" '.$disabled.'>
                        '.file_get_contents('resources/upvote.svg').'
                    </button>
                </div>
            </div>
            <div class="right">
                '.$post_head.'
                <h2>' . $title . '</h2>
                '.$content.'
                <div class="footer">
                    <button name="comment-btn" class="comment-btn">' . $comments . ' comments</button>
                    <button name="save-btn" class="save-btn'.$save_active.'"></button>
                    <button name="share-btn" class="share-btn">Share</button>
                </div>  
            </div>
        </div>
        ';
    }
    function commentHTML($comment) {
        // user
        $user = row('select * from users where user_id=' . $comment['user_id']);
        // likes / dislikes
        $total_likes = rows('select * from comment_likes where comment_id=' . $comment['comment_id']);
        $total_dislikes = rows('select * from comment_dislikes where comment_id=' . $comment['comment_id']);
        $likes = $total_likes - $total_dislikes;
        $liked = $disliked = 0;
        if ($comment['status'] == 'public') {
            $content = $comment['content'];
            $disabled = '';
        } else {
            $content = '[Removed]';
            $disabled = 'disabled';
        }
        if (isset($_SESSION['user_id'])) {
            $liked = rows('select * from comment_likes where comment_id=' . $comment['comment_id'] . ' and user_id=' . $_SESSION['user_id']);
            $disliked = rows('select * from comment_dislikes where comment_id=' . $comment['comment_id'] . ' and user_id=' . $_SESSION['user_id']);
        }
        $saved = rows('select * from saved_comments where user_id=' . $comment['user_id'] . ' and comment_id='. $comment['comment_id']);
        $save_active = $saved > 0 ? ' active' : '';
        $pfp = 'pfps/'.$user['username'];
        // $pfp = file_exists($pfp) ? $pfp : 'default_pfp'; // todo: fix
        $resource_path = 'resources/';
        if ($_SERVER['PHP_SELF'] == '/greendit/request/create_comment.php') {
            $resource_path = '../'.$resource_path;
        }
        echo '
        <div class="comment" data-hash="'.$comment['hash'].'" id="comment-'.$comment['hash'].'">
            <div class="header">
                <a href="users/'.$user['username'].'">
                    <img src="resources/'.$pfp.'.png" class="user-pfp">'. $user['username'] . '
                </a>
            </div>
            <p>'.$content.'</p>
            <div class="footer">
                <div class="arrow-wrapper horizontal">
                <button name="upvote-btn" class="upvote'.($liked?' active':'').'" '.$disabled.'>
                    '.file_get_contents($resource_path.'/upvote.svg').'
                </button>
                <span class="like-count">'.$likes.'</span>
                <button name="downvote-btn" class="downvote'.($disliked?' active':'').'" '.$disabled.'>
                '.file_get_contents($resource_path.'/upvote.svg').'
                </button>
                </div>
                <button name="comment-btn" class="comment-btn">Reply</button>
                <button name="save-btn" class="save-btn'.$save_active.'"></button>
                <button name="share-btn" class="share-btn">Share</button>
            </div>
        </div>
        ';
    }
?>