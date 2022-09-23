<?php
session_start();
require_once 'require/db_connect.php';
function register($username, $password, $verify, $code) {
    if (empty($username)) return 'Username is required';
    if (!preg_match("/\w{3,25}/", $username)) return 'Username invalid';
    if (empty($password)) return 'Password is required';
    if (!preg_match("/.{6,}/", $password)) return 'Password must be at least 6 characters long';
    if ($password !== $verify) {
        unset($_POST['verify']);
        return 'Passwords do not match';
    }

    global $conn;
    $sql = "SELECT username FROM users WHERE username = '$username'";
    if (rows($sql) > 0) return 'Username already exists';
    if (!isset($_SESSION['captcha'])) return 'Session Error has occured';

    if ($_SESSION['captcha'] != $code) {
        unset($_SESSION['captcha']);
        return 'Captcha Invalid';
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if (execute($sql) !== TRUE) return 'Error: ' . $sql . '<br>' . $conn->error;
}
if (isset($_POST['username'], $_POST['password'], $_POST['verify'], $_POST['code'])) {
    $code = htmlspecialchars($_POST['code']);
    $username = trim(htmlspecialchars($_POST['username']));
    $password = htmlspecialchars($_POST['password']);
    $verify = htmlspecialchars($_POST['verify']);
    $code = htmlspecialchars($_POST['code']);
    $error = register($username, $password, $verify, $code);
    if (empty($error)) {
        if (isset($_POST['pfp'])) {
            $image_data = str_replace(' ', '+', $_POST['pfp']);
            $image_data =  substr($image_data, strpos($image_data, ",") + 1);
            $image_data = base64_decode($image_data);
            file_put_contents('resources/pfps/' . $username . '.png', $image_data);
        }
        $user = row('select * from users where username = \'' . $_POST['username'] . '\'');
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
    }
}
function value($field) {
    if (isset($_POST[$field])) {
        return htmlspecialchars($_POST[$field]);
    }
}

// include header
$header['flex'] = true;
require('require/header.php');

// captcha
require 'require/captcha.php';
$session_code = isset($_SESSION['captcha']) ? $_SESSION['captcha'] : createCaptchaCode();
if (!isset($_SESSION['captcha'])) {
    $session_code = createCaptchaCode();
    $_SESSION['captcha'] = $session_code;
}
$data = createCaptcha($session_code);
$captcha = '<img class="captcha" src="data:image/png;base64,' . $data . '" alt="Captcha">';
?>

<form action="register.php" method="post" class="login" enctype="multipart/form-data">
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (isset($error)) : ?>
        <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <div class="pfp-container">
            <img class="pfp medium" id="user-pfp" alt="random pfp">
            <button aria-label="next-pfp" type="button" id="next-pfp-btn">random();</button>
        </div>
        <input type="text" id="username" name="username" placeholder="Username" required pattern="\w{3,25}"
            title="must consist of 3 to 25 letters, numbers and underscores" value="<?= value('username') ?>">
        <input type="password" id="password" name="password" placeholder="Password" required pattern=".{6,}"
            title="must be at least 6 characters long" value="<?= value('password') ?>">
        <input type="password" id="verify" name="verify" placeholder="Verify Password" required
            value="<?= value('verify') ?>">
        <div class="captcha-container">
            <?= $captcha ?>
            <button type="button" id="next-captcha-btn">New</button>
        </div>
        <input type="text" id="captcha" name="code" placeholder="Captcha Code" required value="<?= value('code') ?>">

        <input type="submit" name="submit" value="Sign Up">
        <input type="hidden" name="pfp" id="pfp" value="<?= value('pfp') ?>">
        <p>Already a member?&nbsp;<a href="login.php">LogIn</a></p>
    </div>
    <script src="js/register.js"></script>
</form>
</body>

</html>