<?php require 'require/header.php'; ?>
<center>
    <style>
    body {
        display: flex;
        flex-direction: column;
    }

    center {
        flex-grow: 1;
    }
    </style>
    <div class="message">
        <?php
        echo $_SERVER['REQUEST_URI']; /// debyug
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
        } else {
            $code = $_SERVER['REDIRECT_STATUS'];
        }
        $codes = array(
            400 => ['Bad Request', 'Your browser sent a request that this server could not understand.'],
            401 => ['Unauthorized', 'You are not logged in.'],
            403 => ['Forbidden', 'You are not allowed to access this page.'],
            404 => ['Not Found', 'The page you are looking for does not exist.'],
            410 => ['Gone', 'This page has been deleted.'],
            418 => ['User not found', 'The user you are looking for does not exist.'],
            419 => ['Community not found', 'The community you are looking for does not exist.'],
            502 => ['Bad Gateway', 'The server was unable to process your request.'],
            503 => ['Service Unavailable', 'The server is currently unavailable.'],
        );
        $source_url = 'http' . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (array_key_exists($code, $codes) && is_numeric($code)) {
            $name = $codes[$code][0];
            $message = $codes[$code][1];
            if ($code == 418) $code = '404';
            echo '<h1>' . $code . '</h1>';
            echo '<h2>' . $name . '</h2>';
            echo '<p>' . $message . '</p>';
        } else {
            echo '<h1>500</h1>';
            echo '<h2>Internal Server Error</h2>';
            echo '<p>The server encountered an error.</p>';
        }
        echo '<a href="index.php">Go back</a>';
        ?>
    </div>
</center>
<?php require 'require/footer.php'; ?>