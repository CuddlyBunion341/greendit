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
        if ($result->num_rows > 0) {
            $result = $result->fetch_array();
            return $result[0];
        }
        return null;
    }

    function execute($sql) {
        global $conn;
        $result = $conn->query($sql);
        if (!$result) {
            printSqlErr($sql);
            return false;
        }
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

    function exists($sql) {
        return rows($sql) > 0;
    }

    function toggle($query, $insert, $delete) {
        if (rows($query) == 0) {
            if (execute($insert)) {
                return 0;
            };
        } else {
            if (execute($delete)) {
                return 1;
            };
        }
        return -1;
    }
?>