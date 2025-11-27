<?php

/**
 * Post Model
 * Xử lý các thao tác CRUD với bài viết
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class PostModel
{
    public $conn;
    private $table = 'posts';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Tạo bài viết mới
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        try {
            // Validate
            if (empty($data['title']) || empty($data['content'])) {
                return ['success' => false, 'message' => 'Tiêu đề và nội dung không được trống'];
            }

            // Tạo slug nếu chưa có
            $slug = !empty($data['slug']) ? $data['slug'] : Security::createSlug($data['title']);

            // Kiểm tra slug trùng
            if ($this->slugExists($slug)) {
                $slug = $slug . '-' . time();
            }

            $query = "INSERT INTO {$this->table} 
                     (user_id, category_id, title, slug, excerpt, content, cover_image, status) 
                     VALUES (:user_id, :category_id, :title, :slug, :excerpt, :content, :cover_image, :status)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':excerpt', $data['excerpt']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':cover_image', $data['cover_image']);

            $status = $data['status'] ?? 'draft';
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                $postId = $this->conn->lastInsertId();

                // Lưu tags
                if (!empty($data['tags'])) {
                    $this->saveTags($postId, $data['tags']);
                }

                return [
                    'success' => true,
                    'message' => 'Tạo bài viết thành công',
                    'post_id' => $postId,
                    // 'slug' => $slug
                ];
            }

            return ['success' => false, 'message' => 'Không thể tạo bài viết'];
        } catch (PDOException $e) {
            error_log("Post Create Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database'];
        }
    }

    /**
     * Cập nhật bài viết
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        try {
            // Validate
            if (empty($data['title']) || empty($data['content'])) {
                return ['success' => false, 'message' => 'Tiêu đề và nội dung không được trống'];
            }

            // Tạo slug mới nếu có
            if (!empty($data['slug'])) {
                $slug = $data['slug'];
            } else {
                // $slug = Security::createSlug($data['title']);
            }

            // Kiểm tra slug trùng (trừ bài hiện tại)
            if ($this->slugExists($slug, $id)) {
                $slug = $slug . '-' . time();
            }

            $query = "UPDATE {$this->table} 
                     SET category_id = :category_id, 
                         title = :title, 
                         slug = :slug, 
                         excerpt = :excerpt, 
                         content = :content, 
                         cover_image = :cover_image, 
                         status = :status,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':excerpt', $data['excerpt']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':cover_image', $data['cover_image']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Cập nhật tags
                if (isset($data['tags'])) {
                    $this->deletePostTags($id);
                    if (!empty($data['tags'])) {
                        $this->saveTags($id, $data['tags']);
                    }
                }

                return [
                    'success' => true,
                    'message' => 'Cập nhật bài viết thành công',
                    'slug' => $slug
                ];
            }

            return ['success' => false, 'message' => 'Không thể cập nhật bài viết'];
        } catch (PDOException $e) {
            error_log("Post Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database'];
        }
    }

    /**
     * Xóa bài viết
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Post Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy bài viết theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $post = $stmt->fetch();

        if ($post) {
            $post['tags'] = $this->getPostTags($id);
        }

        return $post;
    }

    /**
     * Lấy bài viết theo slug
     * @param string $slug
     * @return array|null
     */
    public function getBySlug($slug)
    {
        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         u.email as author_email,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.slug = :slug";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $post = $stmt->fetch();

        if ($post) {
            $this->incrementViews($post['id']);
            $post['tags'] = $this->getPostTags($post['id']);
        }

        return $post;
    }

    /**
     * Lấy danh sách bài viết published có phân trang
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPublishedPosts($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Sửa: dùng CONCAT(u.first_name, ' ', u.last_name) thay vì u.name
        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm số bài viết published
     * @return int
     */
    public function countPublishedPosts()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'published'";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy bài viết theo category
     * @param int $categoryId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByCategory($categoryId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.category_id = :category_id AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm bài viết theo category
     * @param int $categoryId
     * @return int
     */
    public function countByCategory($categoryId)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE category_id = :category_id AND status = 'published'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy bài viết theo tag
     * @param int $tagId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByTag($tagId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  INNER JOIN post_tag pt ON p.id = pt.post_id
                  WHERE pt.tag_id = :tag_id AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm bài viết theo tag
     * @param int $tagId
     * @return int
     */
    public function countByTag($tagId)
    {
        $query = "SELECT COUNT(DISTINCT p.id) as total 
                  FROM {$this->table} p
                  INNER JOIN post_tag pt ON p.id = pt.post_id
                  WHERE pt.tag_id = :tag_id AND p.status = 'published'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Tìm kiếm bài viết
     * @param string $keyword
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function search($keyword, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = '%' . $keyword . '%';

        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         c.name as category_name, 
                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE (p.title LIKE :search OR p.content LIKE :search OR p.excerpt LIKE :search)
                  AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm kết quả tìm kiếm
     * @param string $keyword
     * @return int
     */
    public function countSearch($keyword)
    {
        $searchTerm = '%' . $keyword . '%';

        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE (title LIKE :search OR content LIKE :search OR excerpt LIKE :search)
                  AND status = 'published'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy bài viết gần đây
     * @param int $limit
     * @return array
     */
    public function getRecentPosts($limit = 5)
    {
        $query = "SELECT id, title, slug, created_at
                  FROM {$this->table}
                  WHERE status = 'published'
                  ORDER BY created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy bài viết của user
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByUser($userId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT p.*, c.name as category_name
                  FROM {$this->table} p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
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
     * Lưu tags cho bài viết
     * @param int $postId
     * @param array $tagIds
     */
    private function saveTags($postId, $tagIds)
    {
        if (empty($tagIds)) return;

        $query = "INSERT INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";
        $stmt = $this->conn->prepare($query);

        foreach ($tagIds as $tagId) {
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    /**
     * Xóa tất cả tags của bài viết
     * @param int $postId
     */
    private function deletePostTags($postId)
    {
        $query = "DELETE FROM post_tag WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Lấy tags của bài viết
     * @param int $postId
     * @return array
     */
    public function getPostTags($postId)
    {
        $query = "SELECT t.* FROM tags t
                  INNER JOIN post_tag pt ON t.id = pt.tag_id
                  WHERE pt.post_id = :post_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Tăng lượt xem
     * @param int $postId
     */
    private function incrementViews($postId)
    {
        $query = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Kiểm tra quyền sở hữu bài viết
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function isOwner($postId, $userId)
    {
        $query = "SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Count posts by conditions
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

    /**
     * Lấy tất cả bài viết với details (cho admin)
     * @param int $page
     * @param int $perPage
     * @param string $status Filter theo status (optional)
     * @param string $search Tìm kiếm (optional)
     * @return array
     */
    public function getAllWithDetails($page = 1, $perPage = 20, $status = '', $search = '')
    {
        $offset = ($page - 1) * $perPage;

        $whereClauses = [];
        $params = [];

        // Filter by status
        if (!empty($status)) {
            $whereClauses[] = "p.status = :status";
            $params[':status'] = $status;
        }

        // Search
        if (!empty($search)) {
            $whereClauses[] = "(p.title LIKE :search OR p.content LIKE :search OR p.excerpt LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereSQL = empty($whereClauses) ? '1=1' : implode(' AND ', $whereClauses);

        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         u.email as author_email,
                         c.name as category_name,
                         c.slug as category_slug,
                         COUNT(DISTINCT cm.id) as comment_count,
                         GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') as tags_list
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN comments cm ON p.id = cm.post_id
                  LEFT JOIN post_tag pt ON p.id = pt.post_id
                  LEFT JOIN tags t ON pt.tag_id = t.id
                  WHERE {$whereSQL}
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind search/filter params
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind pagination params
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm tất cả bài viết với filter (cho admin)
     * @param string $status
     * @param string $search
     * @return int
     */
    public function countAllWithDetails($status = '', $search = '')
    {
        $whereClauses = [];
        $params = [];

        if (!empty($status)) {
            $whereClauses[] = "status = :status";
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $whereClauses[] = "(title LIKE :search OR content LIKE :search OR excerpt LIKE :search)";
            $params[':search'] = "%{$search}%";
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
    /**
     * Lấy bài viết nổi bật trong category
     */
    public function getFeaturedPosts($categoryId, $limit = 3)
    {
        $query = "SELECT p.*,
                         CONCAT(u.first_name, ' ', u.last_name) as author_name
                  FROM posts p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.category_id = :category_id
                    AND p.status = 'published'
                  ORDER BY p.views DESC, p.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
