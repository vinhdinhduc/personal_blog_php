<div class="tags">
    <div class="tags__header">
        <div class="tags__header-top">
            <h1 class="tags__title">Quản Lý Tags</h1>
            <a href="<?= Router::url('/admin/tags/create') ?>" class="tags__btn tags__btn--primary">
                <i class="fas fa-plus"></i> Thêm Tag Mới
            </a>
        </div>

        <!-- Stats -->
        <div class="tags__stats">
            <div class="tags__stat-item tags__stat-item--total">
                <div class="tags__stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="tags__stat-info">
                    <span class="tags__stat-label">Tổng số tags</span>
                    <span class="tags__stat-value"><?= $stats['total'] ?></span>
                </div>
            </div>
            <div class="tags__stat-item tags__stat-item--used">
                <div class="tags__stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="tags__stat-info">
                    <span class="tags__stat-label">Đang sử dụng</span>
                    <span class="tags__stat-value"><?= $stats['used'] ?></span>
                </div>
            </div>
            <div class="tags__stat-item tags__stat-item--unused">
                <div class="tags__stat-icon">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="tags__stat-info">
                    <span class="tags__stat-label">Chưa sử dụng</span>
                    <span class="tags__stat-value"><?= $stats['unused'] ?></span>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="tags__toolbar">
            <form method="GET" action="<?= Router::url('/admin/tags') ?>" class="tags__search-form">
                <div class="tags__search-group">
                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Tìm kiếm tags..."
                        class="tags__search-input">
                    <button type="submit" class="tags__btn tags__btn--search">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= Router::url('/admin/tags') ?>" class="tags__btn tags__btn--clear">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="tags__bulk-actions">
                <button type="button" class="tags__btn tags__btn--danger" id="bulkDeleteBtn" disabled>
                    <i class="fas fa-trash"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>
    </div>

    <!-- Tags Table -->
    <?php if (empty($tags)): ?>
        <div class="tags__empty">
            <i class="fas fa-tags"></i>
            <p><?= empty($search) ? 'Chưa có tag nào' : 'Không tìm thấy tag phù hợp' ?></p>
            <?php if (empty($search)): ?>
                <a href="<?= Router::url('/admin/tags/create') ?>" class="tags__btn tags__btn--primary">
                    Tạo Tag Đầu Tiên
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="tags__table-wrapper">
            <table class="tags__table">
                <thead class="tags__table-head">
                    <tr>
                        <th class="tags__table-th tags__table-th--checkbox">
                            <input type="checkbox" id="selectAll" class="tags__checkbox">
                        </th>
                        <th class="tags__table-th tags__table-th--name">Tên Tag</th>
                        <th class="tags__table-th tags__table-th--slug">Slug</th>
                        <th class="tags__table-th tags__table-th--count">Số Bài Viết</th>
                        <th class="tags__table-th tags__table-th--date">Ngày Tạo</th>
                        <th class="tags__table-th tags__table-th--actions">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="tags__table-body">
                    <?php foreach ($tags as $tag): ?>
                        <tr class="tags__table-row" data-tag-id="<?= $tag['id'] ?>">
                            <td class="tags__table-td tags__table-td--checkbox">
                                <input type="checkbox" class="tags__checkbox tag-checkbox" value="<?= $tag['id'] ?>">
                            </td>
                            <td class="tags__table-td tags__table-td--name">
                                <div class="tags__name-cell">
                                    <span class="tags__tag-badge"><?= htmlspecialchars($tag['name']) ?></span>
                                    <?php if ($tag['post_count'] == 0): ?>
                                        <span class="tags__badge tags__badge--unused">Chưa dùng</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="tags__table-td tags__table-td--slug">
                                <code class="tags__slug"><?= htmlspecialchars($tag['slug']) ?></code>
                            </td>
                            <td class="tags__table-td tags__table-td--count">
                                <span class="tags__count"><?= $tag['post_count'] ?></span>
                            </td>
                            <td class="tags__table-td tags__table-td--date">
                                <?= date('d/m/Y', strtotime($tag['created_at'])) ?>
                            </td>
                            <td class="tags__table-td tags__table-td--actions">
                                <div class="tags__actions">
                                    <a href="<?= Router::url('/admin/tags/view/' . $tag['id']) ?>"
                                        class="tags__action-btn tags__action-btn--view"
                                        title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= Router::url('/admin/tags/edit/' . $tag['id']) ?>"
                                        class="tags__action-btn tags__action-btn--edit"
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="tags__action-btn tags__action-btn--delete"
                                        onclick="deleteTag(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['name']) ?>', <?= $tag['post_count'] ?>)"
                                        title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="tags__pagination">
                <div class="tags__pagination-info">
                    Hiển thị <?= count($tags) ?> / <?= $pagination['total'] ?> tags
                </div>
                <div class="tags__pagination-links">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                            class="tags__pagination-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $pagination['current_page']): ?>
                            <span class="tags__pagination-link tags__pagination-link--active"><?= $i ?></span>
                        <?php elseif ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                            <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                                class="tags__pagination-link">
                                <?= $i ?>
                            </a>
                        <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                            <span class="tags__pagination-link tags__pagination-link--dots">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagination['has_next']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                            class="tags__pagination-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="tags__modal" id="deleteModal">
    <div class="tags__modal-content">
        <div class="tags__modal-header">
            <h3 class="tags__modal-title">Xác Nhận Xóa Tag</h3>
            <button type="button" class="tags__modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tags__modal-body">
            <p id="deleteMessage">Bạn có chắc chắn muốn xóa tag này?</p>
            <p class="tags__modal-warning" id="deleteWarning" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                Tag này đang được sử dụng và sẽ bị gỡ khỏi tất cả bài viết!
            </p>
        </div>
        <div class="tags__modal-footer">
            <button type="button" class="tags__btn tags__btn--secondary" onclick="closeDeleteModal()">
                Hủy
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="force" value="1">
                <button type="submit" class="tags__btn tags__btn--danger">
                    <i class="fas fa-trash"></i> Xóa Tag
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= Router::url() ?>';
</script>
<script src="<?= Router::url('/public/js/admin-tags.js') ?>"></script>