<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); 
        exit;
    }
    if (!isset($_POST['post_id'])) {
        http_response_code(400);
        exit;
    }

    $response = array('increment' => 0, 'error' => 0);

    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    
    require '../config/db_connect.php';
    $sql = "select * from post_likes where user_id=$user_id and post_id=$post_id";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        $sql = "insert into post_likes(user_id,post_id) values ($user_id,$post_id);";
        if ($conn->query($sql)) {
            $response['increment'] = 1;
            $response['message'] = 'Upvoted post with id ' . $post_id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
    } else {
        $sql = "delete from post_likes where user_id=$user_id and post_id=$post_id";
        if ($conn->query($sql)) {
            $response['increment'] = -1;
            $response['message'] = 'Removed upvote from post with id ' . $post_id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
    }
    $result = $conn->query($sql);

    echo json_encode($response);

?>