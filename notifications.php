<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$pageTitle = 'Thông báo';
$activePage = 'notifications';
$notifications = fetch_notifications();

require_once __DIR__ . '/partials/header.php';
?>
<div class="content-card">
    <div class="section-header">
        <div>
            <h3 class="section-title">Danh sách thông báo</h3>
        </div>

        <?php if (is_admin()) { ?>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal" data-mode="create">
                <i class="bi bi-plus-circle"></i>
                Tạo thông báo
            </button>
        <?php } ?>
    </div>

    <div class="vstack gap-3">
        <?php foreach ($notifications as $notification) { ?>
            <div class="notice-item">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <strong><?php echo e($notification['title']); ?></strong>
                        <span class="badge <?php echo e(priority_badge_class($notification['priority'])); ?>">
                            <?php echo e($notification['priority']); ?>
                        </span>
                    </div>
                    <p><?php echo nl2br(e($notification['content'])); ?></p>
                    <small class="text-secondary">
                        <?php echo e($notification['creator_name']); ?> - <?php echo e(format_datetime_vn($notification['created_at'])); ?>
                    </small>
                </div>

                <?php if (is_admin()) { ?>
                    <div class="notice-actions">
                        <button
                            class="btn btn-outline-secondary btn-sm"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#notificationModal"
                            data-mode="edit"
                            data-notification-id="<?php echo (int) $notification['id']; ?>"
                            data-notification-title="<?php echo e($notification['title']); ?>"
                            data-notification-content="<?php echo e($notification['content']); ?>"
                            data-notification-priority="<?php echo e($notification['priority']); ?>"
                        >
                            Sửa
                        </button>
                        <a class="btn btn-outline-danger btn-sm" href="actions/notification_delete.php?id=<?php echo (int) $notification['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa thông báo này không?')">
                            Xóa
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (empty($notifications)) { ?>
            <div class="empty-box">Chưa có thông báo nào.</div>
        <?php } ?>
    </div>
</div>

<?php if (is_admin()) { ?>
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="actions/notification_save.php" id="notificationForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Thông tin thông báo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="notification_id">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" name="title" id="notification_title" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mức ưu tiên</label>
                                <select class="form-select" name="priority" id="notification_priority">
                                    <option value="Thấp">Thấp</option>
                                    <option value="Trung bình">Trung bình</option>
                                    <option value="Cao">Cao</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nội dung</label>
                                <textarea class="form-control" name="content" id="notification_content" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Đóng</button>
                        <button class="btn btn-brand" type="submit">Lưu thông báo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
