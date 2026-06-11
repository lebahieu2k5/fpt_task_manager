<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();
require_once __DIR__ . '/actions/user_export_helpers.php';

$pageTitle = 'Quản lý tài khoản';
$activePage = 'users';
$filters = get_user_filters($_GET);
$users = fetch_all_users($filters);
$filterQuery = user_filter_query($filters);
$exportSuffix = $filterQuery !== '' ? '?' . $filterQuery : '';
$activeFilterCount = count(array_filter($filters, function ($value) {
    return $value !== '';
}));

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card">
    <div class="section-header">
        <div>
            <h3 class="section-title">Tài khoản và phân quyền</h3>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="actions/user_export_excel.php<?php echo e($exportSuffix); ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i>
                Xuất Excel
            </a>
            <a href="actions/user_export_word.php<?php echo e($exportSuffix); ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-word"></i>
                Xuất Word
            </a>
            <a href="actions/user_export_pdf.php<?php echo e($exportSuffix); ?>" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i>
                Xuất PDF
            </a>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#userModal" data-mode="create">
                <i class="bi bi-plus-circle"></i>
                Tạo tài khoản
            </button>
        </div>
    </div>

    <form method="get" action="users.php" class="user-filter-box mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-1"><i class="bi bi-funnel"></i> Bộ lọc tài khoản</h5>
                <!-- <small class="text-secondary">Có thể nhập một hoặc kết hợp nhiều điều kiện để tìm chính xác hơn.</small> -->
            </div>
            <?php if ($activeFilterCount > 0) { ?>
                <span class="badge text-bg-primary"><?php echo e($activeFilterCount); ?> điều kiện đang áp dụng</span>
            <?php } ?>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_full_name">Họ tên</label>
                <input class="form-control" id="filter_full_name" name="full_name" value="<?php echo e($filters['full_name']); ?>" placeholder="Nhập họ tên...">
            </div>
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_username">Username</label>
                <input class="form-control" id="filter_username" name="username" value="<?php echo e($filters['username']); ?>" placeholder="Nhập username...">
            </div>
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_email">Email</label>
                <input class="form-control" id="filter_email" name="email" value="<?php echo e($filters['email']); ?>" placeholder="Nhập email...">
            </div>
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_department">Bộ phận</label>
                <input class="form-control" id="filter_department" name="department" value="<?php echo e($filters['department']); ?>" placeholder="Nhập bộ phận...">
            </div>
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_role">Vai trò</label>
                <select class="form-select" id="filter_role" name="role">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin" <?php echo $filters['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="manager" <?php echo $filters['role'] === 'manager' ? 'selected' : ''; ?>>Quản lý</option>
                    <option value="member" <?php echo $filters['role'] === 'member' ? 'selected' : ''; ?>>Nhân viên</option>
                </select>
            </div>
            <script>
                flatpickr(".datepicker", {
                    dateFormat: "m/d/Y", // Định dạng hiển thị và gửi lên server
                });
            </script>
            <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_created_from">Ngày tạo từ</label>
                <input type="date" class="form-control" id="filter_created_from" name="created_from" value="<?php echo e($filters['created_from']); ?>">
                <!-- <div class="col-md-6 col-xl-3">
                    <label class="form-label small fw-semibold" for="filter_created_from" width="100%">Ngày tạo từ</label>
                    <input type="text" 
                        class="form-control" 
                        id="filter_created_from" 
                        name="created_from" 
                        placeholder="mm/dd/yyyy"
                        value="<?php echo e($filters['created_from'] ? date('m/d/Y', strtotime($filters['created_from'])) : ''); ?>">
                </div> -->
                <!-- <div id="filter_created_from_display" class="form-text"></div> -->
                <!-- <input type="date" class="form-control" id="filter_created_from" name="created_from" value="<?= date('m/d/Y H:i', strtotime($user['created_at'])) ?>"> -->
            </div>
            <!-- <div class="col-md-6 col-xl-3">
                <label class="form-label small fw-semibold" for="filter_created_to">Ngày tạo đến</label>
                <input type="date" class="form-control" id="filter_created_to" name="created_to" value="<?php echo e($filters['created_to']); ?>">
            </div> -->
            <div class="col-md-6 col-xl-3 d-flex align-items-end gap-2">
                <button class="btn btn-brand flex-grow-1" type="submit">
                    <i class="bi bi-search"></i>
                    Tìm kiếm
                </button>
                <a class="btn btn-outline-secondary" href="users.php" title="Xóa toàn bộ bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Đặt lại
                </a>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
        <strong>Kết quả: <?php echo e(count($users)); ?> tài khoản</strong>
        <?php if ($activeFilterCount > 0) { ?>
            <small class="text-secondary">Các file xuất sẽ chứa đúng danh sách đang được lọc.</small>
        <?php } ?>
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
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <style>
                    /* .ad td{
                        background-color: red !important;
                    }
                    .ma td{
                        background-color: green !important;
                    }
                    .mem td{
                        background-color: blue !important;
                    } */
                </style>
                <?php foreach ($users as $item) { ?>
                    <?php if($item['role']=="admin"): ?>
                    
                    <tr class="ad" style="background-color: red !important;">
                       
                        <td><strong><?php echo e($item['full_name']); ?></strong></td>
                        <td><?php echo e($item['username']); ?></td>
                        <td><?php echo e($item['email']); ?></td>
                        <td><?php echo e($item['department']); ?></td>
                        <td>
                            <span class="badge text-bg-light"><?php echo e(role_label($item['role'])); ?></span>
                        </td>
                        <td><?php echo e(format_datetime_vn($item['created_at'])); ?></td>
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
                    <?php elseif($item['role'] =="manager"): ?>
                    <tr class="ma">
                        
                        <td><strong><?php echo e($item['full_name']); ?></strong></td>
                        <td><?php echo e($item['username']); ?></td>
                        <td><?php echo e($item['email']); ?></td>
                        <td><?php echo e($item['department']); ?></td>
                        <td>
                            <span class="badge text-bg-light"><?php echo e(role_label($item['role'])); ?></span>
                        </td>
                        <td><?php echo e(format_datetime_vn($item['created_at'])); ?></td>
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
                    <?php elseif($item['role']=="member" ): ?>
                        <tr class="mem">               
                        <td><strong><?php echo e($item['full_name']); ?></strong></td>
                        <td><?php echo e($item['username']); ?></td>
                        <td><?php echo e($item['email']); ?></td>
                        <td><?php echo e($item['department']); ?></td>
                        <td>
                            <span class="badge text-bg-light"><?php echo e(role_label($item['role'])); ?></span>
                        </td>
                        <td><?php echo e(format_datetime_vn($item['created_at'])); ?></td>
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
                    <?php endif; ?>
                <?php } ?>
                <?php if (empty($users)) { ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            Không tìm thấy tài khoản phù hợp với các điều kiện lọc.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var createdFromInput = document.getElementById('filter_created_from');
        var createdFromDisplay = document.getElementById('filter_created_from_display');

        function updateCreatedFromDisplay() {
            if (!createdFromInput.value) {
                createdFromDisplay.textContent = '';
                return;
            }

            var dateParts = createdFromInput.value.split('-');
            createdFromDisplay.textContent = 'Ngày đã chọn: '
                + dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
        }

        createdFromInput.addEventListener('change', updateCreatedFromDisplay);
        updateCreatedFromDisplay();
    });
</script>

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
                            <div id="username_error_msg" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" id="user_email">
                            <div id="email_error_msg" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
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
                            <div id="password_error_msg" class="text-danger small mt-1" style="display: none; font-weight: 500; white-space: pre-line;"></div>
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
