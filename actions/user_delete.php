<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();

$userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($userId === current_user_id()) {
    set_flash('danger', 'Bạn không thể tự xóa chính mình.');
    redirect('../users.php');
}

$user = find_user_by_id($userId);

if (!$user) {
    set_flash('danger', 'Không tìm thấy người dùng.');
    redirect('../users.php');
}

$statement = $pdo->prepare('DELETE FROM users WHERE id = :id');
$statement->execute(array('id' => $userId));

set_flash('success', 'Đã xóa tài khoản thành công.');
redirect('../users.php');
exit;
