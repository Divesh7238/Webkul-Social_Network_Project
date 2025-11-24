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
        return $this->getByUserWithReactions($user_id, $user_id);
    }
    public function getByUserWithReactions($owner_id, $current_user_id) {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                r.reaction_type AS user_reaction
            FROM posts p
            LEFT JOIN post_reactions r ON p.id = r.post_id AND r.user_id = ?
            WHERE p.user_id=? 
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$current_user_id, $owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete($post_id,$user_id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
        return $stmt->execute([$post_id,$user_id]);
    }
    private function getReaction($user_id, $post_id) {
        $stmt = $this->db->prepare("SELECT reaction_type FROM post_reactions WHERE user_id=? AND post_id=?");
        $stmt->execute([$user_id, $post_id]);
        return $stmt->fetchColumn();
    }
    private function addReaction($user_id, $post_id, $type) {
        $stmt = $this->db->prepare("INSERT INTO post_reactions (user_id, post_id, reaction_type) VALUES (?,?,?)");
        $stmt->execute([$user_id, $post_id, $type]);
        $this->db->prepare("UPDATE posts SET {$type}s={$type}s+1 WHERE id=?")->execute([$post_id]);
    }
    private function updateReaction($user_id, $post_id, $old_type, $new_type) {
        $stmt = $this->db->prepare("UPDATE post_reactions SET reaction_type=? WHERE user_id=? AND post_id=?");
        $stmt->execute([$new_type, $user_id, $post_id]);
        $this->db->prepare("UPDATE posts SET {$old_type}s={$old_type}s-1, {$new_type}s={$new_type}s+1 WHERE id=?")->execute([$post_id]);
    }
    private function removeReaction($user_id, $post_id, $type) {
        $stmt = $this->db->prepare("DELETE FROM post_reactions WHERE user_id=? AND post_id=?");
        $stmt->execute([$user_id, $post_id]);
        $this->db->prepare("UPDATE posts SET {$type}s={$type}s-1 WHERE id=?")->execute([$post_id]);
    }
    public function processReaction($user_id, $post_id, $new_type) {
        $this->db->beginTransaction();
        try {
            $old_type = $this->getReaction($user_id, $post_id);
            if (!$old_type) {
                $this->addReaction($user_id, $post_id, $new_type);
            } elseif ($old_type === $new_type) {
                $this->removeReaction($user_id, $post_id, $old_type);
                $new_type = null;
            } else {
                $this->updateReaction($user_id, $post_id, $old_type, $new_type);
            }
            $stmt = $this->db->prepare("SELECT likes, dislikes FROM posts WHERE id=?");
            $stmt->execute([$post_id]);
            $counts = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->commit();
            return ['status' => 'ok', 'likes' => $counts['likes'], 'dislikes' => $counts['dislikes'], 'new_reaction' => $new_type];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['status' => 'error'];
        }
    }
}