<!-- Page Header -->
<div class="page-header">
    <h1 class="page-header__title">Quản lý người dùng</h1>
    <nav class="page-header__breadcrumb breadcrumb">
        <a href="/admin/dashboard" class="breadcrumb__item breadcrumb__item--link">Admin</a>
        <span class="breadcrumb__separator">/</span>
        <span class="breadcrumb__item breadcrumb__item--active">Người dùng</span>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stats-card">
        <div class="stats-card__content">
            <div class="stats-card__info">
                <h3 class="stats-card__title">Tổng người dùng</h3>
                <div class="stats-card__value"><?= $totalUsers ?? 0 ?></div>
            </div>
            <div class="stats-card__icon stats-card__icon--primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-card__content">
            <div class="stats-card__info">
                <h3 class="stats-card__title">Admin</h3>
                <div class="stats-card__value"><?= $adminCount ?? 0 ?></div>
            </div>
            <div class="stats-card__icon stats-card__icon--danger">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>
</div>

<!-- Action Toolbar -->
<div class="action-toolbar">
    <button class="btn btn--primary" onclick="showAddUserModal()">
        <i class="fas fa-user-plus"></i>
        <span class="btn__text">Thêm người dùng mới</span>
    </button>
    <button class="btn btn--success">
        <i class="fas fa-file-export"></i>
        <span class="btn__text">Xuất danh sách</span>
    </button>
</div>

