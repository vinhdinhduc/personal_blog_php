<?php

/**
 * Comment Model
 * Xử lý hệ thống bình luận đa cấp (threaded comments)
 */

require_once __DIR__ . '/../../config/database.php';

class CommentModel
{
    private $conn;
    private $table = 'comments';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Tạo comment mới
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        try {
            // Validate
            if (empty($data['content'])) {
                return ['success' => false, 'message' => 'Nội dung bình luận không được trống'];
            }

            // Kiểm tra parent comment tồn tại nếu có
            if (!empty($data['parent_id'])) {
                if (!$this->exists($data['parent_id'])) {
                    return ['success' => false, 'message' => 'Comment cha không tồn tại'];
                }
            }

            $query = "INSERT INTO {$this->table} (post_id, user_id, parent_id, content, is_approved) 
                      VALUES (:post_id, :user_id, :parent_id, :content, :is_approved)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':post_id', $data['post_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);

            $parentId = $data['parent_id'] ?? null;
            $stmt->bindParam(':parent_id', $parentId, PDO::PARAM_INT);

            $stmt->bindParam(':content', $data['content']);

            // Auto-approve cho admin, user thường cần approve
            $isApproved = $data['is_approved'] ?? false;
            $stmt->bindParam(':is_approved', $isApproved, PDO::PARAM_BOOL);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Bình luận thành công',
                    'comment_id' => $this->conn->lastInsertId(),
                    'needs_approval' => !$isApproved
                ];
            }

            return ['success' => false, 'message' => 'Không thể tạo bình luận'];
        } catch (PDOException $e) {
            error_log("Comment Create Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database'];
        }
    }

    /**
     * Cập nhật comment
     * @param int $id
     * @param string $content
     * @return bool
     */
    public function update($id, $content)
    {
        try {
            $query = "UPDATE {$this->table} 
                      SET content = :content, updated_at = CURRENT_TIMESTAMP 
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Comment Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa comment
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Xóa comment sẽ cascade xóa tất cả replies (do foreign key)
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Comment Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve comment
     * @param int $id
     * @return bool
     */
    public function approve($id)
    {
        try {
            $query = "UPDATE {$this->table} SET is_approved = TRUE WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Comment Approve Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unapprove comment
     * @param int $id
     * @return bool
     */
    public function unapprove($id)
    {
        try {
            $query = "UPDATE {$this->table} SET is_approved = FALSE WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Lấy comment theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        $query = "SELECT c.*, u.name as user_name, u.email as user_email
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Lấy tất cả comments của một bài viết (threaded)
     * @param int $postId
     * @param bool $includeUnapproved
     * @return array
     */
    public function getByPost($postId, $includeUnapproved = false)
    {
        // Lấy tất cả comments
        $approvedCondition = $includeUnapproved ? '' : 'AND c.is_approved = TRUE';

        $query = "SELECT c.*, u.name as user_name, u.email as user_email
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id {$approvedCondition}
                  ORDER BY c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        $comments = $stmt->fetchAll();

        // Build threaded structure
        return $this->buildCommentTree($comments);
    }

    /**
     * Build comment tree structure (đệ quy)
     * @param array $comments
     * @param int|null $parentId
     * @return array
     */
    private function buildCommentTree($comments, $parentId = null)
    {
        $tree = [];

        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parentId) {
                // Lấy replies (đệ quy)
                $comment['replies'] = $this->buildCommentTree($comments, $comment['id']);
                $tree[] = $comment;
            }
        }

        return $tree;
    }

    /**
     * Đếm số comments của một bài viết
     * @param int $postId
     * @param bool $approvedOnly
     * @return int
     */
    public function countByPost($postId, $approvedOnly = true)
    {
        $approvedCondition = $approvedOnly ? 'AND is_approved = TRUE' : '';

        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE post_id = :post_id {$approvedCondition}";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy comments gần đây
     * @param int $limit
     * @param bool $approvedOnly
     * @return array
     */
    public function getRecent($limit = 10, $approvedOnly = true)
    {
        $approvedCondition = $approvedOnly ? "WHERE c.is_approved = TRUE" : "";

        $query = "SELECT c.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as user_name,
                         u.email as user_email,
                         p.title as post_title,
                         p.slug as post_slug
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN posts p ON c.post_id = p.id
                  {$approvedCondition}
                  ORDER BY c.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy comments chờ approve
     * @param int $limit
     * @return array
     */
    public function getPendingApproval($limit = 50)
    {
        $query = "SELECT c.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as user_name,
                         u.email as user_email,
                         p.title as post_title,
                         p.slug as post_slug
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN posts p ON c.post_id = p.id
                  WHERE c.is_approved = FALSE
                  ORDER BY c.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy comments của user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUser($userId, $limit = 50)
    {
        $query = "SELECT c.*, 
                         p.title as post_title,
                         p.slug as post_slug
                  FROM {$this->table} c
                  LEFT JOIN posts p ON c.post_id = p.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra comment có tồn tại không
     * @param int $id
     * @return bool
     */
    public function exists($id)
    {
        $query = "SELECT id FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Kiểm tra user có phải owner của comment không
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function isOwner($commentId, $userId)
    {
        $query = "SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $commentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Kiểm tra user có phải author của bài viết chứa comment không
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function isPostAuthor($commentId, $userId)
    {
        $query = "SELECT c.id 
                  FROM {$this->table} c
                  INNER JOIN posts p ON c.post_id = p.id
                  WHERE c.id = :comment_id AND p.user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Lấy thống kê comments
     * @return array
     */
    public function getStats()
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_approved = TRUE THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN is_approved = FALSE THEN 1 ELSE 0 END) as pending
                  FROM {$this->table}";

        $result = $this->conn->query($query)->fetch();

        return [
            'total' => (int)$result['total'],
            'approved' => (int)$result['approved'],
            'pending' => (int)$result['pending']
        ];
    }

    /**
     * Xóa tất cả comments của một bài viết
     * @param int $postId
     * @return bool
     */
    public function deleteByPost($postId)
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE post_id = :post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Comments Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy comment depth (độ sâu của cây comment)
     * @param int $commentId
     * @return int
     */
    public function getDepth($commentId)
    {
        $depth = 0;
        $currentId = $commentId;

        while ($currentId !== null) {
            $query = "SELECT parent_id FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $currentId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            if ($result && $result['parent_id']) {
                $depth++;
                $currentId = $result['parent_id'];
            } else {
                break;
            }
        }

        return $depth;
    }

    /**
     * Lấy số lượng replies của một comment
     * @param int $commentId
     * @return int
     */
    public function countReplies($commentId)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE parent_id = :parent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':parent_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Count comments by conditions
     * @param array $conditions
     * @return int
     */
    public function count($conditions = [])
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $whereClauses[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $whereSQL = empty($whereClauses) ? '1=1' : implode(' AND ', $whereClauses);

        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereSQL}";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();

        return (int)$result['total'];
    }
}
