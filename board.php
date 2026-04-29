<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$boardId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$board = fetch_board_by_id($boardId);

if (!$board) {
    set_flash('danger', 'Khong tim thay board hoac ban khong co quyen truy cap.');
    redirect('boards.php');
}

$pageTitle = $board['name'];
$activePage = 'boards';

$boardMembers = fetch_board_members($boardId);
$statuses = fetch_statuses();
$groupedTasks = fetch_grouped_tasks_by_board($boardId);
$allTasks = fetch_tasks_by_board($boardId);
$doneCount = 0;

foreach ($allTasks as $taskItem) {
    if ($taskItem['status_key'] === 'done') {
        $doneCount++;
    }
}

$completionRate = 0;
if (count($allTasks) > 0) {
    $completionRate = round(($doneCount / count($allTasks)) * 100);
}

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card mb-4">
    <div class="section-header">
        <div>
            <span class="hero-badge mb-2">Board chi tiet</span>
            <h3 class="section-title"><?php echo e($board['name']); ?></h3>
            <p class="section-subtitle mb-1"><?php echo e($board['description']); ?></p>
            <div class="board-meta">
                <span><i class="bi bi-person-badge"></i> Owner: <?php echo e($board['owner_name']); ?></span>
                <span><i class="bi bi-calendar-range"></i> <?php echo e(format_date_vn($board['start_date'])); ?> - <?php echo e(format_date_vn($board['end_date'])); ?></span>
                <span><i class="bi bi-people"></i> <?php echo e(count($boardMembers)); ?> thanh vien</span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <span class="summary-pill">
                <?php echo e($completionRate); ?>% hoan thanh
            </span>
            <?php if (current_user_role() === 'manager') { ?>
                <button
                    class="btn btn-brand"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#taskModal"
                    data-mode="create"
                    data-board-id="<?php echo (int) $boardId; ?>"
                >
                    <i class="bi bi-plus-circle"></i>
                    Tao task
                </button>
            <?php } ?>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <div class="mini-stat">
                <small>Tong task</small>
                <strong><?php echo e(count($allTasks)); ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat">
                <small>Da xong</small>
                <strong><?php echo e($doneCount); ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat">
                <small>Thanh vien</small>
                <strong><?php echo e(count($boardMembers)); ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat">
                <small>Vai tro cua ban</small>
                <strong><?php echo current_user_role() === 'manager' ? 'Quan ly' : 'Thanh vien'; ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="trello-hint mb-3">
    Keo tha task sang cot khac de doi trang thai. Thanh vien chi cap nhat duoc task duoc giao cho minh.
</div>

