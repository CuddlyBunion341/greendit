<?php require('templates/header.php'); ?>

<?php
    if (isset($_POST['username'],$_POST['password'])) {
        require 'config/db_connect.php';
        $password = htmlspecialchars($_POST['password']);
        $username = htmlspecialchars($_POST['username']);

        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
        } else {
            $error = 'Incorrect username or password';
        }

    }
?>

<form action="login.php" method="post">
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

<?php require('templates/footer.php'); ?>