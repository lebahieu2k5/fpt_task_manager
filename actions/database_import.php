<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
require_once __DIR__ . '/database_tools.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../data_management.php');
}

$csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!verify_csrf_token($csrfToken)) {
    set_flash('danger', 'Phiên xác nhận không hợp lệ. Vui lòng thử lại.');
    redirect('../data_management.php');
}

$confirmation = isset($_POST['confirmation']) ? trim($_POST['confirmation']) : '';
if ($confirmation !== 'KHOI PHUC DU LIEU') {
    set_flash('danger', 'Nội dung xác nhận chưa đúng. Hãy nhập chính xác: KHOI PHUC DU LIEU');
    redirect('../data_management.php');
}

if (empty($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
    set_flash('danger', 'Không thể tải file SQL lên máy chủ.');
    redirect('../data_management.php');
}

$upload = $_FILES['sql_file'];
$extension = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
$maxSize = 40 * 1024 * 1024;

if ($extension !== 'sql') {
    set_flash('danger', 'Chỉ chấp nhận file có phần mở rộng `.sql`.');
    redirect('../data_management.php');
}

if ((int) $upload['size'] <= 0 || (int) $upload['size'] > $maxSize) {
    set_flash('danger', 'File SQL phải có dung lượng lớn hơn 0 và không vượt quá 40 MB.');
    redirect('../data_management.php');
}

if (!is_uploaded_file($upload['tmp_name']) || !database_sql_file_is_valid($upload['tmp_name'])) {
    set_flash('danger', 'File tải lên không phải nội dung SQL hợp lệ.');
    redirect('../data_management.php');
}

$errorMessage = '';
$restored = database_restore_from_file($upload['tmp_name'], $errorMessage);

if (!$restored) {
    set_flash('danger', 'Import database thất bại: ' . $errorMessage);
    redirect('../data_management.php');
}

$currentAdmin = find_user_by_id(current_user_id());
if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
    unset($_SESSION['user']);
    set_flash('warning', 'Import thành công nhưng tài khoản Admin hiện tại không còn hợp lệ. Vui lòng đăng nhập lại.');
    redirect('../login.php');
}

$_SESSION['user'] = array(
    'id' => $currentAdmin['id'],
    'full_name' => $currentAdmin['full_name'],
    'username' => $currentAdmin['username'],
    'role' => $currentAdmin['role'],
    'department' => $currentAdmin['department'],
);

set_flash('success', 'Đã import và khôi phục database thành công.');
redirect('../data_management.php');
