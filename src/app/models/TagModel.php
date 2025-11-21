<?php

/**
 * Tag Model
 * Xử lý tags
 */

require_once __DIR__ . '/../../config/database.php';

class TagModel
{
    private $conn;
    private $table = 'tags';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Lấy tất cả tags
     * @return array
     */
    public function getAll()
    {
        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  LEFT JOIN post_tag pt ON t.id = pt.tag_id
                  LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  GROUP BY t.id
                  ORDER BY t.name ASC";

        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tag theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy tag theo slug
     * @param string $slug
     * @return array|null
     */
    public function getBySlug($slug)
    {
        $query = "SELECT * FROM {$this->table} WHERE slug = :slug";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tạo tag mới
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (name, slug) VALUES (:name, :slug)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        }
        return ['success' => false];
    }

    /**
     * Cập nhật tag
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        try {
            // Validate
            if (empty($data['name'])) {
                return ['success' => false, 'message' => 'Tên tag không được trống'];
            }

            // Check if slug exists (excluding current tag)
            if ($this->slugExists($data['slug'], $id)) {
                $data['slug'] = $data['slug'] . '-' . time();
            }

            $query = "UPDATE {$this->table} 
                     SET name = :name, 
                         slug = :slug
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $data['slug']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật tag thành công'
                ];
            }

            return ['success' => false, 'message' => 'Không thể cập nhật tag'];
        } catch (PDOException $e) {
            error_log("Tag Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database'];
        }
    }

    /**
     * Kiểm tra slug đã tồn tại
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    public function slugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $query = "SELECT id FROM {$this->table} WHERE slug = :slug AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        } else {
            $query = "SELECT id FROM {$this->table} WHERE slug = :slug";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':slug', $slug);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Lấy tags phổ biến nhất
     * @param int $limit
     * @return array
     */
    public function getPopular($limit = 10)
    {
        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  INNER JOIN post_tag pt ON t.id = pt.tag_id
                  INNER JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  GROUP BY t.id
                  HAVING post_count > 0
                  ORDER BY post_count DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm tags
     * @param string $keyword
     * @return array
     */
    public function search($keyword)
    {
        $searchTerm = '%' . $keyword . '%';

        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  LEFT JOIN post_tag pt ON t.id = pt.tag_id
                  LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  WHERE t.name LIKE :search OR t.slug LIKE :search
                  GROUP BY t.id
                  ORDER BY t.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy tags theo post ID
     * @param int $postId
     * @return array
     */
    public function getByPostId($postId)
    {
        $query = "SELECT t.* FROM {$this->table} t
                  INNER JOIN post_tag pt ON t.id = pt.tag_id
                  WHERE pt.post_id = :post_id
                  ORDER BY t.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm số lượng tags
     * @return int
     */
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->conn->query($query)->fetch();
        return (int)$result['total'];
    }

    /**
     * Lấy tag cloud (với size dựa trên số lượng posts)
     * @param int $limit
     * @return array
     */
    public function getTagCloud($limit = 30)
    {
        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  INNER JOIN post_tag pt ON t.id = pt.tag_id
                  INNER JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  GROUP BY t.id
                  HAVING post_count > 0
                  ORDER BY RAND()
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $tags = $stmt->fetchAll();

        // Calculate size class based on post count
        if (!empty($tags)) {
            $counts = array_column($tags, 'post_count');
            $min = min($counts);
            $max = max($counts);
            $spread = $max - $min;

            foreach ($tags as &$tag) {
                if ($spread == 0) {
                    $tag['size'] = 'medium';
                } else {
                    $normalized = ($tag['post_count'] - $min) / $spread;
                    if ($normalized < 0.33) {
                        $tag['size'] = 'small';
                    } elseif ($normalized < 0.66) {
                        $tag['size'] = 'medium';
                    } else {
                        $tag['size'] = 'large';
                    }
                }
            }
        }

        return $tags;
    }

    /**
     * Gắn tag vào post
     * @param int $postId
     * @param int $tagId
     * @return bool
     */
    public function attachToPost($postId, $tagId)
    {
        $query = "INSERT IGNORE INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Gỡ tag khỏi post
     * @param int $postId
     * @param int $tagId
     * @return bool
     */
    public function detachFromPost($postId, $tagId)
    {
        $query = "DELETE FROM post_tag WHERE post_id = :post_id AND tag_id = :tag_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đồng bộ tags cho post (xóa hết rồi add lại)
     * @param int $postId
     * @param array $tagIds
     * @return bool
     */
    public function syncToPost($postId, $tagIds)
    {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // Delete existing tags
            $deleteQuery = "DELETE FROM post_tag WHERE post_id = :post_id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Insert new tags
            if (!empty($tagIds)) {
                $insertQuery = "INSERT INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";
                $insertStmt = $this->conn->prepare($insertQuery);

                foreach ($tagIds as $tagId) {
                    $insertStmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
                    $insertStmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
                    $insertStmt->execute();
                }
            }

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback on error
            $this->conn->rollBack();
            error_log("Tag Sync Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm hoặc tạo tag mới từ tên
     * @param string $name
     * @return int|false Tag ID hoặc false nếu lỗi
     */
    public function findOrCreate($name)
    {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }

        $slug = Security::createSlug($name);

        // Tìm tag theo slug
        $tag = $this->getBySlug($slug);

        if ($tag) {
            return $tag['id'];
        }

        // Tạo mới nếu chưa có
        $result = $this->create([
            'name' => $name,
            'slug' => $slug
        ]);

        return $result['success'] ? $result['id'] : false;
    }

    /**
     * Xóa tag
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // Xóa relationships với posts trước
            $deleteRelations = "DELETE FROM post_tag WHERE tag_id = :tag_id";
            $stmtRelations = $this->conn->prepare($deleteRelations);
            $stmtRelations->bindParam(':tag_id', $id, PDO::PARAM_INT);
            $stmtRelations->execute();

            // Xóa tag
            $deleteTag = "DELETE FROM {$this->table} WHERE id = :id";
            $stmtTag = $this->conn->prepare($deleteTag);
            $stmtTag->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmtTag->execute();

            // Commit transaction
            $this->conn->commit();

            return $result && $stmtTag->rowCount() > 0;
        } catch (PDOException $e) {
            // Rollback on error
            $this->conn->rollBack();
            error_log("Tag Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa nhiều tags cùng lúc
     * @param array $ids
     * @return array ['success' => bool, 'deleted_count' => int, 'message' => string]
     */
    public function bulkDelete($ids)
    {
        if (empty($ids)) {
            return [
                'success' => false,
                'deleted_count' => 0,
                'message' => 'Không có tag nào được chọn'
            ];
        }

        try {
            $this->conn->beginTransaction();

            $count = 0;
            foreach ($ids as $id) {
                // Xóa relationships
                $deleteRelations = "DELETE FROM post_tag WHERE tag_id = :tag_id";
                $stmtRelations = $this->conn->prepare($deleteRelations);
                $stmtRelations->bindParam(':tag_id', $id, PDO::PARAM_INT);
                $stmtRelations->execute();

                // Xóa tag
                $deleteTag = "DELETE FROM {$this->table} WHERE id = :id";
                $stmtTag = $this->conn->prepare($deleteTag);
                $stmtTag->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmtTag->execute() && $stmtTag->rowCount() > 0) {
                    $count++;
                }
            }

            $this->conn->commit();

            return [
                'success' => true,
                'deleted_count' => $count,
                'message' => "Đã xóa {$count} tag"
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Tag Bulk Delete Error: " . $e->getMessage());

            return [
                'success' => false,
                'deleted_count' => 0,
                'message' => 'Lỗi khi xóa tags'
            ];
        }
    }

    /**
     * Kiểm tra tag có đang được sử dụng không
     * @param int $id
     * @return bool
     */
    public function isInUse($id)
    {
        $query = "SELECT COUNT(*) as count FROM post_tag WHERE tag_id = :tag_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tag_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Xóa tag an toàn (chỉ xóa nếu không còn sử dụng)
     * @param int $id
     * @param bool $force Bắt buộc xóa ngay cả khi đang sử dụng
     * @return array
     */
    public function safeDelete($id, $force = false)
    {
        if (!$force && $this->isInUse($id)) {
            $tag = $this->getById($id);
            $usageCount = $this->getUsageCount($id);

            return [
                'success' => false,
                'message' => "Tag '{$tag['name']}' đang được sử dụng bởi {$usageCount} bài viết. Bạn có chắc muốn xóa không?",
                'in_use' => true,
                'usage_count' => $usageCount
            ];
        }

        if ($this->delete($id)) {
            return [
                'success' => true,
                'message' => 'Xóa tag thành công',
                'in_use' => false
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xóa tag',
            'in_use' => false
        ];
    }

    /**
     * Lấy số lượng bài viết sử dụng tag
     * @param int $id
     * @return int
     */
    public function getUsageCount($id)
    {
        $query = "SELECT COUNT(*) as count FROM post_tag WHERE tag_id = :tag_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tag_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return (int)$result['count'];
    }
}
