<?php
    $conn = new mysqli('127.0.0.1', 'root', '', 'greendit');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    function printSqlErr($sql) {
        global $conn;
        echo '<br><b>QUERY ERROR:</b><code>' . $sql . '</code>';
        die("Query failed: " . $conn->error);
    }

    function getField($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) printSqlErr($sql);
        echo var_dump($result);
        if ($result->num_rows > 0) {
            $result = $result->fetch_array();
            echo var_dump($result);
            return $result[0];
        }
        return null;
    }

    function execute($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) printSqlErr($sql);
        return $result;
    }

    function query($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) printSqlErr($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    function rows($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) printSqlErr($sql);
        return $result->num_rows;
    }
    
    function row($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) printSqlErr($sql);
        return $result->fetch_assoc();
    }
?>