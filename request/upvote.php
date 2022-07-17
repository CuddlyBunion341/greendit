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
    $upvote = isset($_POST['upvote']) && $_POST['upvote'];
    $table1 = $upvote ? 'post_likes' : 'post_dislikes';
    $table2 = $upvote ? 'post_dislikes' : 'post_likes';

    $response = array('increment' => 0, 'error' => 0);

    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    
    require '../config/db_connect.php';
    $sql = "select * from $table1 where user_id=$user_id and post_id=$post_id";
    $added = $conn->query($sql);
    if ($added->num_rows == 0) {
        // does not exist yet
        $sql = "insert into $table1(user_id,post_id) values ($user_id,$post_id);";
        if ($conn->query($sql)) {
            $response['increment'] = 1;
            $response['message'] = 'voted post with id ' . $post_id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
        $sql = "delete from $table2 where user_id=$user_id and post_id=$post_id";
        $conn->query($sql);
    } else {
        // allready exists
        $sql = "delete from $table1 where user_id=$user_id and post_id=$post_id";
        if ($conn->query($sql)) {
            $response['increment'] = -1;
            $response['message'] = 'Removed vote from post with id ' . $post_id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
    }
    echo json_encode($response);

?>