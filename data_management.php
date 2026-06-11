<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();
require_once __DIR__ . '/actions/database_tools.php';

$pageTitle = 'Sao lưu và khôi phục dữ liệu';
$activePage = 'data-management';
$tables = database_table_stats();
$totalSize = 0;

foreach ($tables as $table) {
    $totalSize += (int) $table['size_bytes'];
}

require_once __DIR__ . '/partials/header.php';
?>
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Sao lưu dữ liệu</h3>
                </div>
                <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-database-down"></i></span>
            </div>

            <a class="btn btn-success" href="actions/database_backup.php">
                <i class="bi bi-download"></i>
                Tải bản sao lưu
            </a>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Khôi phục dữ liệu</h3>
                </div>
                <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-database-up"></i></span>
            </div>

            <div class="alert alert-warning">
                Import có thể ghi đè hoặc xóa dữ liệu hiện tại. Hãy tải một bản backup trước khi thực hiện.
            </div>

            <form method="post" action="actions/database_import.php" enctype="multipart/form-data" class="vstack gap-3" onsubmit="return confirm('Bạn có chắc muốn import và khôi phục database từ file SQL này?');">
                <?php echo csrf_input(); ?>
                <div>
                    <label class="form-label fw-semibold" for="sql_file">File SQL</label>
                    <input class="form-control" type="file" id="sql_file" name="sql_file" accept=".sql,text/plain,application/sql" required>
                    <div class="form-text">Chỉ chấp nhận file `.sql`, dung lượng tối đa 40 MB.</div>
                </div>
                <div>
                    <label class="form-label fw-semibold" for="confirmation">Xác nhận thao tác</label>
                    <input class="form-control" type="text" id="confirmation" name="confirmation" placeholder="Nhập KHOI PHUC DU LIEU" autocomplete="off" required>
                </div>
                <button class="btn btn-danger align-self-start" type="submit">
                    <i class="bi bi-upload"></i>
                    Khôi phục dữ liệu
                </button>
            </form>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h3 class="section-title">Thông tin dữ liệu hiện tại</h3>
            <p class="text-secondary mb-0"><?php echo e(count($tables)); ?> bảng, dung lượng khoảng <?php echo e(database_format_bytes($totalSize)); ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tên bảng</th>
                    <th>Số dòng ước tính</th>
                    <th>Dung lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table) { ?>
                    <tr>
                        <td><strong><?php echo e($table['table_name']); ?></strong></td>
                        <td><?php echo e((int) $table['estimated_rows']); ?></td>
                        <td><?php echo e(database_format_bytes($table['size_bytes'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
