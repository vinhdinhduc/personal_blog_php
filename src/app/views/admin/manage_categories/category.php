<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý danh mục</h1>
    <div class="content__breadcrumb">
        <a href="<?php echo Router::url('/admin'); ?>" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Danh mục</span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="card-grid">
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Tổng danh mục</h3>
                <div class="card__value"><?= $totalCategories ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--primary">
                <i class="fas fa-folder"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Tổng bài viết</h3>
                <div class="card__value"><?= $totalPosts ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--warning">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="category-layout">
    <!-- Categories Table -->
    <div>
        <div class="category-table">
            <div class="category-table__header">
                <h2 class="category-table__title">Danh sách danh mục</h2>
                <div class="category-table__actions">
                    <button class="btn btn--primary" onclick="openAddCategoryModal()">
                        <i class="fas fa-plus"></i> Thêm danh mục mới
                    </button>
                    <div class="category-table__search">
                        <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 250px;" onkeyup="searchCategories(this.value)">
                        <button class="btn btn--info btn--sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <table class="table" id="categoriesTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th style="width: 60px;">ID</th>
                        <th>Tên danh mục</th>
                        <th style="width: 200px;">Slug</th>
                        <th style="width: 100px;">Bài viết</th>
                        <th style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($categories) && count($categories) > 0): ?>
                        <?php foreach ($categories as $category): ?>
                            <tr data-category-id="<?= $category['id'] ?>">
                                <td>
                                    <input type="checkbox" class="category-checkbox" value="<?= $category['id'] ?>">
                                </td>
                                <td>
                                    <strong style="color: var(--primary-color);"><?= $category['id'] ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <span style="font-weight: 600; color: #5a5c69;"><?= htmlspecialchars($category['name']) ?></span>
                                        <?php if (!empty($category['description'])): ?>
                                            <small style="display: block; color: #858796; margin-top: 5px;">
                                                <?= htmlspecialchars(mb_substr($category['description'], 0, 80)) ?><?= mb_strlen($category['description']) > 80 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="code-display"><?= htmlspecialchars($category['slug']) ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?php echo Router::url('/admin/posts?category=' . $category['id']); ?>" class="category-stats">
                                        <i class="fas fa-newspaper"></i>
                                        <?= $category['post_count'] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)" class="btn btn--info btn--sm" data-tooltip="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?php echo Router::url('/category/' . $category['slug']); ?>" target="_blank" class="btn btn--success btn--sm" data-tooltip="Xem trang">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="<?php echo Router::url('/admin/categories/delete/' . $category['id']); ?>" style="display: inline;" onsubmit="return confirmDelete(<?= $category['post_count'] ?? 0 ?>)">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <button type="submit" class="btn btn--danger btn--sm" data-tooltip="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open empty-state__icon"></i>
                                    <p class="empty-state__title">Chưa có danh mục nào</p>
                                    <p class="empty-state__text">Hãy tạo danh mục đầu tiên cho blog của bạn</p>
                                    <button class="btn btn--primary" onclick="document.getElementById('category_name').focus(); openAddCategoryModal();">
                                        <i class="fas fa-plus"></i>
                                        Tạo danh mục đầu tiên
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Quick Actions -->
            <div class="quick-actions" style="margin-top: 20px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                <h4 class="quick-actions__title" style="margin-bottom: 15px;"><i class="fas fa-bolt"></i> Thao tác nhanh</h4>
                <div class="quick-actions__list" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button class="btn btn--danger btn--sm" onclick="bulkDelete()">
                        <i class="fas fa-trash"></i> Xóa đã chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal" id="categoryModal" style="display: none;">
    <div class="modal__overlay" onclick="closeCategoryModal()"></div>
    <div class="modal__content" style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
        <div class="modal__header">
            <h3 class="modal__title">
                <i class="fas fa-plus-circle"></i>
                <span id="formTitle">Thêm danh mục mới</span>
            </h3>
            <button class="modal__close" onclick="closeCategoryModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal__body">
            <form id="categoryForm" method="POST" action="<?php echo Router::url('/admin/categories/create'); ?>" data-validate>
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" id="category_id">

                <!-- Name -->
                <div class="form-group">
                    <label class="form-label">Tên danh mục <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" name="name" id="category_name" class="form-control" placeholder="Ví dụ: Công nghệ, Tin tức..." required onkeyup="generateCategorySlug()">
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label class="form-label">URL thân thiện (Slug)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="slug" id="category_slug" class="form-control" placeholder="url-than-thien" style="flex: 1;">
                        <button type="button" class="btn btn--info" onclick="generateCategorySlug()">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" id="category_description" class="form-control" rows="4" placeholder="Mô tả ngắn về danh mục..."></textarea>
                </div>

                <!-- Form Buttons -->
                <div class="form-buttons">
                    <button type="button" class="btn btn--secondary" onclick="closeCategoryModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="button" class="btn btn--secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn--primary">
                        <i class="fas fa-save"></i> <span id="submitBtnText">Thêm danh mục</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    .modal__overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(3px);
    }

    .modal__content {
        position: relative;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 90%;
        animation: slideUp 0.3s ease;
    }

    .modal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 25px;
        border-bottom: 1px solid #e3e6f0;
    }

    .modal__title {
        margin: 0;
        font-size: 20px;
        color: #5a5c69;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal__close {
        background: none;
        border: none;
        font-size: 24px;
        color: #858796;
        cursor: pointer;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .modal__close:hover {
        background: #f8f9fc;
        color: #5a5c69;
    }

    .modal__body {
        padding: 25px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .category-layout {
        display: block;
    }
</style>

<script>
    function openAddCategoryModal() {
        resetForm();
        document.getElementById('categoryModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            document.getElementById('category_name').focus();
        }, 300);
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        resetForm();
    }

    // Update editCategory function to open modal
    function editCategory(category) {
        document.getElementById('formTitle').textContent = 'Chỉnh sửa danh mục';
        document.getElementById('submitBtnText').textContent = 'Cập nhật';
        document.getElementById('categoryForm').action = '<?php echo Router::url('/admin/categories/update'); ?>';

        document.getElementById('category_id').value = category.id;
        document.getElementById('category_name').value = category.name;
        document.getElementById('category_slug').value = category.slug;
        document.getElementById('category_description').value = category.description || '';

        openAddCategoryModal();
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCategoryModal();
        }
    });
</script>

<script src="<?php echo Router::url('/public/js/admin-category.js'); ?>"></script>