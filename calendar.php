<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Lịch deadline';
$activePage = 'calendar';

$statusId = isset($_GET['status_id']) ? (int) $_GET['status_id'] : 0;
$calendarTasks = fetch_calendar_tasks(50, $statusId);
$statuses = fetch_statuses();

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card mb-4">
    <form method="get" action="calendar.php" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-secondary">Lọc theo trạng thái</label>
            <select name="status_id" class="form-select">
                <option value="0">Tất cả (Chưa hoàn thành)</option>
                <?php foreach ($statuses as $status) { ?>
                    <option value="<?php echo (int) $status['id']; ?>" <?php echo $statusId === (int) $status['id'] ? 'selected' : ''; ?>>
                        <?php echo e($status['status_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-brand w-100">Lọc</button>
        </div>
        <div class="col-md-2">
            <a href="calendar.php" class="btn btn-outline-secondary w-100">Bỏ lọc</a>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h3 class="section-title">Task theo deadline</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Bảng</th>
                    <th>Người thực hiện</th>
                    <th>Trạng thái</th>
                    <th>Deadline</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calendarTasks as $task) { ?>
                    <tr>
                        <td>
                            <strong><?php echo e($task['title']); ?></strong>
                            <div class="text-secondary small"><?php echo e($task['priority']); ?></div>
                            
                        </td>
                        <td>
                            <a href="board.php?id=<?php echo (int) $task['board_id']; ?>" class="fw-medium text-primary text-decoration-none">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                <?php echo e($task['board_name']); ?>
                            </a>
                        </td>
                        <td><?php echo e($task['assignee_name'] ?: 'Chưa giao'); ?></td>
                        <td>
                            <span class="badge <?php echo e(task_status_theme($task['status_key'])); ?> px-3">
                                <?php echo e($task['status_name']); ?>
                            </span>
                        </td>
                        <td class="<?php echo task_is_overdue($task) ? 'text-danger fw-semibold' : ''; ?>">
                            <?php echo e(format_date_vn($task['deadline'])); ?>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?php echo e(google_calendar_url($task)); ?>" target="_blank" rel="noopener">
                                <i class="bi bi-calendar-plus"></i>
                                Google Calendar
                            </a>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (empty($calendarTasks)) { ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Không có task phù hợp.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
