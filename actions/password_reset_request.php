<?php
require_once __DIR__ . '/../bootstrap.php';

if (is_logged_in()) {
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../forgot_password.php');
}

$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');

if ($username === '' || $email === '') {
    set_flash('danger', 'Vui lòng nhập đầy đủ username và email.');
    redirect('../forgot_password.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Email không hợp lệ.');
    redirect('../forgot_password.php');
}

$user = find_user_for_password_reset($username, $email);

if (!$user) {
    set_flash('danger', 'Không tìm thấy tài khoản khớp với username và email đã nhập.');
    redirect('../forgot_password.php');
}

$code = (string) random_int(100000, 999999);
$expiresAt = date('Y-m-d H:i:s', time() + 15 * 60);

try {
    create_password_reset_code($user['id'], $code, $expiresAt);
} catch (PDOException $exception) {
    set_flash('danger', 'Chưa cập nhật database cho chức năng quên mật khẩu. Hãy import file database_update_forgot_password.sql.');
    redirect('../forgot_password.php');
}

$_SESSION['password_reset_demo'] = array(
    'username' => $username,
    'email' => $email,
    'code' => $code,
    'expires_at' => $expiresAt,
);

set_flash('success', 'Đã tạo mã xác nhận. Vui lòng dùng mã để đặt mật khẩu mới.');
redirect('../reset_password.php?username=' . rawurlencode($username) . '&email=' . rawurlencode($email));
