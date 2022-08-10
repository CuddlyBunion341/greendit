<?php require('require/header.php'); ?>
<?php
if (!isset($_GET['name'])) {
    header('Location: /greendit/error/419');
    exit;
}
$name = $_GET['name'];
require_once('require/db_connect.php');
$community = row('select * from communities where shortname = "' . $name . '"');
if (!$community) {
    header('Location: /greendit/error/419');
    exit;
}
require 'require/feed.php';
$community_id = $community['community_id'];
$posts = rows('select * from posts where community_id=' . $community_id);
$users = rows('select * from joined_communities where community_id=' . $community_id);
$joined = false;
if (isset($_SESSION['user_id'])) {
    $session_user = $_SESSION['user_id'];
    $joined = exists("select * from joined_communities where community_id=$community_id and user_id=$session_user");
}
echo '
    <div class="community-header">
        <img class="community-background" src="resources/community.png" alt="">
        <div class="community-banner">
            <img class="community-pfp" src="resources/com.png" alt="">
            <div class="community-banner-main">
                <div class="top">
                    <h2>' . $community['name'] . '</h2>
                    <button aria-label="join" class="join-btn' . activeClass($joined) . '" name="join-btn" data-name="' . $community['shortname'] . '"></button>
                </div>
                <a href="subs/' . $community['shortname'] . '">s/' . $community['shortname'] . '</a>
            </div>
        </div>
        <!--<div class="community-main">
            <div class="community-stats">
                <p>Created ' . $community['created_at'] . '</p>
                <p>' . $posts . ' ' . plural('post', $posts) . '</p>
                <p><span id="members">' . $users . '</span> ' . plural('member', $users) . '</p>
            </div>
        </div>-->
    </div>
    ';
?>
<main class="multicol">
    <aside class="community__info">
        <article class="info titled">
            <h1 class="communtiy__info-about">About</h1>
            <ul>
                <li>
                    <p>Programming and Memes</p>
                </li>
                <li>
                    <p><span class="members">10</span> Members</p>
                </li>
                <li>
                    <p>Created 2022-07-17</p>
                </li>
            </ul>
        </article>
        <article class="community__rules titled">
            <h1>s/<?php echo $community['shortname'] ?> Rules</h1>
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
                    <a href="/greendit/users/admin">
                        <img class="pfp small" src="resources/pfps/admin.png" alt="admin">u/admin
                    </a>
                </li>
                <li class="flair">
                    <a href="/greendit/users/cb341">
                        <img class="pfp small" src="resources/pfps/cb341.png" alt="cb341">u/cb341
                    </a>
                </li>
                <li class="flair">
                    <a href="/greendit/users/JeJe69">
                        <img class="pfp small" src="resources/pfps/JeJe69.png" alt="JeJe69">u/JeJe69
                    </a>
                </li>
            </ul>
        </article>
    </aside>
    <div id="feed">
        <article>
            <h2>Popular posts</h2>
        </article>
        <?php
        if (!isset($_GET['post'])) {
            // show all posts
            if (isset($_SESSION['user_id'])) {
                echo '
                    <article class="create-post">
                        <a href="users/' . $_SESSION['username'] . '"><img class="user-pfp" alt="'.$_SESSION['username'].'" src="resources/pfps/' . $_SESSION['username'] . '.png"></a>
                        <button aria-label="create-post" onclick="window.location=\'/greendit/post.php?id=' . $community['community_id'] . '\'">Create post...</button>
                    </article>
                    ';
            }
            $sql = 'select * from posts where community_id=' . $community['community_id'] . ' order by post_id desc';
            $posts = query($sql);

            foreach ($posts as $post) {
                postHTML($post, false);
            }
            if (count($posts) == 0) {
                echo '<div class="feed-text">No posts yet!</div>';
            }
        } else {
            // show specific post
            if (isset($_GET['post'])) {
                $post = row('select * from posts where hash = \'' . $_GET['post'] . '\' and community_id = '.$community['community_id']);
                if (!$post) {
                    echo '<article>Post not found :/</article>';
                }
            }
            if ($post) {
                postHTML($post, false);
                $comments = query('select * from comments where post_id = ' . $post['post_id']);
                echo '<div class="comment-wrapper" data-hash="' . $post['hash'] . '">';
                if (isset($_SESSION['user_id'])) {
                    $username = $_SESSION['username'];
                    echo '
                            <form class="create-comment">
                                <a href="users/' . $username . '">
                                    <img src="resources/pfps/' . $username . '.png" alt="user" class="pfp medium">
                                </a>
                                <input type="text" class="comment-content" placeholder="Write a comment..." rows="4">
                                <button aria-label="publish-comment" name="comment-btn" type="submit" name="submit" class="comment-btn">Post</button>
                            </form>
                        ';
                }
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        commentHTML($comment);
                    }
                } else {
                    echo '<p>No comments yet.</p>';
                }
                echo '</div>';
                if (isset($_GET['comment'])) {
                    echo '
                        <script>
                            document.addEventListener("DOMContentLoaded",() => {
                                const comment = document.querySelector("#comment-' . $_GET['comment'] . '");
                                comment.classList.add("highlighted");
                                comment.scrollIntoView();
                            });
                        </script>
                        ';
                }
            }
        }
        ?>
        <script src="js/feed.js"></script>
    </div>
</main>
<?php require('require/footer.php'); ?>