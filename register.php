<?php require('templates/header.php'); ?>

<form action="register.php" method="post">
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="text" name="username" id="username" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="Password">
        <input type="password" name="verify" id="verify" placeholder="Verify Password">
        <input type="submit" name="submit" value="Sign Up">
    </div>
</form>