<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); 
        exit;
    }
    if (!isset($_POST['post']) && !isset($_POST['comment'])) {
        http_response_code(400);
        exit;
    }
    $upvote = isset($_POST['upvote']) && $_POST['upvote'] == 'true';
    $table = isset($_POST['post']) ? 'post' : 'comment';
    $table1 = $upvote ? $table.'_likes' : $table.'_dislikes';
    $table2 = $upvote ? $table.'_dislikes' : $table.'_likes';

    $response = array('increment' => 0, 'error' => 0);

    $user_id = $_SESSION['user_id'];
    $id_column = $table.'_id';
    $hash = isset($_POST['post']) ? $_POST['post'] : $_POST['comment'];
    require '../require/db_connect.php';
    $row = row('select * from '.$table.'s where hash=\''.$hash.'\'');
    $id = $row[$table.'_id'];
    if ($row['status'] == 'removed') {
        $response['error'] = 1;
        $response['message'] = 'ERROR: cannot vote removed '.$table;
    } else {
        $added = rows("select * from $table1 where user_id=$user_id and $id_column=$id");
        if ($added == 0) {
            // does not exist yet
            $sql = "insert into $table1(user_id,$id_column) values ($user_id,$id);";
            if ($conn->query($sql)) {
                $response['increment'] = 1;
                $response['message'] = 'voted post with id ' . $id;
            } else {
                $response['message'] = 'SQL ERROR: ' . $conn->error;
                $response['error'] = 1;
            }
            $sql = "select * from $table2 where user_id=$user_id and $id_column=$id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $sql = "delete from $table2 where user_id=$user_id and $id_column=$id";
                $result = $conn->query($sql);
                $response['increment'] += 1;
            }
        } else {
            // allready exists
            $sql = "delete from $table1 where user_id=$user_id and $id_column=$id";
            if ($conn->query($sql)) {
                $response['increment'] = -1;
                $response['message'] = 'Removed vote from post with id ' . $id;
            } else {
                $response['message'] = 'SQL ERROR: ' . $conn->error;
                $response['error'] = 1;
            }
        }
    }

    echo json_encode($response);
?>