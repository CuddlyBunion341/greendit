<?php
require __DIR__ . '/../require/db_connect.php';
require __DIR__ . '/../require/feed.php';
require __DIR__ . '/../require/uuid.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}
$user_id = $_SESSION['user_id'];
if (!isset($_POST['content'], $_POST['post'])) {
    http_response_code(400);
    exit('Missing content or post_id.');
}
$content = trim(htmlspecialchars($_POST['content']));
if (empty($content)) {
    http_response_code(400);
    exit('Content cannot be empty');
}
$comment = row('select * from posts where hash =\'' . $_POST['post'] . '\'');
$post_id = $comment['post_id'];
if ($comment['status'] == 'removed') {
    http_response_code(400);
    exit('Commenting on removed post is not allowed.');
}
do {
    $hash = random_string(6);
} while (exists('select * from comments where hash = \'' . $hash . '\''));
execute("
        insert into comments 
        (user_id, post_id, content, hash) 
        values ('$user_id', '$post_id','$content','$hash')");
$comment_id = $conn->insert_id;
$comment = row('select * from comments where comment_id = ' . $comment_id);
if (!$comment) {
    http_response_code(500);
    exit('Error creating comment.');
}
post_commentHTML($comment);
