<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Tài khoản';
$activePage = 'profile';
$profileUser = find_user_by_id(current_user_id());

require_once __DIR__ . '/partials/header.php';
?>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Thông tin tài khoản</h3>
                </div>
            </div>

            <form method="post" action="actions/profile_save.php" class="row g-3" id="profilePasswordForm">
                <div class="col-md-6">
                    <label class="form-label">Họ tên</label>
                    <input type="text" class="form-control" name="full_name" value="<?php echo e($profileUser['full_name']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?php echo e($profileUser['username']); ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo e($profileUser['email']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bộ phận</label>
                    <input type="text" class="form-control" name="department" value="<?php echo e($profileUser['department']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" name="password" id="profile_password" autocomplete="new-password">
                    <div id="profile_password_error" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" name="password_confirm" id="profile_password_confirm" autocomplete="new-password">
                    <div id="profile_password_confirm_error" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
                </div>
                <div class="col-12">
                    <button class="btn btn-brand" type="submit">
                        <i class="bi bi-save"></i>
                        Lưu tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Phân quyền</h3>
                </div>
            </div>
            <div class="vstack gap-3">
                <div class="member-row">
                    <div>
                        <strong><?php echo e(role_label(current_user_role())); ?></strong>
                        <p class="mb-0 text-secondary small">Vai trò hiện tại</p>
                    </div>
                    <span class="badge text-bg-light"><?php echo e($profileUser['role']); ?></span>
                </div>
                <div class="member-row">
                    <div>
                        <strong><?php echo e($profileUser['username']); ?></strong>
                        <p class="mb-0 text-secondary small">Tài khoản đăng nhập</p>
                    </div>
                    <span class="badge text-bg-success">Active</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
