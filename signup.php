<?php
require 'config.php';
require 'classes/Database.php';
require 'classes/User.php';
$db = (new Database())->pdo();
$user = new User($db);
$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $age = intval($_POST['age']);
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if(strlen($password) < 6) $errors[] = 'Password too short';
    if($user->getByEmail($email)) $errors[] = 'Email already exists';
    if(!empty($_FILES['avatar']['name'])){
        $f = $_FILES['avatar'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        if($f['size'] > 2*1024*1024) $errors[] = 'File too large';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        if(!in_array($mime,$allowed)) $errors[] = 'Invalid file type';
    }
    if(empty($errors)){
        $avatarName = null;
        if(!empty($_FILES['avatar']['name'])){
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatarName = uniqid('av_').'.'.$ext;
            move_uploaded_file($_FILES['avatar']['tmp_name'], UPLOAD_DIR.$avatarName);
        }
        $user->register($fullname,$email,$password,$age,$avatarName);
        header('Location: login.php');
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
<title>Signup</title>
</head>
<body>
<div class="container">
<h2>Signup</h2>
<?php if(!empty($errors)): ?>
<div class="errors"><?php foreach($errors as $e) echo "<div>$e</div>"; ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" id="signupForm">
<input type="text" name="fullname" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<input type="number" name="age" placeholder="Age" required>
<input type="file" name="avatar" accept="image/*">
<button type="submit">Register</button>
</form>
<p>Already have account? <a href="login.php">Login</a></p>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
