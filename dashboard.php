<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Dashboard tien do';
$activePage = 'dashboard';

$stats = fetch_dashboard_stats();
$boards = fetch_boards_for_current_user();
$upcomingTasks = fetch_upcoming_tasks(6);
$overdueTasks = fetch_overdue_tasks(6);
$completionRate = 0;

if (!empty($stats['task_count'])) {
    $completionRate = round(((int) $stats['done_count'] / (int) $stats['task_count']) * 100);
}

require_once __DIR__ . '/partials/header.php';
?>
<section class="hero-summary">
    <div>
        <span class="hero-badge">Tong quan he thong</span>
        <h2>Theo doi cong viec cua team trong mot man hinh</h2>
        <p class="text-secondary mb-0">
            Giao dien nay duoc rut gon tu Use Case: tao bang, giao viec, theo doi tien do va nhin nhanh task tre han.
        </p>
    </div>
    <div class="hero-metric">
        <small>Ti le hoan thanh</small>
        <strong><?php echo e($completionRate); ?>%</strong>
    </div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-kanban"></i></span>
            <div>
                <small>Tong so bang</small>
                <h3><?php echo e((int) $stats['board_count']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-list-check"></i></span>
            <div>
                <small>Tong task</small>
                <h3><?php echo e((int) $stats['task_count']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <small>Da hoan thanh</small>
                <h3><?php echo e((int) $stats['done_count']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-alarm"></i></span>
            <div>
                <small>Tre han</small>
                <h3><?php echo e((int) $stats['overdue_count']); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Bang cong viec noi bat</h3>
                    <p class="section-subtitle">Nhanh chong vao cac board dang duoc theo doi.</p>
                </div>
                <a class="btn btn-outline-secondary btn-sm" href="boards.php">Xem tat ca</a>
            </div>

            <div class="row g-3">
                <?php foreach (array_slice($boards, 0, 4) as $board) { ?>
                    <?php
                    $progress = 0;
                    if ((int) $board['task_count'] > 0) {
                        $progress = round(((int) $board['done_count'] / (int) $board['task_count']) * 100);
                    }
                    ?>
                    <div class="col-md-6">
                        <a class="board-card" href="board.php?id=<?php echo (int) $board['id']; ?>">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h4><?php echo e($board['name']); ?></h4>
                                    <p><?php echo e($board['description']); ?></p>
                                </div>
                                <span class="badge text-bg-light"><?php echo e($progress); ?>%</span>
                            </div>
                            <div class="board-meta">
                                <span><i class="bi bi-people"></i> <?php echo e((int) $board['member_count']); ?> thanh vien</span>
                                <span><i class="bi bi-list-task"></i> <?php echo e((int) $board['task_count']); ?> task</span>
                            </div>
                        </a>
                    </div>
                <?php } ?>

                <?php if (empty($boards)) { ?>
                    <div class="col-12">
                        <div class="empty-box">
                            Chua co board nao. Hay vao muc <strong>Bang cong viec</strong> de tao board dau tien.
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Task den han som</h3>
                    <p class="section-subtitle">Dung de theo doi tien do va nhac viec.</p>
                </div>
            </div>

            <div class="vstack gap-3">
                <?php foreach ($upcomingTasks as $task) { ?>
                    <div class="deadline-item">
                        <div>
                            <strong><?php echo e($task['title']); ?></strong>
                            <p class="mb-1"><?php echo e($task['board_name']); ?> - <?php echo e($task['assignee_name'] ?: 'Chua giao'); ?></p>
                            <small class="text-secondary">Deadline: <?php echo e(format_date_vn($task['deadline'])); ?></small>
                        </div>
                        <span class="badge <?php echo e(priority_badge_class($task['priority'])); ?>">
                            <?php echo e($task['priority']); ?>
                        </span>
                    </div>
                <?php } ?>

                <?php if (empty($upcomingTasks)) { ?>
                    <div class="empty-box">Khong co task sap den han.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="content-card">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Danh sach task tre han</h3>
                    <p class="section-subtitle">Phan nay phu hop voi use case giam sat va phat hien tre han.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Board</th>
                            <th>Nguoi thuc hien</th>
                            <th>Deadline</th>
                            <th>Tien do</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdueTasks as $task) { ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($task['title']); ?></strong>
                                    <div class="text-secondary small"><?php echo e($task['priority']); ?></div>
                                </td>
                                <td><?php echo e($task['board_name']); ?></td>
                                <td><?php echo e($task['assignee_name'] ?: 'Chua giao'); ?></td>
                                <td class="text-danger fw-semibold"><?php echo e(format_date_vn($task['deadline'])); ?></td>
                                <td style="min-width: 180px;">
                                    <div class="progress" role="progressbar" aria-valuenow="<?php echo (int) $task['progress_percent']; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar <?php echo e(progress_bar_class($task['progress_percent'])); ?>" style="width: <?php echo (int) $task['progress_percent']; ?>%">
                                            <?php echo (int) $task['progress_percent']; ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if (empty($overdueTasks)) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">Khong co task nao dang tre han.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
