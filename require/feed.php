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
        // ---- Media ----------------------------------------------------------------------------
        $post_media = query('select * from post_media where post_id = ' . $post['post_id']);
        $content = '';
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
                echo '
                <video width="XXX" height="XXX">
                    <source src="resources/uploads/fullres/' . $post_media[0]['file_name'] . '" type="XXX">
                    Your browser does not support the video tag.
                </video>
                ';
            }
        } else {
            // text content
            $content = '<p>'.$post['content'].'</p>';
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
                    <button class="upvote"><img src="resources/upvote'.($liked?'_full':'').'.svg"></button>
                    <span class="like-count">'.$totalLikes.'</span>
                    <button class="downvote"><img src="resources/downvote'.($disliked?'_full':'').'.svg"></button>
                </div>
            </div>
            <div class="right">
                '.$post_head.'
                <h2>' . $post['title'] . '</h2>
                '.$content.'
                <div class="footer">
                    <button class="comment-btn">' . $comments . ' comments</button>
                    <button class="save-btn">Save</button>
                    <button class="share-btn">Share</button>
                    <button class="report-btn">Report</button>
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
        if (isset($_SESSION['user_id'])) {
            $liked = rows('select * from comment_likes where comment_id=' . $comment['comment_id'] . ' and user_id=' . $_SESSION['user_id']);
            $disliked = rows('select * from comment_dislikes where comment_id=' . $comment['comment_id'] . ' and user_id=' . $_SESSION['user_id']);
        }
        $pfp = 'pfps/'.$user['username'];
        // $pfp = file_exists($pfp) ? $pfp : 'default_pfp'; // todo: fix
        echo '
        <div class="comment" data-hash="'.$comment['hash'].'" id="comment-'.$comment['hash'].'">
            <div class="header">
                <a href="users/'.$user['username'].'">
                    <img src="resources/'.$pfp.'.png" class="user-pfp">'. $user['username'] . '
                </a>
            </div>
            <p>'.$comment['content'].'</p>
            <div class="footer">
                <div class="arrow-wrapper horizontal">
                    <button class="upvote"><img src="resources/upvote'.($liked?'_full':'').'.svg"></button>
                    <span class="like-count">'.$likes.'</span>
                    <button class="downvote"><img src="resources/downvote'.($disliked?'_full':'').'.svg"></button>
                </div>
                <button class="comment-btn">Reply</button>
                <button class="save-btn">Save</button>
                <button class="share-btn">Share</button>
                <button class="report-btn">Report</button>
            </div>
        </div>
        ';
    }
?>