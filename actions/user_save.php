<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../users.php');
}

$userId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$fullName = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
$role = isset($_POST['role']) ? $_POST['role'] : 'member';
$department = trim(isset($_POST['department']) ? $_POST['department'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$validRoles = array('admin', 'manager', 'member');

if ($fullName === '' || $username === '') {
    set_flash('danger', 'Vui lòng nhập họ tên và username.');
    redirect('../users.php');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Email không hợp lệ.');
    redirect('../users.php');
}

if (!in_array($role, $validRoles, true)) {
    $role = 'member';
}

if ($userId === current_user_id() && $role !== 'admin') {
    set_flash('danger', 'Admin đang đăng nhập không thể tự hạ quyền tài khoản của mình.');
    redirect('../users.php');
}

if (username_exists($username, $userId)) {
    set_flash('danger', 'Username đã tồn tại.');
    redirect('../users.php');
}

if ($userId === 0 && strlen($password) < 6) {
    set_flash('danger', 'Tài khoản mới cần mật khẩu tối thiểu 6 ký tự.');
    redirect('../users.php');
}

if ($password !== '' && strlen($password) < 6) {
    set_flash('danger', 'Mật khẩu mới cần tối thiểu 6 ký tự.');
    redirect('../users.php');
}

save_user_by_admin($userId, $fullName, $email !== '' ? $email : null, $username, $role, $department, $password);

if ($userId === current_user_id()) {
    $user = find_user_by_id(current_user_id());
    $_SESSION['user'] = array(
        'id' => $user['id'],
        'full_name' => $user['full_name'],
        'username' => $user['username'],
        'role' => $user['role'],
        'department' => $user['department'],
    );
}

set_flash('success', 'Đã lưu tài khoản và phân quyền.');
redirect('../users.php');
