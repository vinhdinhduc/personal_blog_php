<?php

/**
 * Category Model
 * Xử lý danh mục bài viết
 */

require_once __DIR__ . '/../../config/database.php';

class CategoryModel
{
    public $conn;
    private $table = 'categories';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Lấy tất cả categories
     * @return array
     */
    public function getAll()
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM {$this->table} c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  GROUP BY c.id
                  ORDER BY c.name ASC";

        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Lấy category theo ID
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
     * Lấy category theo slug
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
     * Tạo category mới
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (name, slug, description) 
                  VALUES (:name, :slug, :description)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        }
        return ['success' => false];
    }

    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} 
                  SET name = :name, slug = :slug, description = :description 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
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
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    //Lấy danh mục phổ biến    private function getPopularCategories($limit = 6)
    public function getPopularCategories($limit = 6)
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM categories c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  GROUP BY c.id
                  HAVING post_count > 0
                  ORDER BY post_count DESC, c.name ASC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * Lấy danh mục liên quan
     */
    public function getRelatedCategories($currentCategoryId, $limit = 4)
    {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM categories c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  WHERE c.id != :current_id
                  GROUP BY c.id
                  HAVING post_count > 0
                  ORDER BY RAND()
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':current_id', $currentCategoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
