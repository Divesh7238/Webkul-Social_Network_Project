<?php
class Post {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function create($user_id,$imageName,$description) {
        $stmt = $this->db->prepare("INSERT INTO posts (user_id,image,description) VALUES (?,?,?)");
        return $stmt->execute([$user_id,$imageName,$description]);
    }
    public function getByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete($post_id,$user_id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
        return $stmt->execute([$post_id,$user_id]);
    }
    public function updateLikes($post_id,$type) {
        if($type==='like'){
            $stmt = $this->db->prepare("UPDATE posts SET likes=likes+1 WHERE id=?");
        } else {
            $stmt = $this->db->prepare("UPDATE posts SET dislikes=dislikes+1 WHERE id=?");
        }
        return $stmt->execute([$post_id]);
    }
}
