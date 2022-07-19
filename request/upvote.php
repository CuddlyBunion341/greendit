<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); 
        exit;
    }
    if (!isset($_POST['post_id']) && !isset($_POST['comment_id'])) {
        http_response_code(400);
        exit;
    }
    $upvote = isset($_POST['upvote']) && $_POST['upvote'] == 'true';
    $table = isset($_POST['post_id']) ? 'post' : 'comment';
    $table1 = $upvote ? $table.'_likes' : $table.'_dislikes';
    $table2 = $upvote ? $table.'_dislikes' : $table.'_likes';

    $response = array('increment' => 0, 'error' => 0);

    $user_id = $_SESSION['user_id'];
    $primary = $table.'_id';
    $id = isset($_POST['post_id']) ? $_POST['post_id'] : $_POST['comment_id'];

    // echo var_dump($_POST);

    require '../config/db_connect.php';
    $added = rows("select * from $table1 where user_id=$user_id and $primary=$id");
    if ($added == 0) {
        // does not exist yet
        $sql = "insert into $table1(user_id,$primary) values ($user_id,$id);";
        if ($conn->query($sql)) {
            $response['increment'] = 1;
            $response['message'] = 'voted post with id ' . $id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
        $sql = "select * from $table2 where user_id=$user_id and $primary=$id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $sql = "delete from $table2 where user_id=$user_id and $primary=$id";
            $result = $conn->query($sql);
            $response['increment'] += 1;
        }
    } else {
        // allready exists
        $sql = "delete from $table1 where user_id=$user_id and $primary=$id";
        if ($conn->query($sql)) {
            $response['increment'] = -1;
            $response['message'] = 'Removed vote from post with id ' . $id;
        } else {
            $response['message'] = 'SQL ERROR: ' . $conn->error;
            $response['error'] = 1;
        }
    }
    echo json_encode($response);
?>