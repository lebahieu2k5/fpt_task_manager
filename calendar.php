<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Lịch deadline';
$activePage = 'calendar';
$calendarTasks = fetch_calendar_tasks(50);

require_once __DIR__ . '/partials/header.php';
?>
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
                    <th>Deadline</th>
                    <th>Tiến độ</th>
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
                        <td><?php echo e($task['board_name']); ?></td>
                        <td><?php echo e($task['assignee_name'] ?: 'Chưa giao'); ?></td>
                        <td class="<?php echo task_is_overdue($task) ? 'text-danger fw-semibold' : ''; ?>">
                            <?php echo e(format_date_vn($task['deadline'])); ?>
                        </td>
                        <td style="min-width: 160px;">
                            <div class="progress" role="progressbar" aria-valuenow="<?php echo (int) $task['progress_percent']; ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar <?php echo e(progress_bar_class($task['progress_percent'])); ?>" style="width: <?php echo (int) $task['progress_percent']; ?>%">
                                    <?php echo (int) $task['progress_percent']; ?>%
                                </div>
                            </div>
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
                        <td colspan="6" class="text-center text-secondary py-4">Không có task cần đồng bộ lịch.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
