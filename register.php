<?php require('require/header.php'); ?>

<?php
    require_once 'require/db_connect.php';
    function register($username, $password, $verify) {
        global $conn;
        if (empty($username)) return 'Username is required';
        if (empty($password)) return 'Password is required';
        if ($password !== $verify) return 'Passwords do not match';
        $sql = "SELECT username FROM users WHERE username = '$username'";
        $result = rows($sql);
        if ($result > 0) return 'Username already exists';
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if (execute($sql) !== TRUE) return 'Error: ' . $sql . '<br>' . $conn->error;
    }
    if (isset($_POST['username'],$_POST['password'],$_POST['verify'])) {
        $username = trim(htmlspecialchars($_POST['username']));
        $password = htmlspecialchars($_POST['password']);
        $verify = htmlspecialchars($_POST['verify']);
        $error = register($username,$password,$verify);
        if (empty($error)) {
            if (isset($_POST['pfp'])) {
                $image_data = str_replace(' ','+',$_POST['pfp']);
                $image_data =  substr($image_data,strpos($image_data,",")+1);
                $image_data = base64_decode($image_data);
                file_put_contents('resources/pfps/'.$username.'.png',$image_data);
            }
            $user = row('select * from users where username = \''.$_POST['username'].'\'');
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            // todo: redirect
            // $location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
            // header('Location: '.$location);
            header('Location: index.php');
        }
    }
?>

<form action="register.php" method="post" class="login" enctype="multipart/form-data">
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="text" name="username" id="username" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="Password">
        <input type="password" name="verify" id="verify" placeholder="Verify Password">
        <input type="submit" name="submit" value="Sign Up">
        <input type="hidden" name="pfp" id="pfp">
        <p>Allready a member?&nbsp;<a href="login.php">LogIn</a></p>
    </div>
    <script src="js/register.js"></script>
</form>