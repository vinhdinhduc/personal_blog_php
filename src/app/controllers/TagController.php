<?php

/**
 * Tag Controller
 * Quản lý tags trong admin panel
 */

require_once __DIR__ . '/../models/TagModel.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/Security.php';

class TagController extends BaseController
{
    private $tagModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
    }

    /**
     * Hiển thị danh sách tags
     */
    public function index()
    {
        $this->requireAdmin();

        // Lấy tham số tìm kiếm
        $search = $this->input('search', '');
        $page = max(1, (int)$this->input('page', 1));
        $perPage = 20;

        // Lấy danh sách tags
        if (!empty($search)) {
            $tags = $this->tagModel->search($search);
            $total = count($tags);
            $pagination = $this->paginate($total, $page, $perPage);
            $tags = array_slice($tags, $pagination['offset'], $perPage);
        } else {
            $tags = $this->tagModel->getAll();
            $total = count($tags);
            $pagination = $this->paginate($total, $page, $perPage);
            $tags = array_slice($tags, $pagination['offset'], $perPage);
        }

        // Thống kê
        $stats = [
            'total' => $this->tagModel->count(),
            'used' => count(array_filter($tags, fn($t) => $t['post_count'] > 0)),
            'unused' => count(array_filter($tags, fn($t) => $t['post_count'] == 0))
        ];

        $this->viewWithLayout('admin/manage_tags/tags', [
            'pageTitle' => 'Quản lý Tags',
            'tags' => $tags,
            'stats' => $stats,
            'search' => $search,
            'pagination' => $pagination,
            'needTags' => true
        ], 'layouts/admin_layout');
    }

    /**
     * Hiển thị form tạo tag mới
     */
    public function showCreate()
    {
        $this->requireAdmin();

        $this->viewWithLayout('admin/manage_tags/create_tag', [
            'pageTitle' => 'Thêm Tag Mới',
            'needTags' => true
        ], 'layouts/admin_layout');
    }

    /**
     * Xử lý tạo tag mới
     */
    public function create()
    {
        $this->requireAdmin();
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->redirect('/admin/tags');
            return;
        }

        $name = trim($this->input('name'));
        $slug = trim($this->input('slug'));

        // Validation
        if (empty($name)) {
            Session::flash('error', 'Tên tag không được trống');
            $this->redirect('/admin/tags/create');
            return;
        }

        // Tạo slug tự động nếu không có
        if (empty($slug)) {
            $slug = Security::createSlug($name);
        } else {
            $slug = Security::createSlug($slug);
        }

        // Kiểm tra slug trùng
        if ($this->tagModel->slugExists($slug)) {
            $slug = $slug . '-' . time();
        }

        $result = $this->tagModel->create([
            'name' => $name,
            'slug' => $slug
        ]);

        if ($result['success']) {
            Session::flash('success', 'Thêm tag thành công');
            $this->redirect('/admin/tags');
        } else {
            Session::flash('error', 'Không thể thêm tag');
            $this->redirect('/admin/tags/create');
        }
    }

    /**
     * Hiển thị form chỉnh sửa tag
     */
    public function showEdit($id)
    {
        $this->requireAdmin();

        $tag = $this->tagModel->getById($id);

        if (!$tag) {
            Session::flash('error', 'Không tìm thấy tag');
            $this->redirect('/admin/tags');
            return;
        }

        $usageCount = $this->tagModel->getUsageCount($id);

        $this->viewWithLayout('admin/manage_tags/edit_tag', [
            'pageTitle' => 'Chỉnh Sửa Tag',
            'tag' => $tag,
            'usageCount' => $usageCount,
            'needTags' => true
        ], 'layouts/admin_layout');
    }

    /**
     * Xử lý cập nhật tag
     */
    public function update($id)
    {
        $this->requireAdmin();
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->redirect('/admin/tags');
            return;
        }

        $tag = $this->tagModel->getById($id);
        if (!$tag) {
            Session::flash('error', 'Không tìm thấy tag');
            $this->redirect('/admin/tags');
            return;
        }

        $name = trim($this->input('name'));
        $slug = trim($this->input('slug'));

        // Validation
        if (empty($name)) {
            Session::flash('error', 'Tên tag không được trống');
            $this->redirect('/admin/tags/edit/' . $id);
            return;
        }

        // Tạo slug tự động nếu không có
        if (empty($slug)) {
            $slug = Security::createSlug($name);
        } else {
            $slug = Security::createSlug($slug);
        }

        $result = $this->tagModel->update($id, [
            'name' => $name,
            'slug' => $slug
        ]);

        if ($result['success']) {
            Session::flash('success', 'Cập nhật tag thành công');
            $this->redirect('/admin/tags');
        } else {
            Session::flash('error', $result['message'] ?? 'Không thể cập nhật tag');
            $this->redirect('/admin/tags/edit/' . $id);
        }
    }

    /**
     * Xóa tag
     */
    public function delete($id)
    {
        $this->requireAdmin();
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->redirect('/admin/tags');
            return;
        }

        $tag = $this->tagModel->getById($id);
        if (!$tag) {
            Session::flash('error', 'Không tìm thấy tag');
            $this->redirect('/admin/tags');
            return;
        }

        $force = $this->input('force') === '1';
        $result = $this->tagModel->safeDelete($id, $force);

        if ($result['success']) {
            Session::flash('success', 'Xóa tag thành công');
        } else {
            if ($result['in_use'] && !$force) {
                Session::flash('warning', $result['message']);
            } else {
                Session::flash('error', $result['message']);
            }
        }

        $this->redirect('/admin/tags');
    }

    /**
     * Xóa nhiều tags
     */
    public function bulkDelete()
    {
        $this->requireAdmin();
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'CSRF token không hợp lệ');
            $this->redirect('/admin/tags');
            return;
        }

        // Lấy từ tag_ids[] thay vì ids[]
        $ids = $this->input('tag_ids', []);

        if (empty($ids) || !is_array($ids)) {
            Session::flash('error', 'Vui lòng chọn ít nhất một tag để xóa');
            $this->redirect('/admin/tags');
            return;
        }

        $force = $this->input('force') === '1';

        $successCount = 0;
        $errorCount = 0;

        foreach ($ids as $id) {
            $result = $this->tagModel->safeDelete($id, $force);
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        if ($successCount > 0) {
            Session::flash('success', "Đã xóa thành công {$successCount} tag(s)");
        }

        if ($errorCount > 0) {
            Session::flash('warning', "Không thể xóa {$errorCount} tag(s)");
        }

        $this->redirect('/admin/tags');
    }

    /**
     * Xem chi tiết tag
     */
    public function detail($id)
    {
        $this->requireAdmin();

        $tag = $this->tagModel->getById($id);

        if (!$tag) {
            Session::flash('error', 'Không tìm thấy tag');
            $this->redirect('/admin/tags');
            return;
        }

        $usageCount = $this->tagModel->getUsageCount($id);

        $this->viewWithLayout('admin/manage_tags/view_tag', [
            'pageTitle' => 'Chi Tiết Tag - ' . $tag['name'],
            'tag' => $tag,
            'usageCount' => $usageCount,
            'needTags' => true
        ], 'layouts/admin_layout');
    }

    /**
     * API: Tìm kiếm tags (cho autocomplete)
     */
    public function search()
    {
        $this->requireAdmin();

        $keyword = $this->input('q', '');

        if (empty($keyword)) {
            $this->json(['tags' => []]);
            return;
        }

        $tags = $this->tagModel->search($keyword);
        $this->json(['tags' => $tags]);
    }
}
