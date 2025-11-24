<?php
class User {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function register($fullname,$email,$password,$age,$avatarName) {
        $hash = password_hash($password,PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (fullname,email,password,age,avatar) VALUES (?,?,?,?,?)");
        return $stmt->execute([$fullname,$email,$hash,$age,$avatarName]);
    }
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateProfile($id,$fullname,$age,$avatarName=null) {
        if($avatarName){
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, age=?, avatar=? WHERE id=?");
            return $stmt->execute([$fullname,$age,$avatarName,$id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, age=? WHERE id=?");
            return $stmt->execute([$fullname,$age,$id]);
        }
    }
}
