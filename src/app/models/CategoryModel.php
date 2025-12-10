<?php

//Quản lý danh mục bài viét

require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel
{
    protected $table = 'categories';

    /**
     * Lấy tất cả categories với số lượng bài viết
     * @return array
     */
    public function getAll()
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM {$this->table} c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  GROUP BY c.id
                  ORDER BY c.name ASC";

        return $this->query($query);
    }

    /**
     * Lấy category theo slug
     * @param string $slug
     * @return array|null
     */
    public function getBySlug($slug)
    {
        return $this->findOne(['slug' => $slug]);
    }
    public function getById($id)
    {
        return $this->findOne(['id' => $id]);
    }

    /**
     * Tạo category mới
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        $id = $this->insert([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null
        ]);

        if ($id) {
            return ['success' => true, 'id' => $id];
        }
        return ['success' => false];
    }

    /**
     * Cập nhật category
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        $result = $this->updateById($id, [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null
        ]);

        if ($result) {
            return ['success' => true];
        }
        return ['success' => false];
    }

    /**
     * Xóa category
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->deleteById($id);
    }

    /**
     * Lấy danh mục phổ biến
     * @param int $limit
     * @return array
     */
    public function getPopularCategories($limit = 6)
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM {$this->table} c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  GROUP BY c.id
                  HAVING post_count > 0
                  ORDER BY post_count DESC, c.name ASC
                  LIMIT :limit";

        return $this->query($query, ['limit' => $limit]);
    }

    /**
     * Lấy danh mục liên quan
     * @param int $currentCategoryId
     * @param int $limit
     * @return array
     */
    public function getRelatedCategories($currentCategoryId, $limit = 4)
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM {$this->table} c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  WHERE c.id != :current_id
                  GROUP BY c.id
                  HAVING post_count > 0
                  ORDER BY RAND()
                  LIMIT :limit";

        return $this->query($query, [
            'current_id' => $currentCategoryId,
            'limit' => $limit
        ]);
    }

    /**
     * Kiểm tra slug đã tồn tại chưa
     * @param string $slug
     * @param int|null $excludeId ID cần loại trừ (khi update)
     * @return bool
     */
    public function slugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $query = "SELECT COUNT(*) as count FROM {$this->table} 
                      WHERE slug = :slug AND id != :id";
            $result = $this->queryOne($query, ['slug' => $slug, 'id' => $excludeId]);
        } else {
            $result = $this->queryOne("SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug", ['slug' => $slug]);
        }

        return $result && $result['count'] > 0;
    }

    /**
     * Lấy categories có phân trang
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated($page = 1, $perPage = 10)
    {
        return $this->paginate($page, $perPage, [], '*', 'name ASC');
    }
}
