<?php
require_once __DIR__ . '/bootstrap.php';
require_report_access();

$pageTitle = 'Báo cáo';
$activePage = 'reports';

$stats = fetch_dashboard_stats();
$boards = fetch_boards_for_current_user();
$statusReport = fetch_task_status_report();
$assigneeReport = fetch_assignee_report();
$completionRate = 0;

if (!empty($stats['task_count'])) {
    $completionRate = round(((int) $stats['done_count'] / (int) $stats['task_count']) * 100);
}

require_once __DIR__ . '/partials/header.php';
?>
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-kanban"></i></span>
            <div>
                <small>Bảng công việc</small>
                <h3><?php echo e((int) $stats['board_count']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-list-check"></i></span>
            <div>
                <small>Tổng task</small>
                <h3><?php echo e((int) $stats['task_count']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <small>Hoàn thành</small>
                <h3><?php echo e($completionRate); ?>%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-alarm"></i></span>
            <div>
                <small>Trễ hạn</small>
                <h3><?php echo e((int) $stats['overdue_count']); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-5">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Thống kê trạng thái</h3>
                </div>
            </div>

            <div class="vstack gap-3">
                <?php foreach ($statusReport as $row) { ?>
                    <?php
                    $percent = 0;
                    if (!empty($stats['task_count'])) {
                        $percent = round(((int) $row['task_count'] / (int) $stats['task_count']) * 100);
                    }
                    ?>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong><?php echo e($row['status_name']); ?></strong>
                            <span><?php echo e((int) $row['task_count']); ?></span>
                        </div>
                        <div class="progress" role="progressbar" aria-valuenow="<?php echo e($percent); ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar <?php echo e(progress_bar_class($percent)); ?>" style="width: <?php echo e($percent); ?>%"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Tiến độ theo bảng</h3>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Bảng</th>
                            <th>Thành viên</th>
                            <th>Task</th>
                            <th>Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($boards as $board) { ?>
                            <?php
                            $progress = 0;
                            if ((int) $board['task_count'] > 0) {
                                $progress = round(((int) $board['done_count'] / (int) $board['task_count']) * 100);
                            }
                            ?>
                            <tr>
                                <td><strong><?php echo e($board['name']); ?></strong></td>
                                <td><?php echo e((int) $board['member_count']); ?></td>
                                <td><?php echo e((int) $board['task_count']); ?></td>
                                <td style="min-width: 180px;">
                                    <div class="progress" role="progressbar" aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success" style="width: <?php echo e($progress); ?>%">
                                            <?php echo e($progress); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($boards)) { ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Chưa có dữ liệu.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="content-card mt-4">
    <div class="section-header">
        <div>
            <h3 class="section-title">Báo cáo theo nhân sự</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nhân sự</th>
                    <th>Task</th>
                    <th>Đã xong</th>
                    <th>Trễ hạn</th>
                    <th>Tiến độ TB</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assigneeReport as $row) { ?>
                    <tr>
                        <td><strong><?php echo e($row['assignee_name']); ?></strong></td>
                        <td><?php echo e((int) $row['task_count']); ?></td>
                        <td><?php echo e((int) $row['done_count']); ?></td>
                        <td><?php echo e((int) $row['overdue_count']); ?></td>
                        <td><?php echo e((int) $row['avg_progress']); ?>%</td>
                    </tr>
                <?php } ?>
                <?php if (empty($assigneeReport)) { ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">Chưa có dữ liệu.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