<!-- Data Table Container -->
<div class="data-table">
    <!-- Table Header with Filters -->
    <div class="data-table__header">
        <h2 class="data-table__title">Danh sách người dùng</h2>

        <!-- Filter Group -->
        <form method="GET" action="" class="filter-group">
            <select name="role" class="filter-group__select">
                <option value="">Tất cả vai trò</option>
                <option value="admin" <?= ($currentRole ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="editor" <?= ($currentRole ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="author" <?= ($currentRole ?? '') === 'author' ? 'selected' : '' ?>>Author</option>
                <option value="user" <?= ($currentRole ?? '') === 'user' ? 'selected' : '' ?>>User</option>
            </select>

            <select name="status" class="filter-group__select">
                <option value="">Tất cả trạng thái</option>
                <option value="active" <?= ($currentStatus ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= ($currentStatus ?? '') === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                <option value="blocked" <?= ($currentStatus ?? '') === 'blocked' ? 'selected' : '' ?>>Bị khóa</option>
            </select>

            <input type="text"
                name="search"
                class="filter-group__input"
                placeholder="Tìm kiếm..."
                value="<?= htmlspecialchars($searchKeyword ?? '') ?>">

            <button type="submit" class="btn btn--info btn--sm">
                <i class="fas fa-search"></i>
            </button>

            <?php if (!empty($currentRole) || !empty($currentStatus) || !empty($searchKeyword)): ?>
                <a href="<?= Router::url('/admin/users') ?>" class="btn btn--secondary btn--sm">
                    <i class="fas fa-times"></i>
                    <span class="btn__text">Xóa filter</span>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table Wrapper -->
    <div class="data-table__wrapper">
        <table class="table">
            <thead class="table__head">
                <tr class="table__row">
                    <th class="table__header table__header--checkbox">
                        <input type="checkbox" id="selectAll" class="table__checkbox">
                    </th>
                    <th class="table__header table__header--id">ID</th>
                    <th class="table__header table__header--avatar">Avatar</th>
                    <th class="table__header">Tên người dùng</th>
                    <th class="table__header">Email</th>
                    <th class="table__header table__header--role">Vai trò</th>
                    <th class="table__header table__header--date">Ngày đăng ký</th>
                    <th class="table__header table__header--actions">Thao tác</th>
                </tr>
            </thead>
            <tbody class="table__body">
                <?php if (isset($users) && count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="table__row">
                            <td class="table__cell table__cell--checkbox">
                                <input type="checkbox" class="table__checkbox user-checkbox" value="<?= $user['id'] ?>">
                            </td>
                            <td class="table__cell table__cell--id"><?= $user['id'] ?></td>
                            <td class="table__cell table__cell--avatar">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= Router::url($user['avatar']) ?>"
                                        alt="Avatar"
                                        class="user-avatar">
                                <?php else: ?>
                                    <div class="user-avatar user-avatar--placeholder">
                                        <i class="fa fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="table__cell">
                                <div class="user-info">
                                    <strong class="user-info__name">
                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </strong>
                                    <small class="user-info__username">
                                        <?= htmlspecialchars($user['last_name'] ?? '') ?>
                                    </small>
                                </div>
                            </td>
                            <td class="table__cell">
                                <div class="user-email">
                                    <i class="fas fa-envelope user-email__icon"></i>
                                    <span class="user-email__text"><?= htmlspecialchars($user['email']) ?></span>
                                </div>
                            </td>
                            <td class="table__cell">
                                <?php
                                $roleColors = [
                                    'admin' => 'danger',
                                    'editor' => 'warning',
                                    'author' => 'info',
                                    'user' => 'success'
                                ];
                                $roleIcons = [
                                    'admin' => 'user-shield',
                                    'editor' => 'user-edit',
                                    'author' => 'user-pen',
                                    'user' => 'user'
                                ];
                                $role = $user['role'] ?? 'user';
                                ?>
                                <span class="badge badge--<?= $roleColors[$role] ?>">
                                    <i class="fas fa-<?= $roleIcons[$role] ?> badge__icon"></i>
                                    <span class="badge__text"><?= ucfirst($role) ?></span>
                                </span>
                            </td>

                            <td class="table__cell"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td class="table__cell table__cell--actions">
                                <div class="action-buttons">
                                    <a href="<?= Router::url("/admin/users/update/{$user['id']}") ?>"
                                        class="btn btn--info btn--sm"
                                        data-tooltip="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                        onclick="confirmDeleteUser(<?= $user['id'] ?>)"
                                        class="btn btn--danger btn--sm"
                                        data-tooltip="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="table__row">
                        <td colspan="9" class="table__cell table__cell--empty">
                            <div class="empty-state">
                                <i class="fas fa-users empty-state__icon"></i>
                                <p class="empty-state__text">Chưa có người dùng nào</p>
                                <button class="btn btn--primary" onclick="showAddUserModal()">
                                    <i class="fas fa-user-plus"></i>
                                    <span class="btn__text">Thêm người dùng đầu tiên</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Hiển thị <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> -
                <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?>
                trong tổng số <?= $pagination['total'] ?> người dùng
            </div>
            <nav class="pagination">
                <?php
                // Build query params for pagination
                $queryParams = [];
                if (!empty($currentRole)) $queryParams['role'] = $currentRole;
                if (!empty($currentStatus)) $queryParams['status'] = $currentStatus;
                if (!empty($searchKeyword)) $queryParams['search'] = $searchKeyword;

                function buildPaginationUrl($page, $params)
                {
                    $params['page'] = $page;
                    return '?' . http_build_query($params);
                }
                ?>

                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= buildPaginationUrl($pagination['current_page'] - 1, $queryParams) ?>"
                        class="pagination__item pagination__item--nav">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == $pagination['current_page']): ?>
                        <span class="pagination__item pagination__item--active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= buildPaginationUrl($i, $queryParams) ?>"
                            class="pagination__item"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagination['has_next']): ?>
                    <a href="<?= buildPaginationUrl($pagination['current_page'] + 1, $queryParams) ?>"
                        class="pagination__item pagination__item--nav">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php elseif (isset($pagination)): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info" style="text-align: center;">
                Hiển thị tất cả <?= $pagination['total'] ?> người dùng (Trang <?= $pagination['current_page'] ?>/<?= $pagination['total_pages'] ?>)
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Redirect to create user page
    function showAddUserModal() {
        window.location.href = '<?= Router::url('/admin/users/create') ?>';
    }

    // Select all checkboxes
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Confirm delete user
    function confirmDeleteUser(userId) {
        if (confirm('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= Router::url('/admin/users/delete/') ?>' + userId;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= $csrfToken ?? '' ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>