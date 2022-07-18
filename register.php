<?php require('templates/header.php'); ?>

<?php
    function register($username, $password, $verify) {
        require_once 'config/db_connect.php';
        if (empty($username)) return 'Username is required';
        if (empty($password)) return 'Password is required';
        if ($password !== $verify) return 'Passwords do not match';
        $sql = "SELECT username FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) return 'Username already exists';
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) !== TRUE) return 'Error: ' . $sql . '<br>' . $conn->error;
    }
    if (isset($_POST['username'],$_POST['password'],$_POST['verify'])) {
        $username = trim(htmlspecialchars($_POST['username']));
        $password = htmlspecialchars($_POST['password']);
        $verify = htmlspecialchars($_POST['verify']);
        $error = register($username,$password,$verify);
        if (empty($error)) {
            $_SESSION['user_id'] = $user['id'];
            $location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
            header('Location: '.$location);
        }
    }
?>

<form action="register.php" method="post" class="login">
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="text" name="username" id="username" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="Password">
        <input type="password" name="verify" id="verify" placeholder="Verify Password">
        <input type="submit" name="submit" value="Sign Up">
        <p>Allready a member?&nbsp;<a href="login.php">LogIn</a></p>
    </div>
</form>