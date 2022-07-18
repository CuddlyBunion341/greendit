<?php require('templates/header.php'); ?>
<link rel="stylesheet" href="scripts/css/community.css">
<?php
    if (!isset($_GET['name'])) {
        header('Location: /greendit/error/419');
        exit;
    }
    $name = $_GET['name'];
    require_once('config/db_connect.php');
    $community = row('select * from communities where shortname = "' . $name . '"');
    if (!$community) {
        header('Location: /greendit/error/419');
        exit;
    }
    function plural($word,$num) {
        if ($num == 1) {
            return $word;
        }
        return $word.'s';
    }
    $posts = rows('select * from posts where community_id = ' . $community['community_id']);
    $users = rows('select * from joined_communities where community_id = ' . $community['community_id']);
    echo '
    <div class="community-header">
        <img class="community-background" src="resources/community.png" alt="">
        <div class="community-banner">
            <img class="community-pfp" src="resources/pfp.png" alt="">
            <div class="community-banner-main">
                <div class="top">
                    <h2>' . $community['name'] . '</h2>
                </div>
                <a href="subs/' . $community['shortname'] . '">s/' . $community['shortname'] . '</a>
            </div>
        </div>
        <div class="community-main">
            <div class="community-stats">
                <p>Created ' . $community['created_at'] . '</p>
                <p>' . $posts . ' '.plural('post',$posts).'</p>
                <p>' . $users . ' '.plural('member',$users).'</p>
            </div>
        </div>
    </div>
    ';
?>
<main>
    <h2>Popular posts</h2>
    <div id="feed">
        <?php
            require 'util/feed.php';
            $sql = 'select * from posts where community_id='.$community['community_id'].' order by post_id desc';
            $posts = query($sql);

            foreach ($posts as $post) {
                postHTML($post,false);
            }
        ?>
        <script src="scripts/js/feed.js"></script>
    </div>
</main>
<?php require('templates/footer.php'); ?>