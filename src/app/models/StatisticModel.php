<?php

require_once __DIR__ . '/BaseModel.php';

class StatisticsModel extends BaseModel
{
    protected $table = 'posts'; // Base table

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_posts' => $this->getTotalPosts(),
            'total_comments' => $this->getTotalComments(),
            'total_users' => $this->getTotalUsers(),
            'pending_comments' => $this->getPendingCommentsCount(),
            'draft_posts' => $this->getDraftPostsCount(),
            'published_posts' => $this->getPublishedPostsCount()
        ];

        return $stats;
    }

    /**
     * Get posts by month for chart
     */
    public function getPostsByMonth($months = 6)
    {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                         COUNT(*) as count
                  FROM posts
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                  GROUP BY month
                  ORDER BY month ASC";

        return $this->query($query, ['months' => $months]);
    }

    /**
     * Get total posts
     */
    private function getTotalPosts()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM posts");
        return $result['total'] ?? 0;
    }

    /**
     * Get total comments
     */
    private function getTotalComments()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM comments");
        return $result['total'] ?? 0;
    }

    /**
     * Get total users
     */
    private function getTotalUsers()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM users");
        return $result['total'] ?? 0;
    }

    /**
     * Get pending comments count
     */
    private function getPendingCommentsCount()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM comments WHERE is_approved = FALSE");
        return $result['total'] ?? 0;
    }

    /**
     * Get draft posts count
     */
    private function getDraftPostsCount()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM posts WHERE status = 'draft'");
        return $result['total'] ?? 0;
    }

    /**
     * Get published posts count
     */
    private function getPublishedPostsCount()
    {
        $result = $this->queryOne("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
        return $result['total'] ?? 0;
    }
}
