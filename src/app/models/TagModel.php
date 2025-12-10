<?php



require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../helpers/Security.php';

class TagModel extends BaseModel
{
    protected $table = 'tags';


    // Lấy tất cả tags với số lượng bài viết

    public function getAll()
    {
        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  LEFT JOIN post_tag pt ON t.id = pt.tag_id
                  LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  GROUP BY t.id
                  ORDER BY t.name ASC";

        return $this->query($query);
    }


    //Lấ//y tag theo slug

    public function getBySlug($slug)
    {
        return $this->findOne(['slug' => $slug]);
    }
    public function getById($id)
    {
        return $this->findOne(['id' => $id]);
    }

    /**
     * Tạo tag mới
     */
    public function create($data)
    {
        $id = $this->insert([
            'name' => $data['name'],
            'slug' => $data['slug']
        ]);

        if ($id) {
            return ['success' => true, 'id' => $id];
        }
        return ['success' => false];
    }

    /**
     * Cập nhật tag
     */
    public function update($id, $data)
    {
        try {
            // Validate
            if (empty($data['name'])) {
                return ['success' => false, 'message' => 'Tên tag không được trống'];
            }

            // Check nếu slug đã tồn tại
            if ($this->slugExists($data['slug'], $id)) {
                $data['slug'] = $data['slug'] . '-' . time();
            }

            $result = $this->updateById($id, [
                'name' => $data['name'],
                'slug' => $data['slug']
            ]);

            if ($result) {
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

    // Kiểm tra slug đã tồn tại (ngoại trừ ID cho trước)
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
     * Lấy tags phổ biến nhất
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

        return $this->query($query, ['limit' => $limit]);
    }
    /**
     * Tìm kiếm tags
     */
    public function search($keyword)
    {
        $searchTerm = '%' . $keyword . '%';

        $query = "SELECT t.*, COUNT(pt.post_id) as post_count
                  FROM {$this->table} t
                  LEFT JOIN post_tag pt ON t.id = pt.tag_id
                  LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                  WHERE t.name LIKE :search1 OR t.slug LIKE :search2
                  GROUP BY t.id
                  ORDER BY t.name ASC";

        return $this->query($query, ['search1' => $searchTerm, 'search2' => $searchTerm]);
    }

    /**
     * Lấy tags theo post ID
     */
    public function getByPostId($postId)
    {
        $query = "SELECT t.* FROM {$this->table} t
                  INNER JOIN post_tag pt ON t.id = pt.tag_id
                  WHERE pt.post_id = :post_id
                  ORDER BY t.name ASC";

        return $this->query($query, ['post_id' => $postId]);
    }

    /**
     * Lấy tag cloud (với size dựa trên số lượng posts)
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

        $tags = $this->query($query, ['limit' => $limit]);

        // Tính size dựa trên post_count
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
 
     */
    public function attachToPost($postId, $tagId)
    {
        $query = "INSERT IGNORE INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";
        return $this->execute($query, ['post_id' => $postId, 'tag_id' => $tagId]);
    }

    /**
     * Gỡ tag khỏi post
     */
    public function detachFromPost($postId, $tagId)
    {
        $query = "DELETE FROM post_tag WHERE post_id = :post_id AND tag_id = :tag_id";
        return $this->execute($query, ['post_id' => $postId, 'tag_id' => $tagId]);
    }


    //Đồng bộ tags cho post (xóa hết rồi add lại)

    public function syncToPost($postId, $tagIds)
    {
        try {
            // Bắt đầu transaction
            $this->beginTransaction();

            // Xóa tags hiện có
            $this->execute("DELETE FROM post_tag WHERE post_id = :post_id", ['post_id' => $postId]);

            // Thêm tags mới
            if (!empty($tagIds)) {
                $insertQuery = "INSERT INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";

                foreach ($tagIds as $tagId) {
                    $this->execute($insertQuery, ['post_id' => $postId, 'tag_id' => $tagId]);
                }
            }

            // Commit transaction
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback on error
            $this->rollback();
            error_log("Tag Sync Error: " . $e->getMessage());
            return false;
        }
    }


    //Tìm hoặc tạo tag mới từ tên


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


    //  Xóa tag


    public function delete($id)
    {
        try {
            // Begin transaction
            $this->beginTransaction();

            // Xóa relationships với posts trước
            $this->execute("DELETE FROM post_tag WHERE tag_id = :tag_id", ['tag_id' => $id]);

            // Xóa tag
            $result = $this->deleteById($id);

            // Commit transaction
            $this->commit();

            return $result;
        } catch (PDOException $e) {
            // Rollback on error
            $this->rollback();
            error_log("Tag Delete Error: " . $e->getMessage());
            return false;
        }
    }


    //  Xóa nhiều tags cùng lúc

    public function bulkDelete($ids)
    {
        try {
            $this->beginTransaction();

            $count = 0;
            foreach ($ids as $id) {
                // Xóa relationships
                $this->execute("DELETE FROM post_tag WHERE tag_id = :tag_id", ['tag_id' => $id]);

                // Xóa tag
                if ($this->deleteById($id)) {
                    $count++;
                }
            }

            $this->commit();

            return [
                'success' => true,
                'deleted_count' => $count,
                'message' => "Đã xóa {$count} tag"
            ];
        } catch (PDOException $e) {
            $this->rollback();
            error_log("Tag Bulk Delete Error: " . $e->getMessage());

            return [
                'success' => false,
                'deleted_count' => 0,
                'message' => 'Lỗi khi xóa tags'
            ];
        }
    }


    // Kiểm tra tag có đang được sử dụng không

    public function isInUse($id)
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM post_tag WHERE tag_id = :tag_id",
            ['tag_id' => $id]
        );

        return $result && $result['count'] > 0;
    }


    // Xóa tag an toàn (chỉ xóa nếu không còn sử dụng)

    public function safeDelete($id, $force = false)
    {
        if (!$force && $this->isInUse($id)) {
            $tag = $this->findById($id);
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


    //Lấy số lượng bài viết sử dụng tag


    public function getUsageCount($id)
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM post_tag WHERE tag_id = :tag_id",
            ['tag_id' => $id]
        );

        return $result ? (int)$result['count'] : 0;
    }
}
