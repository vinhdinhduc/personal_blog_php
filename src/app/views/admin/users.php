<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý người dùng</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Người dùng</span>
    </div>
</div>

<!-- Statistics -->
<div class="card-grid" style="margin-bottom: 30px;">
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Tổng người dùng</h3>
                <div class="card__value"><?= $totalUsers ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Admin</h3>
                <div class="card__value"><?= $adminCount ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--danger">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Hoạt động</h3>
                <div class="card__value"><?= $activeUsers ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--success">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Bị khóa</h3>
                <div class="card__value"><?= $blockedUsers ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--warning">
                <i class="fas fa-user-lock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="margin-bottom: 20px;">
    <button class="btn btn--primary" onclick="showAddUserModal()">
        <i class="fas fa-user-plus"></i>
        Thêm người dùng mới
    </button>
    <button class="btn btn--success">
        <i class="fas fa-file-export"></i>
        Xuất danh sách
    </button>
</div>

<!-- Users Table -->
<div class="table-container">
    <div class="table-container__header">
        <h2 class="table-container__title">Danh sách người dùng</h2>

        <!-- Filter & Search -->
        <div style="display: flex; gap: 10px;">
            <select class="form-control" style="width: 150px;">
                <option value="">Tất cả vai trò</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="author">Author</option>
                <option value="user">User</option>
            </select>

            <select class="form-control" style="width: 150px;">
                <option value="">Tất cả trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="inactive">Không hoạt động</option>
                <option value="blocked">Bị khóa</option>
            </select>

            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 250px;">

            <button class="btn btn--info btn--sm">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 50px;">
                    <input type="checkbox" id="selectAll">
                </th>
                <th style="width: 60px;">ID</th>
                <th style="width: 80px;">Avatar</th>
                <th>Tên người dùng</th>
                <th>Email</th>
                <th style="width: 120px;">Vai trò</th>
                <th style="width: 100px;">Bài viết</th>
                <th style="width: 150px;">Ngày đăng ký</th>
                <th style="width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($users) && count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>">
                        </td>
                        <td><?= $user['id'] ?></td>
                        <td>
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= htmlspecialchars($user['avatar']) ?>"
                                    alt="Avatar"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid var(--primary-color);">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 18px;">
                                    <?= strtoupper(substr($user['first_name'] . $user['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                            <br>
                            <small style="color: var(--secondary-color);">
                                <?= htmlspecialchars($user['last_name'] ?? '') ?>
                            </small>
                        </td>
                        <td>
                            <i class="fas fa-envelope" style="color: var(--info-color);"></i>
                            <?= htmlspecialchars($user['email']) ?>
                        </td>
                        <td>
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
                                <i class="fas fa-<?= $roleIcons[$role] ?>"></i>
                                <?= ucfirst($role) ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span style="background: var(--light-color); padding: 5px 12px; border-radius: 15px; font-weight: 500;">
                                <?= $user['post_count'] ?? 0 ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>

                        <td>
                            <a href=<?= Router::url("/admin/users/update/{$user['id']}") ?>
                                class="btn btn--info btn--sm"
                                data-tooltip="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>


                            <a href="/admin/users/delete/<?= $user['id'] ?>"
                                class="btn btn--danger btn--sm btn-delete"
                                data-tooltip="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 60px;">
                        <i class="fas fa-users" style="font-size: 64px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                        <p style="font-size: 18px; color: var(--secondary-color); margin-bottom: 20px;">Chưa có người dùng nào</p>
                        <button class="btn btn--primary" onclick="showAddUserModal()">
                            <i class="fas fa-user-plus"></i>
                            Thêm người dùng đầu tiên
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-top: 1px solid #eee;">
            <div style="color: var(--secondary-color);">
                Hiển thị <?= ($currentPage - 1) * $perPage + 1 ?> -
                <?= min($currentPage * $perPage, $totalUsers) ?>
                trong tổng số <?= $totalUsers ?> người dùng
            </div>
            <div style="display: flex; gap: 5px;">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="btn btn--sm">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <button class="btn btn--primary btn--sm"><?= $i ?></button>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>" class="btn btn--sm"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="btn btn--sm">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
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
</script>