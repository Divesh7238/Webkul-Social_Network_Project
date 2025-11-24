<?php
require 'config.php';
require 'classes/Database.php';
require 'classes/User.php';
$db = (new Database())->pdo();
$user = new User($db);
$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $u = $user->getByEmail($email);
    if(!$u || !password_verify($password,$u['password'])) $errors[] = 'Invalid credentials';
    if(empty($errors)){
        $_SESSION['user_id'] = $u['id'];
        header('Location: profile.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="assets/css/style.css">
<title>Login</title>
</head>
<body>
<div class="container">
<h2>Login</h2>
<?php if(!empty($errors)): ?>
<div class="errors"><?php foreach($errors as $e) echo "<div>$e</div>"; ?></div>
<?php endif; ?>
<form method="post" id="loginForm">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<p>Don't have account? <a href="signup.php">Signup</a></p>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
