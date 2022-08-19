<?php
function get_community_data($community) {
    extract($community);
    // ---- Stats ----------------------------------------------------------------------------
    $members = rows('select * from joined_communities where community_id=' . $community_id);
    $posts = rows('select * from posts where community_id=' . $community_id);
    // ---- User data ------------------------------------------------------------------------
    $joined = false;
    if (isset($_SESSION['user_id'])) {
        $session_user = $_SESSION['user_id'];
        $joined = exists("select * from joined_communities where community_id=$community_id and user_id=$session_user");
    }
    return [
        'name' => $name,
        'shortname' => $shortname,
        'joined' => $joined,
        'created_at' => $created_at,
        'posts' => $posts,
        'members' => $members,
    ];
}

function overview_communityHTML($community) {
    $community_data = get_community_data($community);
    extract($community_data);
    ?>
    <article class="community" data-name="<?= $shortname ?>">
        <img src="resources/com.png" alt="<?= $shortname ?>" class="pfp small">
        <div class="main">
            <p><?= $name ?></p>
            <a href="subs/<?= $shortname ?>">s/<?= $shortname ?></a>
        </div>
        <button class="join-btn<?= active($joined) ?>" data-name="<?= $shortname ?>"></button>
    </article>
    <?php
}
?>