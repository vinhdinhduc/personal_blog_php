<?php


require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../helpers/Security.php';

class PostModel extends BaseModel
{
    protected $table = 'posts';

    //Tạo bài viết mới
    public function create($data)
    {
        try {


            // Tạo slug nếu chưa có
            $slug = !empty($data['slug']) ? $data['slug'] : Security::createSlug($data['title']);

            // Kiểm tra slug trùng
            if ($this->slugExists($slug)) {
                $slug = $slug . '-' . time();
            }

            $status = $data['status'] ?? 'draft';

            $postId = $this->insert([
                'user_id' => $data['user_id'],
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'cover_image' => $data['cover_image'],
                'status' => $status
            ]);

            if ($postId) {

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

    //Cập nhật post
    public function update($id, $data)
    {
        try {


            // Tạo slug mới nếu có
            if (!empty($data['slug'])) {
                $slug = $data['slug'];
            } else {
                $slug = Security::createSlug($data['title']);
            }

            // Kiểm tra slug trùng 
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

            if ($this->execute($query, [
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'cover_image' => $data['cover_image'],
                'status' => $data['status'],
                'id' => $id
            ])) {
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

    //Xoá bài viết
    public function delete($id)
    {
        try {
            return $this->deleteById($id);
        } catch (PDOException $e) {
            error_log("Post Delete Error: " . $e->getMessage());
            return false;
        }
    }
    //Lấy bài viết theo Id
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

        $post = $this->queryOne($query, ['id' => $id]);

        if ($post) {
            $post['tags'] = $this->getPostTags($id);
        }

        return $post;
    }
    //Lấy bài viết theo Slug
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

        $post = $this->queryOne($query, ['slug' => $slug]);

        if ($post) {
            $this->incrementViews($post['id']);
            $post['tags'] = $this->getPostTags($post['id']);
        }

        return $post;
    }

    // Lấy danh sách bài viết published có phân trang

    public function getPublishedPosts($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

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

        return $this->query($query, ['limit' => $perPage, 'offset' => $offset]);
    }

    /**
     * Đếm số bài viết published
   
     */
    public function countPublishedPosts()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'published'");
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy bài viết theo category

     */
    public function getByCategory($categoryId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT p.*, 
                         CONCAT(u.first_name, ' ', u.last_name) as author_name,
                         u.avatar as author_avatar,
                         c.name as category_name, 

                         c.slug as category_slug
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.category_id = :category_id AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        return $this->query($query, ['category_id' => $categoryId, 'limit' => $perPage, 'offset' => $offset]);
    }

    /**
     * Đếm bài viết theo category
 
     */
    public function countByCategory($categoryId)
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE category_id = :category_id AND status = 'published'",
            ['category_id' => $categoryId]
        );
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy bài viết theo tag
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

        return $this->query($query, ['tag_id' => $tagId, 'limit' => $perPage, 'offset' => $offset]);
    }

    /**
     * Đếm bài viết theo tag
     */
    public function countByTag($tagId)
    {
        $result = $this->queryOne(
            "SELECT COUNT(DISTINCT p.id) as total FROM {$this->table} p INNER JOIN post_tag pt ON p.id = pt.post_id WHERE pt.tag_id = :tag_id AND p.status = 'published'",
            ['tag_id' => $tagId]
        );
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Tìm kiếm bài viết
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
                  WHERE (p.title LIKE :search1 OR p.content LIKE :search2 OR p.excerpt LIKE :search3)
                  AND p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        return $this->query($query, [
            'search1' => $searchTerm,
            'search2' => $searchTerm,
            'search3' => $searchTerm,
            'limit' => $perPage,
            'offset' => $offset
        ]);
    }

    /**
     * Đếm kết quả tìm kiếm
     */
    public function countSearch($keyword)
    {
        $searchTerm = '%' . $keyword . '%';

        $result = $this->queryOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE (title LIKE :search1 OR content LIKE :search2 OR excerpt LIKE :search3) AND status = 'published'",
            ['search1' => $searchTerm, 'search2' => $searchTerm, 'search3' => $searchTerm]
        );
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy bài viết gần đây
     */
    public function getRecentPosts($limit = 5)
    {
        $query = "SELECT p.id, p.title, p.slug, p.created_at,p.status,CONCAT(u.first_name, ' ' , u.last_name) as author_name, c.name as category_name
                  FROM posts p
                  INNER JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.status = 'published'
                  ORDER BY p.created_at DESC
                  LIMIT :limit";

        return $this->query($query, ['limit' => $limit]);
    }


    /**
     * Kiểm tra slug đã tồn tại
     */
    public function slugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug AND id != :id",
                ['slug' => $slug, 'id' => $excludeId]
            );
        } else {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug",
                ['slug' => $slug]
            );
        }

        return $result && $result['count'] > 0;
    }

    /**
     * Lưu tags cho bài viết
     */
    private function saveTags($postId, $tagIds)
    {
        if (empty($tagIds)) return;

        $query = "INSERT INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";

        foreach ($tagIds as $tagId) {
            $this->execute($query, ['post_id' => $postId, 'tag_id' => $tagId]);
        }
    }

    /**
     * Xóa tất cả tags của bài viết
     */
    private function deletePostTags($postId)
    {
        $this->execute("DELETE FROM post_tag WHERE post_id = :post_id", ['post_id' => $postId]);
    }

    /**
     * Lấy tags của bài viết
     */
    public function getPostTags($postId)
    {
        return $this->query(
            "SELECT t.* FROM tags t INNER JOIN post_tag pt ON t.id = pt.tag_id WHERE pt.post_id = :post_id",
            ['post_id' => $postId]
        );
    }

    /**
     * Tăng lượt xem
     */
    private function incrementViews($postId)
    {
        $this->execute("UPDATE {$this->table} SET views = views + 1 WHERE id = :id", ['id' => $postId]);
    }

    /**
     * Kiểm tra quyền sở hữu bài viết
     */
    public function isOwner($postId, $userId)
    {
        return $this->exists(['id' => $postId, 'user_id' => $userId]);
    }

    /**
     * Count posts by conditions
     */
    public function countPosts($conditions = [])
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
     * Lấy tất cả bài viết với details (cho admin)
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

        // Merge pagination params
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;

        return $this->query($query, $params);
    }

    /**
     * Đếm tất cả bài viết với filter (cho admin)
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

        $result = $this->queryOne("SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereSQL}", $params);
        return $result ? (int)$result['total'] : 0;
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

        return $this->query($query, ['category_id' => $categoryId, 'limit' => $limit]);
    }
}
