<?php

/**
 * Base Model
 * Model cơ sở với các query helpers để tái sử dụng
 */

require_once __DIR__ . '/../../config/database.php';

abstract class BaseModel
{
    protected $conn;
    protected $table;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Find by ID
     * @param int $id
     * @param string $columns
     * @return array|null
     */
    public function findById($id, $columns = '*')
    {
        return $this->findOne(['id' => $id], $columns);
    }

    /**
     * Find one record by conditions
     * @param array $conditions ['column' => 'value']
     * @param string $columns
     * @return array|null
     */
    public function findOne($conditions, $columns = '*')
    {
        $whereClause = $this->buildWhereClause($conditions);
        $query = "SELECT {$columns} FROM {$this->table} WHERE {$whereClause} LIMIT 1";

        $stmt = $this->conn->prepare($query);

        // Bind WITHOUT prefix for findOne/findAll
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    /**
     * Find all records by conditions
     * @param array $conditions
     * @param string $columns
     * @param string $orderBy
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findAll($conditions = [], $columns = '*', $orderBy = 'id DESC', $limit = null, $offset = 0)
    {
        $whereClause = empty($conditions) ? '1=1' : $this->buildWhereClause($conditions);
        $query = "SELECT {$columns} FROM {$this->table} WHERE {$whereClause} ORDER BY {$orderBy}";

        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);

        // Bind conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Insert record
     * @param array $data ['column' => 'value']
     * @return int|false Last insert ID or false
     */
    public function insert($data)
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                  VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Update record
     * @param int $id
     * @param array $data ['column' => 'value']
     * @return bool
     */
    public function updateById($id, $data)
    {
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = :{$column}";
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind data params
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        // Bind id
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Update records by conditions
     * @param array $conditions
     * @param array $data
     * @return bool
     */
    public function update($conditions, $data)
    {
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = :set_{$column}";
        }

        $whereClause = $this->buildWhereClauseWithPrefix($conditions, 'where_');
        $query = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$whereClause}";

        $stmt = $this->conn->prepare($query);

        // Bind data params with 'set_' prefix
        foreach ($data as $column => $value) {
            $stmt->bindValue(':set_' . $column, $value);
        }

        // Bind condition params with 'where_' prefix
        foreach ($conditions as $column => $value) {
            $stmt->bindValue(':where_' . $column, $value);
        }

        return $stmt->execute();
    }

    /**
     * Delete by ID
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete by conditions
     * @param array $conditions
     * @return bool
     */
    public function delete($conditions)
    {
        $whereClause = $this->buildWhereClause($conditions);
        $query = "DELETE FROM {$this->table} WHERE {$whereClause}";

        $stmt = $this->conn->prepare($query);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        return $stmt->execute();
    }

    /**
     * Count records
     * @param array $conditions
     * @return int
     */
    public function count($conditions = [])
    {
        $whereClause = empty($conditions) ? '1=1' : $this->buildWhereClause($conditions);
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";

        $stmt = $this->conn->prepare($query);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();

        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    /**
     * Check if record exists
     * @param array $conditions
     * @return bool
     */
    public function exists($conditions)
    {
        return $this->count($conditions) > 0;
    }

    /**
     * Paginate records
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @param string $columns
     * @param string $orderBy
     * @return array
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [], $columns = '*', $orderBy = 'id DESC')
    {
        $offset = ($page - 1) * $perPage;
        $items = $this->findAll($conditions, $columns, $orderBy, $perPage, $offset);
        $total = $this->count($conditions);

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
     * Execute raw query
     * @param string $query
     * @param array $params
     * @return array
     */
    protected function query($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Execute raw query and get single result
     * @param string $query
     * @param array $params
     * @return array|null
     */
    protected function queryOne($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Execute raw query (INSERT/UPDATE/DELETE)
     * @param string $query
     * @param array $params
     * @return bool
     */
    protected function execute($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        return $stmt->execute();
    }

    /**
     * Bind params helper
     */
    protected function bindParams($stmt, $params = [])
    {
        foreach ($params as $key => $value) {
            // positional params (0-based keys) become 1-based indexes for PDO
            if (is_int($key)) {
                $param = $key + 1;
            } else {
                $param = (strpos($key, ':') === 0) ? $key : ':' . $key;
            }

            if (is_int($value)) {
                $stmt->bindValue($param, $value, PDO::PARAM_INT);
            } elseif (is_bool($value)) {
                $stmt->bindValue($param, $value, PDO::PARAM_BOOL);
            } elseif (is_null($value)) {
                $stmt->bindValue($param, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
        }
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        $this->conn->rollBack();
    }

    /**
     * Build WHERE clause from conditions (without prefix)
     * @param array $conditions
     * @return string
     */
    private function buildWhereClause($conditions)
    {
        $clauses = [];
        foreach (array_keys($conditions) as $column) {
            $clauses[] = "{$column} = :{$column}";
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Build WHERE clause with prefix
     * @param array $conditions
     * @param string $prefix
     * @return string
     */
    private function buildWhereClauseWithPrefix($conditions, $prefix = '')
    {
        $clauses = [];
        foreach (array_keys($conditions) as $column) {
            $clauses[] = "{$column} = :{$prefix}{$column}";
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Get last insert ID
     * @return int
     */
    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    /**
     * Get table name
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}
