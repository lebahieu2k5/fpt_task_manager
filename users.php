<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$pageTitle = 'Quản lý tài khoản';
$activePage = 'users';
$users = fetch_all_users();

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card">
    <div class="section-header">
        <div>
            <h3 class="section-title">Tài khoản và phân quyền</h3>
        </div>
        <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#userModal" data-mode="create">
            <i class="bi bi-plus-circle"></i>
            Tạo tài khoản
        </button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Bộ phận</th>
                    <th>Vai trò</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $item) { ?>
                    <tr>
                        <td><strong><?php echo e($item['full_name']); ?></strong></td>
                        <td><?php echo e($item['username']); ?></td>
                        <td><?php echo e($item['email']); ?></td>
                        <td><?php echo e($item['department']); ?></td>
                        <td>
                            <span class="badge text-bg-light"><?php echo e(role_label($item['role'])); ?></span>
                        </td>
                        <td class="text-end">
                            <button
                                class="btn btn-outline-secondary btn-sm"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#userModal"
                                data-mode="edit"
                                data-user-id="<?php echo (int) $item['id']; ?>"
                                data-user-full-name="<?php echo e($item['full_name']); ?>"
                                data-user-email="<?php echo e($item['email']); ?>"
                                data-user-username="<?php echo e($item['username']); ?>"
                                data-user-role="<?php echo e($item['role']); ?>"
                                data-user-department="<?php echo e($item['department']); ?>"
                            >
                                Sửa
                            </button>
                            <a class="btn btn-outline-danger btn-sm" href="actions/user_delete.php?id=<?php echo (int) $item['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Thao tác này không thể hoàn tác.')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="actions/user_save.php" id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="full_name" id="user_full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="user_username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="user_email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bộ phận</label>
                            <input type="text" class="form-control" name="department" id="user_department">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vai trò</label>
                            <select class="form-select" name="role" id="user_role">
                                <option value="admin">Admin</option>
                                <option value="manager">Quản lý</option>
                                <option value="member">Nhân viên</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="password" id="user_password" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-brand" type="submit">Lưu tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
