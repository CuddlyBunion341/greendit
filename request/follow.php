<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}
if (!isset($_POST['username'])) {
    http_response_code(400);
    exit('Missing username');
}

require __DIR__ . '/../require/db_connect.php';
$username = $_POST['username'];
$follower = $_SESSION['username'];
$follower_id = $_SESSION['user_id'];
$user_id = getField('select user_id from users where username=\'' . $username . '\'');

$toggle = toggle(
    "select * from followers where user_id=$user_id and follower_id=$follower_id",
    "insert into followers (user_id, follower_id) values ($user_id,$follower_id)",
    "delete from followers where user_id=$user_id and follower_id=$follower_id"
);

$response = array('toggle' => $toggle);

if ($toggle == -1) {
    http_response_code(500);
    $message = $conn->error;
} else {
    $message = $follower . ' is ' . ($toggle == 1 ? 'now' : 'no longer') . ' following ' . $username;
}
$response['message'] = $message;

echo json_encode($response);
