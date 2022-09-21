<?php
if (!isset($_SESSION['captcha'])) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = substr(str_shuffle(str_repeat($chars, 6)), 0, 6);
    $hash = hash('md5', $code);
    $data = createCaptcha($code);
    $captcha = '<img class="captcha" src="data:image/png;base64,' . $data . '" alt="Captcha">';
}
require_once 'require/db_connect.php';
function register($username, $password, $verify, $code) {
    if (!preg_match('/([a-zA-Z0-9]{4}-){2}[a-zA-Z0-9]{4}/', $code)) return 'Access Code format invalid';
    global $conn;
    if (empty($username)) return 'Username is required';
    if (!preg_match("/\w{3,25}/", $username)) return 'Username invalid';
    if (empty($password)) return 'Password is required';
    if (!preg_match("/.{6,}/", $password)) return 'Password must be at least 6 characters long';
    if ($password !== $verify) return 'Passwords do not match';
    $sql = "SELECT username FROM users WHERE username = '$username'";
    $result = rows($sql);
    if ($result > 0) return 'Username already exists';
    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = 'SELECT code FROM access_codes WHERE code = \'' . $code . '\' AND user_id is null';
    if (!exists($sql)) return 'Access Code invalid or in use';
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if (execute($sql) !== TRUE) return 'Error: ' . $sql . '<br>' . $conn->error;
    execute('UPDATE access_codes SET user_id = (SELECT user_id FROM users WHERE username = \'' . $username . '\') WHERE code = \'' . $code . '\'');
}
if (isset($_POST['username'], $_POST['password'], $_POST['verify'], $_POST['code'])) {
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
function createCaptcha($code, $width = 130, $height = 50, $font_size = 30) {
    // create base image
    $img = new Imagick();
    $img->newImage($width, $height, '#fff');

    // draw font
    $draw = new ImagickDraw();
    $draw->setFont(__DIR__ . '/resources/fonts/Monaco.ttf');
    $draw->setFontSize($font_size);
    $img->annotateImage($draw, 10, $height / 2 + $font_size / 2, 0, $code);

    // add noise
    $img->waveImage(2, 20);
    $img->addNoiseImage(3);

    // add lines
    $draw = new ImagickDraw();
    $draw->setStrokeColor(new ImagickPixel('#000'));
    $img->drawImage($draw);
    for ($i = 0; $i < 5; $i++) {
        $x1 = rand(0, $width);
        $x2 = rand(0, $width);
        $y1 = rand(0, $height);
        $y2 = rand(0, $height);
        $draw->line($x1, $y1, $x2, $y2);
    }

    // export image
    $img->drawImage($draw);
    $img->setImageFormat('png');
    $data = $img->getImageBlob();
    $data = base64_encode($data);
    return $data;
}

$header['flex'] = true;
require('require/header.php');
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
            title="must be at least 6 characters long">
        <input type="password" id="verify" name="verify" placeholder="Verify Password" required>
        <input type="text" id="code" name="code" placeholder="Access code" required
            pattern="([a-zA-Z0-9]{4}-){2}[a-zA-Z0-9]{4}" title="XXXX-XXXX-XXXX" value="<?= value('code') ?>">
        <?= $captcha ?>
        <input type="text" id="captcha" name="captcha" placeholder="Captcha Code" required pattern="[a-zA-Z0-9]{6}">

        <input type="submit" name="submit" value="Sign Up">
        <input type="hidden" name="pfp" id="pfp" value="<?= value('pfp') ?>">
        <p>Already a member?&nbsp;<a href="login.php">LogIn</a></p>
    </div>
    <script src="js/register.js"></script>
</form>
</body>

</html>