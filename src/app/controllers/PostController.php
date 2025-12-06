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

        $this->viewWithLayout('admin/posts/post_add', [
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'pageTitle' => 'Tạo bài viết mới',
            'csrfToken' => Security::generateCSRFToken(),
            "needPostEdit" => true

        ], "layouts/admin_layout");
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
            $this->redirect('/admin/posts/create');
            return;
        }

        // Rate limiting
        if (!Security::rateLimit('post_create', 5, 300)) {
            Toast::warning('Bạn đang tạo bài quá nhanh. Vui lòng chờ 5 phút');
            $this->redirect('/admin/posts/create');
            return;
        }

        $postModel = new PostModel();

        // ✅ XỬ LÝ UPLOAD ẢNH ĐÚNG
        $coverImage = null;

        // Kiểm tra có file upload không
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Cover image file detected");
            error_log("File error code: " . $_FILES['cover_image']['error']);

            if ($_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadFile('cover_image', 'uploads/posts/');

                if ($uploadResult['success']) {
                    $coverImage = $uploadResult['path'];
                    error_log("✅ Image uploaded: " . $coverImage);
                } else {
                    error_log("❌ Image upload failed: " . $uploadResult['message']);
                    // Không return, tiếp tục tạo post không có ảnh
                }
            } else {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá upload_max_filesize)',
                    UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá MAX_FILE_SIZE)',
                    UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần',
                    UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tmp',
                    UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
                    UPLOAD_ERR_EXTENSION => 'Upload bị chặn bởi extension'
                ];

                $errorMsg = $errorMessages[$_FILES['cover_image']['error']] ?? 'Lỗi upload không xác định';
                error_log("❌ Upload error: " . $errorMsg);
            }
        } else {
            error_log("No cover image file uploaded");
        }

        // ✅ XỬ LÝ TAGS: Chuyển từ chuỗi thành tag IDs
        $tagIds = [];
        $tagsInput = $this->input('tags', '');

        if (!empty($tagsInput) && is_string($tagsInput)) {
            $tagModel = new TagModel();

            // Tách chuỗi thành array tên tags
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagNames = array_filter($tagNames); // Loại bỏ empty

            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    // Tìm hoặc tạo mới tag
                    $tagId = $tagModel->findOrCreate($tagName);
                    if ($tagId) {
                        $tagIds[] = $tagId;
                    }
                }
            }

            error_log("Tags processed: " . print_r(['input' => $tagsInput, 'names' => $tagNames, 'ids' => $tagIds], true));
        }

        // Prepare data
        $data = [
            'user_id' => Session::getUserId(),
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ? Security::createSlug($this->input('slug')) : '',
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'),
            'cover_image' => $coverImage, // Có thể null
            'status' => $this->input('status', 'draft'),
            'tags' => $tagIds // Array of tag IDs
        ];


        // Validate
        if (empty($data['title'])) {
            Toast::error('Tiêu đề không được để trống');
            $this->redirect('/admin/posts/create');
            return;
        }

        if (empty($data['content']) || trim($data['content']) === '' || $data['content'] === '<p><br></p>') {
            Toast::error('Nội dung không được để trống');
            $this->redirect('/admin/posts/create');
            return;
        }

        // Create post
        $result = $postModel->create($data);

        error_log("Post create result: " . print_r($result, true));

        if ($result['success']) {
            Toast::success('Tạo bài viết thành công!');
            $this->redirect('/admin/posts');
        } else {
            Toast::error($result['message']);
            $this->redirect('/admin/posts/create');
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
        // DEBUG: Xem cấu trúc của tags
        error_log("Post tags structure: " . print_r($post['tags'], true));

        $selectedTags = [];
        if (isset($post['tags']) && is_array($post['tags'])) {
            $selectedTags = array_column($post['tags'], 'id');
        }

        error_log("Selected tag IDs: " . print_r($selectedTags, true));

        $this->viewWithLayout('admin/posts/post_edit', [
            'post' => $post,
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'selectedTags' => $selectedTags,
            'pageTitle' => 'Sửa bài viết: ' . $post['title'],
            'csrfToken' => Security::generateCSRFToken(),
            "needPostEdit" => true
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
            Toast::error('Bài viết không tồn tại');
            $this->redirect('/admin/posts');
            return;
        }

        if (!$this->canEdit($post)) {
            Toast::error('Bạn không có quyền sửa bài viết này');
            $this->redirect('/admin/posts');
            return;
        }

        if (!$this->validateCSRF()) {
            $this->redirect('/admin/posts/' . $id . '/edit');
            return;
        }

        // ✅ XỬ LÝ UPLOAD ẢNH
        $coverImage = $post['cover_image']; // Giữ ảnh cũ

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile('cover_image', 'uploads/posts/');

            if ($uploadResult['success']) {
                // Xóa ảnh cũ nếu có
                if (!empty($post['cover_image'])) {
                    $oldImagePath = __DIR__ . '/../../../public/' . $post['cover_image'];
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                        error_log("Deleted old image: " . $oldImagePath);
                    }
                }
                $coverImage = $uploadResult['path'];
            }
        }

        // ✅ XỬ LÝ TAGS: Chuyển từ chuỗi thành tag IDs
        $tagIds = [];
        $tagsInput = $this->input('tags', '');

        if (!empty($tagsInput) && is_string($tagsInput)) {
            $tagModel = new TagModel();

            // Tách chuỗi thành array tên tags
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagNames = array_filter($tagNames); // Loại bỏ empty

            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    // Tìm hoặc tạo mới tag
                    $tagId = $tagModel->findOrCreate($tagName);
                    if ($tagId) {
                        $tagIds[] = $tagId;
                    }
                }
            }

            error_log("Tags updated: " . print_r(['input' => $tagsInput, 'names' => $tagNames, 'ids' => $tagIds], true));
        }

        // Prepare data
        $data = [
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ? Security::createSlug($this->input('slug')) : '',
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'),
            'cover_image' => $coverImage,
            'status' => $this->input('status', 'draft'),
            'tags' => $tagIds // Array of tag IDs
        ];

        // Update
        $result = $postModel->update($id, $data);

        if ($result['success']) {
            Toast::success('Cập nhật bài viết thành công!');
            $this->redirect('/admin/posts');
        } else {
            Toast::error($result['message']);
            $this->redirect('/admin/posts/' . $id . '/edit');
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
            Toast::error('Invalid CSRF token');
            $this->redirect('/admin/posts');
            return;
        }

        $postModel = new PostModel();
        $post = $postModel->getById($id);

        if (!$post) {
            Toast::error('Bài viết không tồn tại');
            $this->redirect('/admin/posts');
            return;
        }

        // Kiểm tra quyền xóa
        if (!$this->canDelete($post)) {
            Toast::error('Bạn không có quyền xóa bài viết này');
            $this->redirect('/admin/posts');
            return;
        }

        // ✅ XÓA ẢNH NẾU CÓ
        if (!empty($post['cover_image'])) {
            $imagePath = __DIR__ . '/../../../public/' . $post['cover_image'];
            if (file_exists($imagePath)) {
                @unlink($imagePath);
                error_log("Deleted image: " . $imagePath);
            }
        }

        // ✅ XÓA POST
        if ($postModel->delete($id)) {
            Toast::success('Xóa bài viết thành công');
            $this->redirect('/admin/posts');
        } else {
            Toast::error('Không thể xóa bài viết');
            $this->redirect('/admin/posts');
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
