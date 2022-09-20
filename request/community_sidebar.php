<?php
    if (!isset($_GET['name'])) {
        http_response_code(400);
        exit('No community specified');
    }
    $name = trim(htmlspecialchars($_GET['name']));
    if (strlen($name) == 0) {
        http_response_code(400);
        exit('No community specified');
    }
    require __DIR__ . '/../require/db_connect.php';
    require __DIR__ . '/../require/feed.php';
    if (!($sub = row('select * from communities where shortname = \'' . $name . '\''))) {
        http_response_code(404);
        exit('Community not found');
    }
    communitySidebarHTML($sub);