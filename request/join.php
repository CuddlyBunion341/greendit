<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}
if (!isset($_POST['name'])) {
    http_response_code(400);
    exit('Missing community name');
}

require __DIR__ . '/../require/db_connect.php';
$user_id = $_SESSION['user_id'];
$sub_name = htmlspecialchars($_POST['name']);
$sub_id = getField('select community_id from communities where shortname = \'' . $sub_name . '\'');

$toggle = toggle(
    "select * from joined_communities where user_id = $user_id and community_id = $sub_id",
    "insert into joined_communities (user_id, community_id) values ($user_id,$sub_id)",
    "delete from joined_communities where user_id = $user_id and community_id = $sub_id"
);

$response = array('toggle' => $toggle);

if ($toggle == -1) {
    http_response_code(500);
    $message = $conn->error;
} else {
    $message = ($toggle == 1 ? 'Joined' : 'Left') . ' s/' . $sub_name;
}
$response['message'] = $message;

echo json_encode($response);
