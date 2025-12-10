<?php

// Xử lý model bình luận
require_once __DIR__ . '/BaseModel.php';

class CommentModel extends BaseModel
{
    protected $table = 'comments';

    //Tạo comment mới
    public function create($data)
    {
        try {


            // Kiểm tra parent comment tồn tại nếu có
            if (!empty($data['parent_id'])) {
                if (!$this->exists(['id' => $data['parent_id']])) {
                    return ['success' => false, 'message' => 'Comment cha không tồn tại'];
                }
            }

            $isApproved = $data['is_approved'] ?? false;

            $commentId = $this->insert([
                'post_id' => $data['post_id'],
                'user_id' => $data['user_id'],
                'parent_id' => $data['parent_id'] ?? null,
                'content' => $data['content'],
                'is_approved' => $isApproved
            ]);

            if ($commentId) {
                return [
                    'success' => true,
                    'message' => 'Bình luận thành công',
                    'comment_id' => $commentId,
                    'needs_approval' => !$isApproved
                ];
            }

            return ['success' => false, 'message' => 'Không thể tạo bình luận'];
        } catch (PDOException $e) {
            error_log("Comment Create Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database'];
        }
    }
    //Cập nhật comment
    public function update($id, $content)
    {
        try {
            return $this->execute(
                "UPDATE {$this->table} SET content = :content, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
                ['content' => $content, 'id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Comment Update Error: " . $e->getMessage());
            return false;
        }
    }

    //Xoá comment
    public function delete($id)
    {
        try {
            // Xóa comment sẽ cascade xóa tất cả replies 
            return $this->deleteById($id);
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
            return $this->execute(
                "UPDATE {$this->table} SET is_approved = TRUE WHERE id = :id",
                ['id' => $id]
            );
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
            return $this->execute(
                "UPDATE {$this->table} SET is_approved = FALSE WHERE id = :id",
                ['id' => $id]
            );
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
        return $this->queryOne(
            "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email FROM {$this->table} c LEFT JOIN users u ON c.user_id = u.id WHERE c.id = :id",
            ['id' => $id]
        );
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

        $query = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id {$approvedCondition}
                  ORDER BY c.created_at ASC";

        $comments = $this->query($query, ['post_id' => $postId]);

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

        $result = $this->queryOne($query, ['post_id' => $postId]);
        return $result ? (int)$result['total'] : 0;
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

        return $this->query($query, ['limit' => $limit]);
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

        return $this->query($query, ['limit' => $limit]);
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

        return $this->query($query, ['user_id' => $userId, 'limit' => $limit]);
    }

    /**
     * Kiểm tra user có phải owner của comment không
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function isOwner($commentId, $userId)
    {
        return $this->exists(['id' => $commentId, 'user_id' => $userId]);
    }

    /**
     * Kiểm tra user có phải author của bài viết chứa comment không
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function isPostAuthor($commentId, $userId)
    {
        $result = $this->queryOne(
            "SELECT c.id FROM {$this->table} c INNER JOIN posts p ON c.post_id = p.id WHERE c.id = :comment_id AND p.user_id = :user_id",
            ['comment_id' => $commentId, 'user_id' => $userId]
        );

        return $result !== null;
    }

    /**
     * Lấy thống kê comments
     * @return array
     */
    public function getStats()
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) as total, SUM(CASE WHEN is_approved = TRUE THEN 1 ELSE 0 END) as approved, SUM(CASE WHEN is_approved = FALSE THEN 1 ELSE 0 END) as pending FROM {$this->table}"
        );

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
            return $this->execute(
                "DELETE FROM {$this->table} WHERE post_id = :post_id",
                ['post_id' => $postId]
            );
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
            $result = $this->queryOne(
                "SELECT parent_id FROM {$this->table} WHERE id = :id",
                ['id' => $currentId]
            );
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
        $result = $this->queryOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE parent_id = :parent_id",
            ['parent_id' => $commentId]
        );
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Count comments by conditions
     * @param array $conditions
     * @return int
     */
    public function countComments($conditions = [])
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $whereClauses[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        $whereSQL = empty($whereClauses) ? '1=1' : implode(' AND ', $whereClauses);

        $result = $this->queryOne("SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereSQL}", $params);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Get filtered comments với thông tin user, post và pagination
     * @param string $status 'all', 'pending', 'approved'
     * @param int|null $postId
     * @param string $search
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getFilteredComments($status = 'all', $postId = null, $search = '', $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;

        // Build query conditions
        $conditions = [];
        $params = [];

        if ($status === 'pending') {
            $conditions[] = 'c.is_approved = FALSE';
        } elseif ($status === 'approved') {
            $conditions[] = 'c.is_approved = TRUE';
        }

        if ($postId) {
            $conditions[] = 'c.post_id = :post_id';
            $params[':post_id'] = $postId;
        }

        if (!empty($search)) {
            $conditions[] = '(c.content LIKE :search OR CONCAT(u.first_name, " ", u.last_name) LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total 
                       FROM comments c 
                       LEFT JOIN users u ON c.user_id = u.id 
                       WHERE {$whereClause}";

        $stmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = $stmt->fetch()['total'];

        // Get comments
        $query = "SELECT c.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as user_name,
                         u.email as user_email,
                         p.title as post_title,
                         p.slug as post_slug,
                         (SELECT COUNT(*) FROM comments WHERE parent_id = c.id) as reply_count
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN posts p ON c.post_id = p.id
                  WHERE {$whereClause}
                  ORDER BY c.created_at DESC
                  LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'has_prev' => $page > 1,
            'has_next' => $page < ceil($total / $perPage)
        ];
    }

    /**
     * Get replies của một comment kèm thông tin user
     * @param int $parentId
     * @return array
     */
    public function getRepliesWithUser($parentId)
    {
        $query = "SELECT c.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as user_name,
                         u.email as user_email
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.parent_id = :parent_id
                  ORDER BY c.created_at ASC";

        return $this->query($query, ['parent_id' => $parentId]);
    }
}
