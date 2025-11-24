<?php
require '../config.php';
require '../classes/Database.php';
require '../classes/Post.php';
$db = (new Database())->pdo();
$post = new Post($db);
header('Content-Type: application/json');
if(empty($_SESSION['user_id'])){ echo json_encode(['status'=>'error']); exit; }
$action = $_POST['action'] ?? '';
if($action==='create'){
    $desc = trim($_POST['description'] ?? '');
    $imgName = null;
    if(!empty($_FILES['post_image']['name'])){
        $f = $_FILES['post_image'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        if(!in_array($mime,$allowed) || $f['size']>3*1024*1024){ echo json_encode(['status'=>'error']); exit; }
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $imgName = uniqid('p_').'.'.$ext;
        move_uploaded_file($f['tmp_name'], UPLOAD_DIR.$imgName);
    }
    $post->create($_SESSION['user_id'],$imgName,$desc);
    echo json_encode(['status'=>'ok']);
    exit;
}
if($action==='delete'){
    $pid = intval($_POST['post_id']);
    $post->delete($pid,$_SESSION['user_id']);
    echo json_encode(['status'=>'ok']);
    exit;
}
if($action==='react'){
    $pid = intval($_POST['post_id']);
    $type = $_POST['type'];
    if($type==='like' || $type==='dislike'){
        $post->updateLikes($pid,$type);
        echo json_encode(['status'=>'ok']);
        exit;
    }
}
echo json_encode(['status'=>'error']);
