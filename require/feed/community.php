<?php
function getCommunityData($community) {
    extract($community);
    // ---- Objects --------------------------------------------------------------------------
    $owner = getField('select username from users where user_id = ' . $user_id);
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
        'owner' => $owner,
        'description' => $description,
        'shortname' => $shortname,
        'joined' => $joined,
        'created_at' => $created_at,
        'posts' => $posts,
        'members' => $members,
    ];
}

function overviewCommunityHTML($community) {
    $community_data = getCommunityData($community);
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

function communitySidebarHTML($community) {
    $community_data = getCommunityData($community);
    extract($community_data);
?>
<article class="info titled">
    <h1 class="community_info-about">About s/<?= $shortname ?></h1>
    <ul>
        <li>
            <p><?= $description ?></p>
        </li>
        <li><span class="members"><?= $members ?></span> Members</li>
        <li><span class="posts"><?= $posts ?></span> Posts</li>
        <li>Created <span class="date"><?= formatDate($created_at) ?></span></li>
    </ul>
</article>
<article class="community__rules titled">
    <h1>s/<?= $shortname ?> Rules</h1>
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
            <a href="/greendit/users/<?= $owner ?>">
                <img class="pfp small" src="resources/pfps/<?= $owner ?>.png" alt="<?= $owner ?>">u/<?= $owner ?>
            </a>
        </li>
    </ul>
</article>
<?php
}
?>