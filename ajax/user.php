<?php
require '../config.php';
require '../classes/Database.php';
require '../classes/User.php';
$db = (new Database())->pdo();
$user = new User($db);
header('Content-Type: application/json');
if(empty($_SESSION['user_id'])){ echo json_encode(['status'=>'error']); exit; }
$action = $_POST['action'] ?? '';
if($action==='update_field'){
    $field = $_POST['field'];
    $value = $_POST['value'];
    if($field==='fullname' || $field==='age'){
        $user->updateProfile($_SESSION['user_id'], $field==='fullname' ? $value : null, $field==='age' ? intval($value) : null);
        echo json_encode(['status'=>'ok']);
        exit;
    }
}
if($action==='upload_avatar'){
    if(!empty($_FILES['avatar']['name'])){
        $f = $_FILES['avatar'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        if(!in_array($mime,$allowed) || $f['size']>2*1024*1024){ echo json_encode(['status'=>'error']); exit; }
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $fn = uniqid('av_').'.'.$ext;
        move_uploaded_file($f['tmp_name'], UPLOAD_DIR.$fn);
        $user->updateProfile($_SESSION['user_id'], $_POST['fullname'] ?? null, $_POST['age'] ?? null, $fn);
        echo json_encode(['status'=>'ok','avatar'=>'uploads/'.$fn]);
        exit;
    }
}
echo json_encode(['status'=>'error']);
