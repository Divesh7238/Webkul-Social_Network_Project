<?php
require 'config.php';
require 'classes/Database.php';
require 'classes/User.php';
require 'classes/Post.php';
if(empty($_SESSION['user_id'])){ header('Location: login.php'); exit; }
$db = (new Database())->pdo();
$user = new User($db);
$post = new Post($db);
$u = $user->getById($_SESSION['user_id']);
$posts = $post->getByUser($u['id']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="assets/css/style.css">
<title>Profile</title>
</head>
<body>
<div class="container profile-page">
<div class="profile-card">
<div class="avatar">
<img src="<?php echo $u['avatar'] ? 'uploads/'.$u['avatar'] : 'assets/default-avatar.png'; ?>" id="profileAvatar">
</div>
<div class="info">
<div class="row"><label>Email:</label><span><?php echo htmlspecialchars($u['email']); ?></span></div>
<div class="row editable" data-field="fullname"><label>Name:</label><span id="fullname"><?php echo htmlspecialchars($u['fullname']); ?></span><button class="edit-btn" data-field="fullname">✎</button></div>
<div class="row editable" data-field="age"><label>Age:</label><span id="age"><?php echo intval($u['age']); ?></span><button class="edit-btn" data-field="age">✎</button></div>
<form id="avatarForm" enctype="multipart/form-data">
<input type="file" name="avatar" id="avatarInput" accept="image/*">
<button type="submit">Upload Avatar</button>
</form>
</div>
</div>
<div class="post-section">
<h3>Create Post</h3>
<form id="postForm" enctype="multipart/form-data">
<textarea name="description" placeholder="Say something..." required></textarea>
<input type="file" name="post_image" accept="image/*">
<button type="submit">Post</button>
</form>
<div id="posts">
<?php foreach($posts as $p): ?>
<div class="post" data-id="<?php echo $p['id']; ?>">
<div class="post-header">
<span class="post-time"><?php echo $p['created_at']; ?></span>
<button class="delete-post" data-id="<?php echo $p['id']; ?>">Delete</button>
</div>
<div class="post-body">
<?php if($p['image']): ?>
<img src="uploads/<?php echo $p['image']; ?>" class="post-img">
<?php endif; ?>
<p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
</div>
<div class="post-actions">
<button class="like-btn" data-id="<?php echo $p['id']; ?>">Like (<span class="likes"><?php echo $p['likes']; ?></span>)</button>
<button class="dislike-btn" data-id="<?php echo $p['id']; ?>">Dislike (<span class="dislikes"><?php echo $p['dislikes']; ?></span>)</button>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<p><a href="logout.php">Logout</a></p>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
