<?php

/**
 * Post Controller
 * Xử lý CRUD bài viết
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/TagModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../helpers/ToastHelper.php';

class PostController extends BaseController
{

    public function posts()
    {
        $this->requireAdmin();

        $postModel = new PostModel();

        $page = $this->input('page', 1);
        $perPage = 20;

        // Lấy filter parameters
        $status = $this->input('status', '');
        $search = $this->input('search', '');

        // Get posts with details through Model
        $posts = $postModel->getAllWithDetails($page, $perPage, $status, $search);
        $totalPosts = $postModel->countAllWithDetails($status, $search);

        $pagination = $this->paginate($totalPosts, $page, $perPage);

        $this->viewWithLayout('admin/posts/posts', [
            'posts' => $posts,
            'pagination' => $pagination,
            'totalPosts' => $totalPosts,
            'currentStatus' => $status,
            'searchKeyword' => $search,
            'pageTitle' => 'Quản lý bài viết',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Hiển thị chi tiết bài viết
     * @param string $slug
     */
    public function show($slug)
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $post = $postModel->getBySlug($slug);

        if (!$post) {
            $this->redirect('/');
            return;
        }

        // Kiểm tra quyền xem bài draft
        if ($post['status'] === 'draft') {
            if (
                !Session::isLoggedIn() ||
                (Session::getUserId() != $post['user_id'] && !Session::isAdmin())
            ) {
                $this->redirect('/');
                return;
            }
        }

        // Lấy comments
        $comments = $commentModel->getByPost($post['id']);

        // Related posts
        $relatedPosts = [];
        if ($post['category_id']) {
            $relatedPosts = $postModel->getByCategory($post['category_id'], 1, 4);
            // Loại bỏ bài hiện tại
            $relatedPosts = array_filter($relatedPosts, function ($p) use ($post) {
                return $p['id'] != $post['id'];
            });
        }

        $this->viewWithLayout('post/view', [
            'post' => $post,
            'comments' => $comments,
            'relatedPosts' => $relatedPosts,
            'pageTitle' => $post['title']
        ]);
    }

    /**
     * Hiển thị form tạo bài viết
     */
    public function showCreate()
    {
        $this->requireAuth();

        $categoryModel = new CategoryModel();
        $tagModel = new TagModel();

        $this->viewWithLayout('post/create', [
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'pageTitle' => 'Tạo bài viết mới',
            'csrfToken' => Security::generateCSRFToken()
        ]);
    }

    /**
     * Xử lý tạo bài viết
     */
    public function create()
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Toast::error('Yêu cầu không hợp lệ');
            $this->redirect('/post/create');
            return;
        }

        // Rate limiting
        if (!Security::rateLimit('post_create', 5, 300)) {
            Toast::warning('Bạn đang tạo bài quá nhanh. Vui lòng chờ 5 phút');
            $this->redirect('/post/create');
            return;
        }

        $postModel = new PostModel();

        // Prepare data
        $data = [
            'user_id' => Session::getUserId(),
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ? Security::createSlug($this->input('slug')) : '',
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'), // Rich text - không sanitize
            'cover_image' => $this->input('cover_image'),
            'status' => $this->input('status', 'draft'),
            'tags' => $this->input('tags', [])
        ];

        // Validate
        if (empty($data['title'])) {
            Toast::error('Tiêu đề không được để trống');
            $this->redirect('/post/create');
            return;
        }

        if (empty($data['content'])) {
            Toast::error('Nội dung không được để trống');
            $this->redirect('/post/create');
            return;
        }

        // Create post
        $result = $postModel->create($data);

        if ($result['success']) {
            Toast::success('Tạo bài viết thành công!');
            $this->redirect('/admin/posts');
        } else {
            Toast::error($result['message']);
            $this->redirect('/post/create');
        }
    }

    /**
     * Hiển thị form sửa bài viết
     * @param int $id
     */
    public function showEdit($id)
    {
        $this->requireAuth();

        $postModel = new PostModel();
        $post = $postModel->getById($id);

        if (!$post) {
            Session::flash('error', 'Bài viết không tồn tại');
            $this->redirect('/');
            return;
        }

        // Kiểm tra quyền sửa
        if (!$this->canEdit($post)) {
            Session::flash('error', 'Bạn không có quyền sửa bài viết này');
            $this->redirect('/');
            return;
        }

        $categoryModel = new CategoryModel();
        $tagModel = new TagModel();

        // Get selected tag IDs
        $selectedTags = array_column($post['tags'], 'id');

        $this->viewWithLayout('admin/posts/post_edit', [
            'post' => $post,
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'selectedTags' => $selectedTags,
            'pageTitle' => 'Sửa bài viết: ' . $post['title'],
            'csrfToken' => Security::generateCSRFToken()
        ], "layouts/admin_layout");
    }

    /**
     * Xử lý cập nhật bài viết
     * @param int $id
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        $postModel = new PostModel();
        $post = $postModel->getById($id);

        if (!$post) {
            Session::flash('error', 'Bài viết không tồn tại');
            $this->redirect('/');
            return;
        }

        // Kiểm tra quyền
        if (!$this->canEdit($post)) {
            Session::flash('error', 'Bạn không có quyền sửa bài viết này');
            $this->redirect('/');
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            $this->redirect('/post/' . $id . '/edit');
            return;
        }

        // Prepare data
        $data = [
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ? Security::createSlug($this->input('slug')) : '',
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'),
            'cover_image' => $this->input('cover_image', $post['cover_image']),
            'status' => $this->input('status', 'draft'),
            'tags' => $this->input('tags', [])
        ];

        // Update
        $result = $postModel->update($id, $data);

        if ($result['success']) {
            Toast::success('Cập nhật bài viết thành công!');
            $this->redirect('/admin/posts');
        } else {
            Toast::error($result['message']);
            $this->redirect('/posts/' . $id . '/edit');
        }
    }

    /**
     * Xóa bài viết
     * @param int $id
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        // Validate CSRF
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $postModel = new PostModel();
        $post = $postModel->getById($id);

        if (!$post) {
            $this->json(['success' => false, 'message' => 'Bài viết không tồn tại'], 404);
            return;
        }

        // Kiểm tra quyền xóa
        if (!$this->canDelete($post)) {
            $this->json(['success' => false, 'message' => 'Bạn không có quyền xóa bài viết này'], 403);
            return;
        }

        if ($postModel->delete($id)) {
            Session::flash('success', 'Xóa bài viết thành công');
            $this->json(['success' => true, 'redirect' => '/']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể xóa bài viết'], 500);
        }
    }

    /**
     * Upload hình ảnh
     */
    public function uploadImage()
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        if (!isset($_FILES['image'])) {
            $this->json(['success' => false, 'message' => 'Không có file'], 400);
            return;
        }

        // Validate file
        $validation = Security::validateFileUpload($_FILES['image']);
        if (!$validation['success']) {
            $this->json($validation, 400);
            return;
        }

        // Upload
        $result = $this->uploadFile('image', 'uploads/posts/');

        if ($result['success']) {
            // Save to database (optional)
            // ... code to save upload record

            $this->json([
                'success' => true,
                'url' => $result['url'],
                'path' => $result['path']
            ]);
        } else {
            $this->json($result, 500);
        }
    }

    /**
     * Kiểm tra quyền sửa bài viết
     * @param array $post
     * @return bool
     */
    private function canEdit($post)
    {
        // Admin có thể sửa tất cả
        if (Session::isAdmin()) {
            return true;
        }

        // Author có thể sửa bài của mình
        return Session::getUserId() == $post['user_id'];
    }

    /**
     * Kiểm tra quyền xóa bài viết
     * @param array $post
     * @return bool
     */
    private function canDelete($post)
    {
        // Admin có thể xóa tất cả
        if (Session::isAdmin()) {
            return true;
        }

        // Author có thể xóa bài của mình
        return Session::getUserId() == $post['user_id'];
    }
}
