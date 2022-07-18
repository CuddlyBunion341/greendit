<?php require 'templates/header.php';?>
<main>
    <link rel="stylesheet" href="scripts/css/post.css">
    <form action="post.php" method="post" class="create-post-form">
        <div class="container">
            <h1>Create a post</h1>
            <select name="sub" id="sub">
                <option value="" selected hidden disabled>Community</option>
                <?php
                    require_once('config/db_connect.php');
                    $subs = query('select * from communities order by shortname asc');
                    foreach ($subs as $sub) {
                        echo '<option value="'.$sub['shortname'].'">s/'.$sub['shortname'].'</option>';
                    }
                ?>
            </select>
            <input type="text" name="title" id="title" placeholder="Title">
            <textarea name="content" id="content" cols="30" rows="10" placeholder="Text (required)"></textarea>
            <button type="submit">Post</button>
        </div>
    </form>
</main>
<?php require 'templates/footer.php';?>