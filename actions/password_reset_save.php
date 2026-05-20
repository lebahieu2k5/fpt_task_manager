<?php
require_once __DIR__ . '/../bootstrap.php';

if (is_logged_in()) {
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../reset_password.php');
}

$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$resetCode = trim(isset($_POST['reset_code']) ? $_POST['reset_code'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
$resetUrl = '../reset_password.php?username=' . rawurlencode($username) . '&email=' . rawurlencode($email);

if ($username === '' || $email === '' || $resetCode === '' || $password === '' || $passwordConfirm === '') {
    set_flash('danger', 'Vui lòng nhập đầy đủ thông tin đặt lại mật khẩu.');
    redirect($resetUrl);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Email không hợp lệ.');
    redirect($resetUrl);
}

if (!preg_match('/^[0-9]{6}$/', $resetCode)) {
    set_flash('danger', 'Mã xác nhận cần gồm 6 chữ số.');
    redirect($resetUrl);
}

if (strlen($password) < 6) {
    set_flash('danger', 'Mật khẩu mới cần tối thiểu 6 ký tự.');
    redirect($resetUrl);
}

if ($password !== $passwordConfirm) {
    set_flash('danger', 'Xác nhận mật khẩu không khớp.');
    redirect($resetUrl);
}

$user = find_user_for_password_reset($username, $email);

if (!$user) {
    set_flash('danger', 'Không tìm thấy tài khoản khớp với username và email đã nhập.');
    redirect($resetUrl);
}

try {
    $reset = find_valid_password_reset($user['id'], $resetCode);
} catch (PDOException $exception) {
    set_flash('danger', 'Chưa cập nhật database cho chức năng quên mật khẩu. Hãy import file database_update_forgot_password.sql.');
    redirect($resetUrl);
}

if (!$reset) {
    set_flash('danger', 'Mã xác nhận không đúng hoặc đã hết hạn.');
    redirect($resetUrl);
}

update_user_password($user['id'], $password);
mark_password_reset_used($reset['id']);

if (isset($_SESSION['password_reset_demo'])) {
    unset($_SESSION['password_reset_demo']);
}

set_flash('success', 'Đã đặt lại mật khẩu. Bạn có thể đăng nhập bằng mật khẩu mới.');
redirect('../login.php');
