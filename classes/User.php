<?php
class User {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function register($fullname,$email,$password,$dob,$avatarName) {
        $hash = password_hash($password,PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (fullname,email,password,dob,avatar) VALUES (?,?,?,?,?)");
        return $stmt->execute([$fullname,$email,$hash,$dob,$avatarName]);
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
    public function updateProfile($id,$fullname,$dob,$avatarName=null) {
        if($avatarName){
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, dob=?, avatar=? WHERE id=?");
            return $stmt->execute([$fullname,$dob,$avatarName,$id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, dob=? WHERE id=?");
            return $stmt->execute([$fullname,$dob,$id]);
        }
    }
}