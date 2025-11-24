<?php
session_start();

// If user is already logged in → go to profile
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h1>Welcome to the Social Network</h1>
    
    <p><a href="login.php">Login</a></p>
    <p><a href="signup.php">Create an Account</a></p>
</div>

</body>
</html>
