<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($conn)) require_once __DIR__.'/db_connect.php';
    function getPostData($post) {
        extract($post);
        // ---- Objects --------------------------------------------------------------------------
        $user = row('select * from users where user_id='.$user_id);
        $community = row('select * from communities where community_id='.$community_id);
        $media = query('select * from post_media where post_id='.$post_id);
        // ---- Stats ----------------------------------------------------------------------------
        $comments = rows('select * from comments where post_id='.$post_id);
        $likes = rows('select * from post_likes where post_id='.$post_id);
        $dislikes = rows('select * from post_dislikes where post_id='.$post_id);
        $total_likes = $likes - $dislikes;
        $date = $created_at;
        $datediff = time() - strtotime($date);
        $date = round($datediff / (60 * 60 * 24));
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
            'days' => $date,
            'liked' => $liked,
            'disliked' => $disliked,
            'saved' => $saved
        );
    }
    function getCommentData($comment) {
        extract($comment);
        // ---- Objects --------------------------------------------------------------------------
        $user = row('select * from users where user_id='.$user_id);
        $post = row('select * from posts where post_id='.$post_id);
        $community = row('select * from communities where community_id='.$post['community_id']);
        $post_author = row('select * from users where user_id='.$post['user_id']);
        // ---- Stats ----------------------------------------------------------------------------
        $likes = rows('select * from comment_likes where comment_id='.$comment_id);
        $dislikes = rows('select * from comment_dislikes where comment_id='.$comment_id);
        $total_likes = $likes - $dislikes;
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
            'title' => $post['title'],
            'post_author' => $post_author['username'],
            'likes' => $total_likes,
            'liked' => $liked,
            'disliked' => $disliked,
            'saved' => $saved
        );
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
            }
        }
        // ---- Post header ----------------------------------------------------------------------
        $post_head = '<div class="head">';
        if ($show_community) {
            if (!$show_user) $post_head .= 'posted in ';
            $post_head .= '<a href="subs/'.$sub.'">s/'.$sub.'</a>&nbsp;';
        }
        if ($show_user) {
            $post_head .= 'posted by
            <a href="users/'.$username.'">
            u/'. $username . '
            </a>';
        }
        $post_head .= $days . ' day(s) ago </div>';
        echo '
        <div class="post" data-hash="'.$hash.'">
            <div class="left">
                '.arrow_wrapper($liked,$disliked,$likes,false,$removed).'
            </div>
            <div class="right">
                '.$post_head.'
                <h2>' . $title . '</h2>
                '.$content.'
                '.footerHTML($saved,$comments).'
            </div>
        </div>
        ';
    }
    function commentHTML($comment) {
        $comment_data = getCommentData($comment);
        extract($comment_data);
        if ($removed) {
            $content = '[Removed]';
        }
        $pfp = 'resources/pfps/'.$username.'.png';
        echo '
        <div class="comment" data-hash="'.$hash.'" id="comment-'.$hash.'">
            <div class="header">
                <a href="users/'.$username.'">
                    <img src="'.$pfp.'" class="user-pfp">'. $username . '
                </a>
            </div>
            <p>'.$content.'</p>
            <div class="footer">
                '.arrow_wrapper($liked,$disliked,$likes,true,$removed).'
                <button name="comment-btn" class="comment-btn">Reply</button>
                <button name="save-btn" class="save-btn'.activeClass($saved).'"></button>
                <button name="share-btn" class="share-btn">Share</button>
            </div>
        </div>
        ';
    }
    function activeClass($bool,$class='active') {
        if ($bool) {
            return ' '.$class;
        }
        return '';
    }
    function arrow_wrapper($liked=false,$disliked=false,$count=0,$horizontal=false,$disabled=false) {
        return '
        <div class="arrow-wrapper'.activeClass($horizontal,'horizontal').'">
            <button name="upvote-btn" class="upvote'.activeClass($liked).'" '.activeClass($disabled,'disabled').'>
                '.file_get_contents(__DIR__.'/../resources/upvote.svg').'
            </button>
            <span class="like-count">
                '.$count.'
            </span>
            <button name="downvote-btn" class="downvote'.activeClass($disliked).'" '.activeClass($disabled,'disabled').'>
                '.file_get_contents(__DIR__.'/../resources/upvote.svg').'
            </button>
        </div>
        ';
    }
    function overviewPostHTML($post) {
        $post_data = getPostData($post);
        extract($post_data);
        if ($removed) return;

        echo  '
        <div class="post overview" data-hash="'.$hash.'">
            <div class="left">
                '.arrow_wrapper($liked,$disliked,$likes).'
            </div>
            <div class="thumb">
                <i class="fa-solid fa-align-justify"></i>
            </div>
            <div class="right">
                <div class="head">
                    posted in <a href="subs/'.$sub.'">s/'.$sub.'</a>
                    '.$days.' day(s) ago
                </div>
                <h2>'.$title.'</h2>
                '.footerHTML($saved, $comments).'
            </div>
        </div>
        ';
    }
    function overviewCommentHTML($comment) {
        $comment_data = getCommentData($comment);
        extract($comment_data);
        if ($removed) return;

        echo '
        <div class="comment overview" data-hash="'.$hash.'">
            <div class="header">
                <i class="fa-regular fa-message"></i>
                <a href="users/'.$username.'">'.$username.'</a>
                commented on <a href="subs/'.$sub.'/posts/'.$post['hash'].'">'.$title.'</a> 
                in <a href="subs/'.$sub.'">s/'.$sub.'</a>
                Posted by <a href="users/'.$post_author.'">u/'.$post_author.'</a>
            </div>
            <div class="content">
                <div class="indent">
                    <div class="header">
                        <a href="users/'.$username.'">'.$username.'</a>
                        XXX days ago
                    </div>
                    <p>'.$content.'</p>
                    <div class="footer">
                        <button name="comment-btn" class="comment-btn">Reply</button>
                        <button name="save-btn" class="save-btn'.activeClass($saved).'"></button>
                        <button name="share-btn" class="share-btn">Share</button>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
    function footerHTML($saved,$comments) {
        return '
            <div class="footer">
                <button name="comment-btn" class="comment-btn">
                    '.file_get_contents(__DIR__.'/../resources/icons/comment.svg').'
                    '.$comments.' comments
                </button>
                <button name="save-btn" class="save-btn '.activeClass($saved).'">
                    '.file_get_contents(__DIR__.'/../resources/icons/bookmark.svg').'
                </button>
                <button name="share-btn" class="share-btn">
                    '.file_get_contents(__DIR__.'/../resources/icons/share.svg').'
                    Share
                </button>
            </div>
        ';
    }
?>