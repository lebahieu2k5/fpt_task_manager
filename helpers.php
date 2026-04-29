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

function is_logged_in()
{
    return current_user() !== null;
}

function require_login()
{
    if (!is_logged_in()) {
        set_flash('warning', 'Vui long dang nhap de tiep tuc.');
        redirect('login.php');
    }
}

function require_manager()
{
    require_login();

    if (current_user_role() !== 'manager') {
        set_flash('danger', 'Ban khong co quyen thuc hien chuc nang nay.');
        redirect('dashboard.php');
    }
}

function can_manage_board($board)
{
    return current_user_role() === 'manager' && !empty($board);
}

function can_edit_task($task)
{
    if (current_user_role() === 'manager') {
        return true;
    }

    return !empty($task) && (int) $task['assignee_id'] === current_user_id();
}

function priority_badge_class($priority)
{
    switch ($priority) {
        case 'Cao':
            return 'bg-danger-subtle text-danger';
        case 'Trung binh':
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
        return 'Chua co';
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
        return 'Chua co';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return $date;
    }

    return date('d/m/Y H:i', $timestamp);
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
