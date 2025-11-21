<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý danh mục</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Danh mục</span>
    </div>
</div>

<!-- Statistics -->
<div class="card-grid" style="margin-bottom: 30px;">
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
                <h3 class="card__title">Đang hoạt động</h3>
                <div class="card__value"><?= $activeCategories ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Danh mục cha</h3>
                <div class="card__value"><?= $parentCategories ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--info">
                <i class="fas fa-sitemap"></i>
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

<?php if (isset($message)): ?>
    <div class="alert alert--<?= $message['type'] ?>" style="padding: 15px; border-radius: 8px; margin-bottom: 20px; background: <?= $message['type'] == 'success' ? 'rgba(28,200,138,0.1)' : 'rgba(231,74,59,0.1)' ?>; border-left: 4px solid <?= $message['type'] == 'success' ? 'var(--success-color)' : 'var(--danger-color)' ?>;">
        <i class="fas fa-<?= $message['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= $message['text'] ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 400px; gap: 20px;">
    <!-- Categories List -->
    <div class="table-container">
        <div class="table-container__header">
            <h2 class="table-container__title">Danh sách danh mục</h2>

            <!-- Search & Filter -->
            <div style="display: flex; gap: 10px;">
                <select class="form-control" style="width: 150px;" onchange="filterCategories(this.value)">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Không hoạt động</option>
                </select>

                <input type="text"
                    class="form-control"
                    placeholder="Tìm kiếm danh mục..."
                    style="width: 250px;"
                    onkeyup="searchCategories(this.value)">

                <button class="btn btn--info btn--sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <table class="table" id="categoriesTable">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Icon</th>
                    <th>Tên danh mục</th>
                    <th style="width: 150px;">Slug</th>
                    <th style="width: 150px;">Danh mục cha</th>
                    <th style="width: 100px;">Bài viết</th>
                    <th style="width: 100px;">Thứ tự</th>
                    <th style="width: 120px;">Trạng thái</th>
                    <th style="width: 180px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($categories) && count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr data-category-id="<?= $category['id'] ?>"
                            data-status="<?= $category['status'] ?>"
                            style="<?= !empty($category['parent_id']) ? 'background: rgba(0,0,0,0.02);' : '' ?>">
                            <td>
                                <input type="checkbox" class="category-checkbox" value="<?= $category['id'] ?>">
                            </td>
                            <td>
                                <strong style="color: var(--primary-color);"><?= $category['id'] ?></strong>
                            </td>
                            <td>
                                <div style="width: 50px; height: 50px; background: <?= $category['color'] ?? 'var(--primary-color)' ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px;">
                                    <?php if (!empty($category['icon'])): ?>
                                        <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($category['parent_id'])): ?>
                                    <span style="color: var(--secondary-color); margin-right: 5px;">
                                        └─
                                    </span>
                                <?php endif; ?>
                                <strong><?= htmlspecialchars($category['name']) ?></strong>
                                <br>
                                <?php if (!empty($category['description'])): ?>
                                    <small style="color: var(--secondary-color);">
                                        <?= htmlspecialchars(mb_substr($category['description'], 0, 60)) ?>...
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="background: var(--light-color); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= htmlspecialchars($category['slug']) ?>
                                </code>
                            </td>
                            <td>
                                <?php if (!empty($category['parent_name'])): ?>
                                    <span class="badge badge--info">
                                        <i class="fas fa-folder-open"></i>
                                        <?= htmlspecialchars($category['parent_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--secondary-color); font-style: italic;">
                                        <i class="fas fa-minus"></i> Gốc
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="/admin/posts?category=<?= $category['id'] ?>"
                                    style="background: var(--light-color); padding: 5px 12px; border-radius: 15px; font-weight: 600; text-decoration: none; color: var(--dark-color); display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-newspaper" style="font-size: 12px;"></i>
                                    <?= $category['post_count'] ?? 0 ?>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <span style="background: var(--light-color); padding: 5px 12px; border-radius: 5px; font-weight: 600;">
                                    <?= $category['sort_order'] ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($category['status'] == 'active'): ?>
                                    <span class="badge badge--success">
                                        <i class="fas fa-check-circle"></i>
                                        Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge--warning">
                                        <i class="fas fa-pause-circle"></i>
                                        Tạm dừng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="editCategory(<?= $category['id'] ?>)"
                                    class="btn btn--info btn--sm"
                                    data-tooltip="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="/category/<?= $category['slug'] ?>"
                                    target="_blank"
                                    class="btn btn--success btn--sm"
                                    data-tooltip="Xem trang">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="/admin/categories/delete/<?= $category['id'] ?>"
                                    class="btn btn--danger btn--sm btn-delete"
                                    data-tooltip="Xóa"
                                    onclick="return confirmDelete(<?= $category['post_count'] ?? 0 ?>)">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 60px;">
                            <i class="fas fa-folder-open" style="font-size: 64px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                            <p style="font-size: 18px; color: var(--secondary-color); margin-bottom: 20px;">Chưa có danh mục nào</p>
                            <button class="btn btn--primary" onclick="document.getElementById('category_name').focus()">
                                <i class="fas fa-plus"></i>
                                Tạo danh mục đầu tiên
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Category Form -->
    <div>
        <div class="table-container">
            <h3 style="margin-bottom: 20px; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-plus-circle"></i>
                <span id="formTitle">Thêm danh mục mới</span>
            </h3>

            <form id="categoryForm" action="/admin/categories/store" method="POST" data-validate>
                <input type="hidden" name="id" id="category_id" value="">
                <input type="hidden" name="_method" id="form_method" value="POST">

                <!-- Category Name -->
                <div class="form-group">
                    <label class="form-label">
                        Tên danh mục <span style="color: var(--danger-color);">*</span>
                    </label>
                    <input type="text"
                        name="name"
                        id="category_name"
                        class="form-control"
                        placeholder="Ví dụ: Công nghệ, Tin tức..."
                        required
                        onkeyup="generateCategorySlug()">
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label class="form-label">URL thân thiện (Slug)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text"
                            name="slug"
                            id="category_slug"
                            class="form-control"
                            placeholder="url-than-thien"
                            style="flex: 1;">
                        <button type="button" class="btn btn--info" onclick="generateCategorySlug()">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description"
                        id="category_description"
                        class="form-control"
                        rows="3"
                        placeholder="Mô tả ngắn về danh mục..."></textarea>
                </div>

                <!-- Parent Category -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sitemap"></i>
                        Danh mục cha
                    </label>
                    <select name="parent_id" id="category_parent" class="form-control">
                        <option value="">-- Không có (Danh mục gốc) --</option>
                        <?php if (isset($categories) && count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                                <?php if (empty($cat['parent_id'])): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i>
                        Chọn danh mục cha nếu muốn tạo danh mục con
                    </small>
                </div>

                <!-- Icon -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-icons"></i>
                        Icon (FontAwesome)
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text"
                            name="icon"
                            id="category_icon"
                            class="form-control"
                            placeholder="fas fa-folder"
                            style="flex: 1;"
                            onkeyup="previewIcon()">
                        <div id="iconPreview" style="width: 50px; height: 50px; background: var(--light-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--primary-color);">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                    <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                        <a href="https://fontawesome.com/icons" target="_blank" style="color: var(--primary-color);">
                            <i class="fas fa-external-link-alt"></i>
                            Xem danh sách icons
                        </a>
                    </small>
                </div>

                <!-- Color -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-palette"></i>
                        Màu sắc
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;">
                        <?php
                        $colors = [
                            '#4e73df' => 'Primary',
                            '#1cc88a' => 'Success',
                            '#36b9cc' => 'Info',
                            '#f6c23e' => 'Warning',
                            '#e74a3b' => 'Danger',
                            '#858796' => 'Secondary',
                            '#5a5c69' => 'Dark',
                            '#e83e8c' => 'Pink',
                            '#6f42c1' => 'Purple',
                            '#fd7e14' => 'Orange',
                            '#20c997' => 'Teal',
                            '#17a2b8' => 'Cyan'
                        ];
                        foreach ($colors as $hex => $name): ?>
                            <div>
                                <input type="radio"
                                    name="color"
                                    id="color_<?= $hex ?>"
                                    value="<?= $hex ?>"
                                    style="display: none;"
                                    <?= (!isset($category['color']) && $hex == '#4e73df') ? 'checked' : '' ?>>
                                <label for="color_<?= $hex ?>"
                                    style="width: 100%; height: 40px; background: <?= $hex ?>; border-radius: 8px; cursor: pointer; display: block; border: 3px solid transparent; transition: all 0.3s;"
                                    onmouseover="this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.transform='scale(1)'"
                                    onclick="document.querySelectorAll('label[for^=color_]').forEach(el => el.style.borderColor='transparent'); this.style.borderColor='#fff'; this.style.boxShadow='0 0 0 2px <?= $hex ?>';"
                                    data-tooltip="<?= $name ?>">
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sort-numeric-down"></i>
                        Thứ tự hiển thị
                    </label>
                    <input type="number"
                        name="sort_order"
                        id="category_sort_order"
                        class="form-control"
                        value="0"
                        min="0">
                    <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                        Số càng nhỏ hiển thị càng trước
                    </small>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="status" value="active" checked>
                            <span>
                                <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                                Hoạt động
                            </span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="status" value="inactive">
                            <span>
                                <i class="fas fa-pause-circle" style="color: var(--warning-color);"></i>
                                Tạm dừng
                            </span>
                        </label>
                    </div>
                </div>

                <!-- SEO Meta -->
                <div style="background: var(--light-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-search"></i>
                        Tối ưu SEO
                    </h4>

                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text"
                            name="meta_title"
                            id="category_meta_title"
                            class="form-control"
                            placeholder="Tiêu đề SEO...">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description"
                            id="category_meta_description"
                            class="form-control"
                            rows="2"
                            maxlength="160"
                            placeholder="Mô tả SEO (tối đa 160 ký tự)..."></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-group" style="display: flex; gap: 10px; margin-bottom: 0;">
                    <button type="button"
                        class="btn btn--secondary"
                        onclick="resetForm()"
                        style="flex: 1;">
                        <i class="fas fa-undo"></i>
                        Reset
                    </button>
                    <button type="submit"
                        class="btn btn--primary"
                        style="flex: 2;">
                        <i class="fas fa-save"></i>
                        <span id="submitBtnText">Thêm danh mục</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="table-container" style="margin-top: 20px;">
            <h4 style="margin-bottom: 15px; font-size: 14px;">
                <i class="fas fa-bolt"></i>
                Thao tác nhanh
            </h4>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button class="btn btn--success btn--sm" onclick="bulkActivate()">
                    <i class="fas fa-check-circle"></i>
                    Kích hoạt đã chọn
                </button>
                <button class="btn btn--warning btn--sm" onclick="bulkDeactivate()">
                    <i class="fas fa-pause-circle"></i>
                    Tạm dừng đã chọn
                </button>
                <button class="btn btn--danger btn--sm" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i>
                    Xóa đã chọn
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Generate slug from name
    function generateCategorySlug() {
        const name = document.getElementById('category_name').value;
        const slug = name.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        document.getElementById('category_slug').value = slug;
    }

    // Preview icon
    function previewIcon() {
        const iconClass = document.getElementById('category_icon').value || 'fas fa-folder';
        document.getElementById('iconPreview').innerHTML = `<i class="${iconClass}"></i>`;
    }

    // Edit category
    function editCategory(id) {
        // In real implementation, fetch category data via AJAX
        // For now, show example
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Chỉnh sửa danh mục';
        document.getElementById('submitBtnText').textContent = 'Cập nhật';
        document.getElementById('form_method').value = 'PUT';
        document.getElementById('categoryForm').action = `/admin/categories/update/${id}`;
        document.getElementById('category_id').value = id;

        // Scroll to form
        document.getElementById('categoryForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Reset form
    function resetForm() {
        document.getElementById('categoryForm').reset();
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle"></i> Thêm danh mục mới';
        document.getElementById('submitBtnText').textContent = 'Thêm danh mục';
        document.getElementById('form_method').value = 'POST';
        document.getElementById('categoryForm').action = '/admin/categories/store';
        document.getElementById('category_id').value = '';
        document.getElementById('iconPreview').innerHTML = '<i class="fas fa-folder"></i>';
    }

    // Confirm delete
    function confirmDelete(postCount) {
        if (postCount > 0) {
            return confirm(`CẢNH BÁO: Danh mục này có ${postCount} bài viết. Bạn có chắc muốn xóa?`);
        }
        return confirm('Bạn có chắc muốn xóa danh mục này?');
    }

    // Filter categories
    function filterCategories(status) {
        const rows = document.querySelectorAll('#categoriesTable tbody tr');
        rows.forEach(row => {
            if (!status || row.dataset.status === status || !row.dataset.categoryId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Search categories
    function searchCategories(query) {
        const rows = document.querySelectorAll('#categoriesTable tbody tr');
        const lowerQuery = query.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(lowerQuery) ? '' : 'none';
        });
    }

    // Bulk actions
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.category-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function bulkActivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một danh mục!');
            return;
        }
        if (confirm(`Kích hoạt ${ids.length} danh mục đã chọn?`)) {
            window.location.href = `/admin/categories/bulk-activate?ids=${ids.join(',')}`;
        }
    }

    function bulkDeactivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một danh mục!');
            return;
        }
        if (confirm(`Tạm dừng ${ids.length} danh mục đã chọn?`)) {
            window.location.href = `/admin/categories/bulk-deactivate?ids=${ids.join(',')}`;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một danh mục!');
            return;
        }
        if (confirm(`CẢNH BÁO: Xóa vĩnh viễn ${ids.length} danh mục đã chọn?`)) {
            window.location.href = `/admin/categories/bulk-delete?ids=${ids.join(',')}`;
        }
    }

    // Select all
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.category-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>