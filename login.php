<?php require('require/header.php'); ?>

<?php
    if (isset($_POST['username'],$_POST['password'])) {
        require_once 'require/db_connect.php';
        $password = htmlspecialchars($_POST['password']);
        $username = htmlspecialchars($_POST['username']);

        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $location = isset($_SESSION['HTTP_REFERER']) ? $_SESSION['HTTP_REFERER'] : 'index.php';
            header('Location: '.$location);
        } else {
            $error = 'Incorrect username or password';
        }

    }
?>

<form action="login.php" method="post" class="login">
    <div class="container">
        <h1>Log In</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="text" name="username" id="username" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="Password">
        <input type="submit" name="submit" value="Login">
        <p>Not a member?&nbsp;<a href="register.php">SignUp</a></p>
    </div>
</form>