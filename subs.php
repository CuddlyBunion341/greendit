<?php require('templates/header.php'); ?>
<main>
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
    $date = $community['created_at'];
    $datediff = time() - strtotime($date);
    $date = round($datediff / (60 * 60 * 24));
    $posts = rows('select * from posts where community_id = ' . $community['community_id']);
    $users = rows('select * from joined_communities where community_id = ' . $community['community_id']);
    echo '
        <div class="community-info">
            <div class="community-pfp">
                <img src="resources/pfp.png" alt="">
            </div>
            <div class="community-main">
                <h2>' . $community['name'] . '</h2>
                <a href="subs/' . $community['shortname'] . '">s/' . $community['shortname'] . '</a>
                <p>Created ' . $community['created_at'] . '</p>
                <button class="join-btn">Join</button>
            </div>
            <div class="community-stats">
                <a href="community/' . $community['shortname'] . '/posts">' . $posts . ' '.plural('post',$posts).'</a><br>
                <a href="community/' . $community['shortname'] . '/members">' . $users . ' '.plural('member',$users).' </a>
            </div>
        </div>
    ';
?>
</main>
<?php require('templates/footer.php'); ?>