<div class="row g-4 align-items-start">
    <?php foreach ($statuses as $status) { ?>
        <?php $bucket = $groupedTasks[$status['status_key']]; ?>
        <div class="col-xl-3 col-md-6">
            <section class="task-column <?php echo e(task_status_theme($status['status_key'])); ?>">
                <div class="task-column-header">
                    <div>
                        <h4><?php echo e($status['status_name']); ?></h4>
                        <small><?php echo e(count($bucket['tasks'])); ?> task</small>
                    </div>
                    <span class="column-dot"></span>
                </div>

                <div class="task-list" data-status-id="<?php echo (int) $status['id']; ?>" data-board-id="<?php echo (int) $boardId; ?>">
                    <?php foreach ($bucket['tasks'] as $task) { ?>
                        <?php
                        $editable = can_edit_task($task);
                        $canManage = current_user_role() === 'manager';
                        ?>
                        <article
                            class="task-card <?php echo task_is_overdue($task) ? 'task-card-overdue' : ''; ?>"
                            draggable="<?php echo $editable ? 'true' : 'false'; ?>"
                            data-task-id="<?php echo (int) $task['id']; ?>"
                            data-editable="<?php echo $editable ? '1' : '0'; ?>"
                        >
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <span class="badge <?php echo e(priority_badge_class($task['priority'])); ?>">
                                    <?php echo e($task['priority']); ?>
                                </span>
                                <?php if (task_is_overdue($task)) { ?>
                                    <span class="badge text-bg-danger">Tre han</span>
                                <?php } ?>
                            </div>

                            <h5><?php echo e($task['title']); ?></h5>
                            <p><?php echo e($task['description']); ?></p>

                            <div class="task-card-meta">
                                <span><i class="bi bi-person"></i> <?php echo e($task['assignee_name'] ?: 'Chua giao'); ?></span>
                                <span><i class="bi bi-calendar-event"></i> <?php echo e(format_date_vn($task['deadline'])); ?></span>
                            </div>

                            <div class="progress mt-3" role="progressbar" aria-valuenow="<?php echo (int) $task['progress_percent']; ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar <?php echo e(progress_bar_class($task['progress_percent'])); ?>" style="width: <?php echo (int) $task['progress_percent']; ?>%">
                                    <?php echo (int) $task['progress_percent']; ?>%
                                </div>
                            </div>

                            <?php if (!empty($task['note'])) { ?>
                                <div class="task-note mt-3">
                                    <strong>Cap nhat:</strong> <?php echo e($task['note']); ?>
                                </div>
                            <?php } ?>

                            <div class="task-actions">
                                <?php if ($editable || $canManage) { ?>
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#taskModal"
                                        data-mode="edit"
                                        data-task-id="<?php echo (int) $task['id']; ?>"
                                        data-task-board-id="<?php echo (int) $task['board_id']; ?>"
                                        data-task-title="<?php echo e($task['title']); ?>"
                                        data-task-description="<?php echo e($task['description']); ?>"
                                        data-task-assignee="<?php echo !empty($task['assignee_id']) ? (int) $task['assignee_id'] : ''; ?>"
                                        data-task-status="<?php echo (int) $task['status_id']; ?>"
                                        data-task-priority="<?php echo e($task['priority']); ?>"
                                        data-task-deadline="<?php echo e($task['deadline']); ?>"
                                        data-task-progress="<?php echo (int) $task['progress_percent']; ?>"
                                        data-task-note="<?php echo e($task['note']); ?>"
                                        data-can-manage="<?php echo $canManage ? '1' : '0'; ?>"
                                    >
                                        <?php echo $canManage ? 'Sua task' : 'Cap nhat'; ?>
                                    </button>
                                <?php } ?>

                                <?php if ($canManage) { ?>
                                    <a class="btn btn-outline-danger btn-sm" href="actions/task_delete.php?id=<?php echo (int) $task['id']; ?>&board_id=<?php echo (int) $boardId; ?>" onclick="return confirm('Ban co chac muon xoa task nay khong?')">
                                        Xoa
                                    </a>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>

                    <?php if (empty($bucket['tasks'])) { ?>
                        <div class="empty-task">Chua co task trong cot nay.</div>
                    <?php } ?>
                </div>
            </section>
        </div>
    <?php } ?>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-5">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Thanh vien trong board</h3>
                    <p class="section-subtitle">Danh sach duoc su dung de phan cong task.</p>
                </div>
            </div>
            <div class="vstack gap-3">
                <?php foreach ($boardMembers as $member) { ?>
                    <div class="member-row">
                        <div>
                            <strong><?php echo e($member['full_name']); ?></strong>
                            <p class="mb-0 text-secondary small"><?php echo e($member['department']); ?></p>
                        </div>
                        <span class="badge text-bg-light"><?php echo e($member['role']); ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="content-card h-100">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Huong dan doc board</h3>
                    <p class="section-subtitle">Phan nay giup giai thich bai lam de thuyet trinh de hon.</p>
                </div>
            </div>
            <div class="explain-box">
                <p><strong>Board</strong> la mot du an hoac mot nhom cong viec lon.</p>
                <p><strong>Cot trang thai</strong> mo phong luong cong viec: Chua bat dau, Dang thuc hien, Cho duyet, Hoan thanh.</p>
                <p><strong>Task card</strong> chua ten viec, nguoi thuc hien, muc uu tien, deadline va ty le tien do.</p>
                <p><strong>Dashboard</strong> tong hop so task, task xong va task tre han de quan ly ra quyet dinh nhanh.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="actions/task_save.php" id="taskForm">
                <div class="modal-header">
                    <h5 class="modal-title">Thong tin task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="task_id">
                    <input type="hidden" name="board_id" id="task_board_id" value="<?php echo (int) $boardId; ?>">

                    <div class="alert alert-info d-none" id="memberEditNotice">
                        Thanh vien chi cap nhat duoc trang thai, phan tram tien do va ghi chu cua task da duoc giao cho minh.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Ten task</label>
                            <input type="text" class="form-control manager-only-field" name="title" id="task_title" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Do uu tien</label>
                            <select class="form-select manager-only-field" name="priority" id="task_priority">
                                <option value="Thap">Thap</option>
                                <option value="Trung binh">Trung binh</option>
                                <option value="Cao">Cao</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mo ta</label>
                            <textarea class="form-control manager-only-field" name="description" id="task_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nguoi thuc hien</label>
                            <select class="form-select manager-only-field" name="assignee_id" id="task_assignee_id">
                                <option value="">Chua giao</option>
                                <?php foreach ($boardMembers as $member) { ?>
                                    <option value="<?php echo (int) $member['id']; ?>"><?php echo e($member['full_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trang thai</label>
                            <select class="form-select" name="status_id" id="task_status_id">
                                <?php foreach ($statuses as $status) { ?>
                                    <option value="<?php echo (int) $status['id']; ?>"><?php echo e($status['status_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deadline</label>
                            <input type="date" class="form-control manager-only-field" name="deadline" id="task_deadline">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tien do (%)</label>
                            <input type="number" class="form-control" min="0" max="100" name="progress_percent" id="task_progress_percent" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chu cap nhat</label>
                            <textarea class="form-control" name="note" id="task_note" rows="3" placeholder="Nhan vien co the viet bao cao ngan o day..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Dong</button>
                    <button class="btn btn-brand" type="submit">Luu task</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
