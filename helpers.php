<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function app_url($path)
{
    $basePath = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';

    if (substr($basePath, -8) === '/actions') {
        $basePath = dirname($basePath);
    }

    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }

    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}

function set_flash($type, $message)
{
    $_SESSION['flash'] = array(
        'type' => $type,
        'message' => $message,
    );
}

function pull_flash()
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token($token)
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function password_validation_error($password)
{
    if (strlen($password) < 6) {
        return 'Mật khẩu phải có tối thiểu 6 ký tự.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return 'Mật khẩu phải có ít nhất 1 chữ cái viết hoa.';
    }

    if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return 'Mật khẩu phải có cả chữ và số.';
    }

    if (preg_match('/\s/', $password)) {
        return 'Mật khẩu không được chứa khoảng trắng.';
    }

    if (!preg_match('/[^a-zA-Z0-9\s]/', $password)) {
        return 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.';
    }

    return '';
}

function current_user()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function current_user_id()
{
    $user = current_user();
    return $user ? (int) $user['id'] : 0;
}

function current_user_role()
{
    $user = current_user();
    return $user ? $user['role'] : null;
}

function is_admin()
{
    return current_user_role() === 'admin';
}

function is_manager()
{
    return current_user_role() === 'manager';
}

function is_member()
{
    return current_user_role() === 'member';
}

function role_label($role)
{
    switch ($role) {
        case 'admin':
            return 'Admin';
        case 'manager':
            return 'Quản lý';
        case 'member':
            return 'Nhân viên';
        default:
            return 'Không xác định';
    }
}

function is_logged_in()
{
    return current_user() !== null;
}

function require_login()
{
    if (!is_logged_in()) {
        set_flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
        redirect(app_url('login.php'));
    }
}

function require_manager()
{
    require_login();

    if (!is_manager()) {
        set_flash('danger', 'Bạn không có quyền thực hiện chức năng này.');
        redirect(app_url('dashboard.php'));
    }
}

function require_admin()
{
    require_login();

    if (!is_admin()) {
        set_flash('danger', 'Chỉ Admin mới có quyền thực hiện chức năng này.');
        redirect(app_url('dashboard.php'));
    }
}

function require_report_access()
{
    require_login();

    if (!is_admin() && !is_manager()) {
        set_flash('danger', 'Bạn không có quyền xem báo cáo.');
        redirect(app_url('dashboard.php'));
    }
}

function can_manage_board($board)
{
    return is_manager() && !empty($board);
}

function can_edit_task($task)
{
    if (is_manager()) {
        return true;
    }

    return is_member() && !empty($task) && (int) $task['assignee_id'] === current_user_id();
}

function priority_badge_class($priority)
{
    switch ($priority) {
        case 'Cao':
            return 'bg-danger-subtle text-danger';
        case 'Trung bình':
            return 'bg-warning-subtle text-warning-emphasis';
        default:
            return 'bg-success-subtle text-success';
    }
}

function progress_bar_class($progress)
{
    if ($progress >= 100) {
        return 'bg-success';
    }

    if ($progress >= 60) {
        return 'bg-primary';
    }

    if ($progress >= 30) {
        return 'bg-warning';
    }

    return 'bg-secondary';
}

function format_date_vn($date)
{
    if (empty($date)) {
        return 'Chưa có';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return $date;
    }

    return date('d/m/Y', $timestamp);
}

function format_datetime_vn($date)
{
    if (empty($date)) {
        return 'Chưa có';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return $date;
    }

    return date('d/m/Y H:i', $timestamp);
}

function google_calendar_url($task)
{
    if (empty($task['deadline'])) {
        return '#';
    }

    $start = date('Ymd', strtotime($task['deadline']));
    $end = date('Ymd', strtotime($task['deadline'] . ' +1 day'));
    $details = 'Board: ' . (isset($task['board_name']) ? $task['board_name'] : '');

    if (!empty($task['assignee_name'])) {
        $details .= "\nNgười thực hiện: " . $task['assignee_name'];
    }

    if (!empty($task['description'])) {
        $details .= "\n" . $task['description'];
    }

    return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . rawurlencode($task['title'])
        . '&dates=' . $start . '/' . $end
        . '&details=' . rawurlencode($details);
}

function task_is_overdue($task)
{
    if (empty($task['deadline']) || empty($task['status_key'])) {
        return false;
    }

    return $task['status_key'] !== 'done' && strtotime($task['deadline']) < strtotime(date('Y-m-d'));
}

function task_status_theme($statusKey)
{
    switch ($statusKey) {
        case 'todo':
            return 'theme-todo';
        case 'doing':
            return 'theme-doing';
        case 'review':
            return 'theme-review';
        case 'done':
            return 'theme-done';
        default:
            return 'theme-todo';
    }
}
