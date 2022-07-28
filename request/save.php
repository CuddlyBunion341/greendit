<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        exit();
    }
    if (!isset($_POST['post']) && !isset($_POST['comment'])) {
        http_response_code(400);
        exit();
    }

    require '../require/db_connect.php';

    if (isset($_POST['post'])) {
        $values = [
            $_POST['post'],
            'posts',
            'saved_posts',
            'post_id'
        ];
    } else {
        $values = [
            $_POST['comment'],
            'comments',
            'saved_comments',
            'comment_id',
        ];
    }
    [$hash,$table1,$table2,$id_col] = $values;
    $hash = htmlspecialchars($hash);
    $id = getField("select $id_col from $table1 where hash = '$hash'");
    if (!$id) {
        http_response_code(410);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $toggle = toggle(
        "select * from $table2 where user_id = $user_id and $id_col = $id",
        "insert into $table2 (user_id, $id_col) values ($user_id, $id)",
        "delete from $table2 where user_id = $user_id and $id_col = $id"
    );

    $response = array('toggle' => $toggle);

    if ($toggle == -1) {
        $message = $conn -> error;
    } else {
        $message = ($toggle==1?'un':'').'saved '.(isset($_POST['post'])?'post':'comment').' with hash = ' . $hash;
    }
    $response['message'] = $message;

    echo json_encode($response);

?>