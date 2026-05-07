<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();

$notificationId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$notification = fetch_notification_by_id($notificationId);

if (!$notification) {
    set_flash('danger', 'Không tìm thấy thông báo cần xóa.');
    redirect('../notifications.php');
}

$statement = $pdo->prepare('DELETE FROM notifications WHERE id = :id');
$statement->execute(array('id' => $notificationId));

set_flash('success', 'Đã xóa thông báo.');
redirect('../notifications.php');
