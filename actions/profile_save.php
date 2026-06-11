<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../profile.php');
}

$fullName = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$department = trim(isset($_POST['department']) ? $_POST['department'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

if ($fullName === '') {
    set_flash('danger', 'Họ tên không được để trống.');
    redirect('../profile.php');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Email không hợp lệ.');
    redirect('../profile.php');
}

if ($password !== '') {
    $passwordError = password_validation_error($password);

    if ($passwordError !== '') {
        set_flash('danger', $passwordError);
        redirect('../profile.php');
    }
}

if ($password !== $passwordConfirm) {
    set_flash('danger', 'Xác nhận mật khẩu không khớp.');
    redirect('../profile.php');
}

update_user_profile(current_user_id(), $fullName, $email !== '' ? $email : null, $department, $password);

$user = find_user_by_id(current_user_id());
$_SESSION['user'] = array(
    'id' => $user['id'],
    'full_name' => $user['full_name'],
    'username' => $user['username'],
    'role' => $user['role'],
    'department' => $user['department'],
);

set_flash('success', 'Đã cập nhật tài khoản.');
redirect('../profile.php');
