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

// if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     set_flash('danger', 'Email không hợp lệ.');
//     redirect('../users.php');
// }

if($email != ''){
    if (strlen($email) != strlen(trim($email))) {
        set_flash('danger', 'Email khong duoc chua dau cach o dau hoac cuoi');
        redirect('../users.php');
    }
    if (preg_match('/\s+@|@\s+/', $email)) {
        set_flash('danger', 'Email khong duoc co dau cach truoc hoac sau ki tu @.');
        redirect('../users.php');
    }
    if (preg_match('/\s/', $email)) {
        set_flash('danger', 'Email khong duoc chua khoang trang.');
        redirect('../users.php');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'dinh dang email khong hop le.');
        redirect('../users.php');
    }
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
if (preg_match('/[^a-zA-Z]/', $username)) {
    set_flash('danger', 'Username chỉ nhận chữ.');
    redirect('../users.php');
}



if ($userId === 0 && $password === '') {
    set_flash('danger', 'Tài khoản mới bắt buộc phải có mật khẩu.');
    redirect('../users.php');
}

if ($password !== '') {
    $passwordError = password_validation_error($password);

    if ($passwordError !== '') {
        set_flash('danger', $passwordError);
        redirect('../users.php');
    }
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
