<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Danh sach bang cong viec';
$activePage = 'boards';

$boards = fetch_boards_for_current_user();
$users = fetch_all_users();

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card mb-4">
    <div class="section-header">
        <div>
            <h3 class="section-title">Tat ca bang cong viec</h3>
            <p class="section-subtitle">
                Moi board dai dien cho mot nhom cong viec hoac mot du an. Day la phan gan nhat voi "Board" trong Trello.
            </p>
        </div>

        <?php if (current_user_role() === 'manager') { ?>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#boardModal" data-mode="create">
                <i class="bi bi-plus-circle"></i>
                Tao board moi
            </button>
        <?php } ?>
    </div>

    <div class="row g-4">
        <?php foreach ($boards as $board) { ?>
            <?php
            $memberIds = fetch_board_member_ids($board['id']);
            $progress = 0;
            if ((int) $board['task_count'] > 0) {
                $progress = round(((int) $board['done_count'] / (int) $board['task_count']) * 100);
            }
            ?>
            <div class="col-lg-6">
                <div class="board-card board-card-solid">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <span class="badge bg-light text-dark mb-2">Owner: <?php echo e($board['owner_name']); ?></span>
                            <h4><?php echo e($board['name']); ?></h4>
                            <p><?php echo e($board['description']); ?></p>
                        </div>
                        <span class="badge rounded-pill text-bg-warning"><?php echo e($progress); ?>%</span>
                    </div>

                    <div class="board-meta">
                        <span><i class="bi bi-people"></i> <?php echo e((int) $board['member_count']); ?> thanh vien</span>
                        <span><i class="bi bi-list-task"></i> <?php echo e((int) $board['task_count']); ?> task</span>
                        <span><i class="bi bi-calendar-event"></i> <?php echo e(format_date_vn($board['end_date'])); ?></span>
                    </div>

                    <div class="progress mt-3" role="progressbar" aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-success" style="width: <?php echo e($progress); ?>%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                        <a class="btn btn-outline-primary btn-sm" href="board.php?id=<?php echo (int) $board['id']; ?>">
                            Xem chi tiet
                        </a>

                        <?php if (current_user_role() === 'manager') { ?>
                            <div class="d-flex gap-2">
                                <button
                                    class="btn btn-outline-secondary btn-sm"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#boardModal"
                                    data-mode="edit"
                                    data-board-id="<?php echo (int) $board['id']; ?>"
                                    data-board-name="<?php echo e($board['name']); ?>"
                                    data-board-description="<?php echo e($board['description']); ?>"
                                    data-board-start="<?php echo e($board['start_date']); ?>"
                                    data-board-end="<?php echo e($board['end_date']); ?>"
                                    data-board-members="<?php echo e(implode(',', $memberIds)); ?>"
                                >
                                    Sua
                                </button>
                                <a class="btn btn-outline-danger btn-sm" href="actions/board_delete.php?id=<?php echo (int) $board['id']; ?>" onclick="return confirm('Ban co chac muon xoa board nay khong?')">
                                    Xoa
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (empty($boards)) { ?>
            <div class="col-12">
                <div class="empty-box">Chua co bang cong viec nao duoc tao.</div>
            </div>
        <?php } ?>
    </div>
</div>

<?php if (current_user_role() === 'manager') { ?>
    <div class="modal fade" id="boardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="actions/board_save.php" id="boardForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Thong tin board</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="board_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ten board</label>
                                <input type="text" class="form-control" name="name" id="board_name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ngay bat dau</label>
                                <input type="date" class="form-control" name="start_date" id="board_start_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Han hoan thanh</label>
                                <input type="date" class="form-control" name="end_date" id="board_end_date">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mo ta ngan</label>
                                <textarea class="form-control" name="description" id="board_description" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Moi thanh vien vao board</label>
                                <select class="form-select" name="member_ids[]" id="board_member_ids" multiple size="6">
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo (int) $user['id']; ?>">
                                            <?php echo e($user['full_name']); ?> - <?php echo e($user['role']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="form-text">Giu phim Ctrl de chon nhieu thanh vien.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Dong</button>
                        <button class="btn btn-brand" type="submit">Luu board</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
