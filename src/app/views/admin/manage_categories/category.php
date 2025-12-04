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
<div class="category-stats">
    <div class="category-stats__card">
        <div class="category-stats__content">
            <h3 class="category-stats__label">Tổng danh mục</h3>
            <div class="category-stats__value"><?= $totalCategories ?? 0 ?></div>
        </div>
        <div class="category-stats__icon category-stats__icon--primary">
            <i class="fas fa-folder"></i>
        </div>
    </div>

    <div class="category-stats__card">
        <div class="category-stats__content">
            <h3 class="category-stats__label">Tổng bài viết</h3>
            <div class="category-stats__value"><?= $totalPosts ?? 0 ?></div>
        </div>
        <div class="category-stats__icon category-stats__icon--warning">
            <i class="fas fa-newspaper"></i>
        </div>
    </div>
</div>

<!-- Category Table -->
<div class="category-table">
    <div class="category-table__header">
        <h2 class="category-table__title">
            <i class="fas fa-list"></i> Danh sách danh mục
        </h2>
        <div class="category-table__actions">
            <div class="category-table__search">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Tìm kiếm danh mục..."
                    onkeyup="searchCategories(this.value)"
                    id="search_input">
                <button class="btn btn--info btn--icon" onclick="document.getElementById('search_input').focus()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <button class="btn btn--primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>

    <div class="category-table__wrapper">
        <table class="table" id="categoriesTable">
            <thead class="table__head">
                <tr>
                    <th class="table__th table__th--checkbox">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th class="table__th table__th--id">ID</th>
                    <th class="table__th">Tên danh mục</th>
                    <th class="table__th table__th--slug">Slug</th>
                    <th class="table__th table__th--count">Bài viết</th>
                    <th class="table__th table__th--actions">Thao tác</th>
                </tr>
            </thead>
            <tbody class="table__body">
                <?php if (isset($categories) && count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr class="table__row" data-category-id="<?= $category['id'] ?>">
                            <td class="table__td table__td--checkbox">
                                <input type="checkbox" class="category-checkbox" value="<?= $category['id'] ?>">
                            </td>
                            <td class="table__td table__td--id">
                                <strong class="table__id"><?= $category['id'] ?></strong>
                            </td>
                            <td class="table__td">
                                <div class="category-info">
                                    <div class="category-info__icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div class="category-info__details">
                                        <span class="category-info__name">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </span>
                                        <?php if (!empty($category['description'])): ?>
                                            <small class="category-info__desc">
                                                <?= htmlspecialchars(mb_substr($category['description'], 0, 80)) ?><?= mb_strlen($category['description']) > 80 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td table__td--slug">
                                <code class="table__code"><?= htmlspecialchars($category['slug']) ?></code>
                            </td>
                            <td class="table__td table__td--count">
                                <a href="<?php echo Router::url('/admin/posts?category=' . $category['id']); ?>"
                                    class="category-count"
                                    title="Xem bài viết">
                                    <i class="fas fa-newspaper"></i>
                                    <span><?= $category['post_count'] ?? 0 ?></span>
                                </a>
                            </td>
                            <td class="table__td table__td--actions">
                                <div class="table-actions">
                                    <button
                                        onclick='openEditModal(<?= json_encode($category) ?>)'
                                        class="btn btn--info btn--sm"
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo Router::url('/category/' . $category['slug']); ?>"
                                        target="_blank"
                                        class="btn btn--success btn--sm"
                                        title="Xem trang">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST"
                                        action="<?php echo Router::url('/admin/categories/delete/' . $category['id']); ?>"
                                        style="display: inline;"
                                        onsubmit="return confirmDelete('<?= htmlspecialchars($category['name']) ?>', <?= $category['post_count'] ?? 0 ?>)">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="btn btn--danger btn--sm" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="table__row table__row--empty">
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-folder-open empty-state__icon"></i>
                                <p class="empty-state__title">Chưa có danh mục nào</p>
                                <p class="empty-state__text">Hãy tạo danh mục đầu tiên cho blog của bạn</p>
                                <button class="btn btn--primary" onclick="openAddModal()">
                                    <i class="fas fa-plus"></i>
                                    <span>Tạo danh mục đầu tiên</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Quick Actions -->
    <?php if (isset($categories) && count($categories) > 0): ?>
        <div class="category-table__footer">
            <div class="category-table__bulk">
                <h4 class="category-table__bulk-title">
                    <i class="fas fa-tasks"></i> Thao tác với <span id="selected_count">0</span> mục đã chọn:
                </h4>
                <div class="category-table__bulk-actions">
                    <button class="btn btn--danger btn--sm" onclick="bulkDelete()" id="bulk_delete_btn" disabled>
                        <i class="fas fa-trash"></i>
                        <span>Xóa đã chọn</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include form modals
include __DIR__ . '/add_category.php';
include __DIR__ . '/edit_category.php';
?>

<script src="<?php echo Router::url('/js/admin-category.js'); ?>"></script>