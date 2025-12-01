<?php

/**
 * Category Controller
 * Xử lý quản lý danh mục bài viết
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController extends BaseController
{
    private $categoryModel;
    private $postModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->postModel = new PostModel();
    }

    /**
     * Kiểm tra quyền admin cho các action cần thiết
     */
    private function checkAdminAccess()
    {
        $this->requireAdmin();
    }
    /**
     * Hiển thị danh sách categories cho người dùng
     */
    public function categoryList()
    {
        $categories = $this->categoryModel->getAll();


        //Get bài viết mới nhất cho mỗi category
        foreach ($categories as $category) {

            $category['recent_posts'] = $this->postModel->getByCategory($category['id'], 1, 3);
        }
        //Lây category phổ biến nhất theo bài viết
        $popularCategory = $this->categoryModel->getPopularCategories(6);
        // Render danh sách tất cả categories, không phải view category detail
        $this->viewWithLayout('users/category_list', [
            'categories' => $categories,
            "popularCategories" => $popularCategory,
            'pageTitle' => 'Danh mục bài viết - BlogIT'
        ], 'layouts/main');
    }


    //Hiển thị bài viết theo danh mục

    public function show($slug)
    {
        $category = $this->categoryModel->getBySlug($slug);
        if (!$category) {
            Toast::error('Danh mục không tồn tại');
            Router::redirect('/category');
            return;
        }

        //Phân trang
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;

        // Get post theo category

        $posts = $this->postModel->getByCategory($category["id"], $page, $perPage);


        //Tính tổng số bài viết
        $totalPosts = $this->postModel->countByCategory($category["id"]);
        $totalPages = ceil($totalPosts / $perPage);

        //Lấy danh mục liên quan

        $relatedCategories = $this->categoryModel->getRelatedCategories($category["id"], 4);
        // Lấy bài viết nổi bật
        $featuredPosts = $this->postModel->getFeaturedPosts($category["id"], 3);
        //Get avatar author cho mỗi bài viết

        //Đưa dữ liệu ra view
        $this->viewWithLayout("users/category_detail", [
            'category' => $category,
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts,
            'relatedCategories' => $relatedCategories,
            'featuredPosts' => $featuredPosts,
            'pageTitle' => $category['name'] . ' - BlogIT'
        ], "layouts/main");
    }

    /**
     * Hiển thị danh sách categories
     */
    public function categories()
    {
        $this->checkAdminAccess();
        $categories = $this->categoryModel->getAll();

        // Tính toán thống kê
        $stats = [
            'total' => count($categories),
            'active' => count(array_filter($categories, fn($c) => ($c['status'] ?? 'active') == 'active')),
            'parent' => count(array_filter($categories, fn($c) => empty($c['parent_id']))),
            'total_posts' => array_sum(array_column($categories, 'post_count'))
        ];

        $this->viewWithLayout('admin/manage_categories/category', [
            'categories' => $categories,
            'totalCategories' => $stats['total'],
            'activeCategories' => $stats['active'],
            'parentCategories' => $stats['parent'],
            'totalPosts' => $stats['total_posts'],
            'pageTitle' => 'Quản lý danh mục',
            "needCategory" => true,
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Tạo category mới
     */
    public function createCategory()
    {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/categories');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/categories');
            return;
        }

        // Validate dữ liệu
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            Toast::error('Tên danh mục không được để trống');
            Router::redirect('/admin/categories');
            return;
        }

        // Tạo slug
        $slug = $this->generateSlug($_POST['slug'] ?? $name);

        // Kiểm tra slug đã tồn tại
        if ($this->categoryModel->getBySlug($slug)) {
            Toast::error('Slug đã tồn tại, vui lòng chọn slug khác');
            Router::redirect('/admin/categories');
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => trim($_POST['description'] ?? ''),
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'icon' => trim($_POST['icon'] ?? 'fas fa-folder'),
            'color' => $_POST['color'] ?? '#4e73df',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? '')
        ];

        $result = $this->categoryModel->create($data);

        if ($result['success']) {
            Toast::success('Tạo danh mục thành công');
        } else {
            Toast::error('Có lỗi xảy ra khi tạo danh mục');
        }

        Router::redirect('/admin/categories');
    }

    /**
     * Cập nhật category
     */
    public function updateCategory($id)
    {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/categories');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/categories');
            return;
        }

        $category = $this->categoryModel->getById($id);
        if (!$category) {
            Toast::error('Không tìm thấy danh mục');
            Router::redirect('/admin/categories');
            return;
        }

        // Validate dữ liệu
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            Toast::error('Tên danh mục không được để trống');
            Router::redirect('/admin/categories');
            return;
        }

        // Tạo slug
        $slug = $this->generateSlug($_POST['slug'] ?? $name);

        // Kiểm tra slug đã tồn tại (trừ chính nó)
        $existingCategory = $this->categoryModel->getBySlug($slug);
        if ($existingCategory && $existingCategory['id'] != $id) {
            Toast::error('Slug đã tồn tại, vui lòng chọn slug khác');
            Router::redirect('/admin/categories');
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => trim($_POST['description'] ?? ''),
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'icon' => trim($_POST['icon'] ?? 'fas fa-folder'),
            'color' => $_POST['color'] ?? '#4e73df',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? '')
        ];

        $result = $this->categoryModel->update($id, $data);

        if ($result['success']) {
            Toast::success('Cập nhật danh mục thành công');
        } else {
            Toast::error('Có lỗi xảy ra khi cập nhật danh mục');
        }

        Router::redirect('/admin/categories');
    }

    /**
     * Xóa category
     */
    public function deleteCategory($id)
    {
        $this->checkAdminAccess();
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            Toast::error('Không tìm thấy danh mục');
            Router::redirect('/admin/categories');
            return;
        }

        // Kiểm tra có bài viết không
        if (($category['post_count'] ?? 0) > 0) {
            Toast::warning('Không thể xóa danh mục có bài viết. Vui lòng di chuyển hoặc xóa bài viết trước.');
            Router::redirect('/admin/categories');
            return;
        }

        if ($this->categoryModel->delete($id)) {
            Toast::success('Xóa danh mục thành công');
        } else {
            Toast::error('Có lỗi xảy ra khi xóa danh mục');
        }

        Router::redirect('/admin/categories');
    }

    /**
     * Generate slug từ tên
     */
    private function generateSlug($text)
    {
        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Vietnamese characters
        $vietnamese = [
            'à',
            'á',
            'ả',
            'ã',
            'ạ',
            'ă',
            'ằ',
            'ắ',
            'ẳ',
            'ẵ',
            'ặ',
            'â',
            'ầ',
            'ấ',
            'ẩ',
            'ẫ',
            'ậ',
            'è',
            'é',
            'ẻ',
            'ẽ',
            'ẹ',
            'ê',
            'ề',
            'ế',
            'ể',
            'ễ',
            'ệ',
            'ì',
            'í',
            'ỉ',
            'ĩ',
            'ị',
            'ò',
            'ó',
            'ỏ',
            'õ',
            'ọ',
            'ô',
            'ồ',
            'ố',
            'ổ',
            'ỗ',
            'ộ',
            'ơ',
            'ờ',
            'ớ',
            'ở',
            'ỡ',
            'ợ',
            'ù',
            'ú',
            'ủ',
            'ũ',
            'ụ',
            'ư',
            'ừ',
            'ứ',
            'ử',
            'ữ',
            'ự',
            'ỳ',
            'ý',
            'ỷ',
            'ỹ',
            'ỵ',
            'đ'
        ];

        $latin = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'y',
            'y',
            'y',
            'd'
        ];

        $text = str_replace($vietnamese, $latin, $text);

        // Remove special characters
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

        // Replace spaces and multiple hyphens with single hyphen
        $text = preg_replace('/[\s-]+/', '-', $text);

        // Trim hyphens from start and end
        return trim($text, '-');
    }
}
