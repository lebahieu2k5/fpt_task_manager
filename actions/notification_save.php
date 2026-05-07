<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../notifications.php');
}

$notificationId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$title = trim(isset($_POST['title']) ? $_POST['title'] : '');
$content = trim(isset($_POST['content']) ? $_POST['content'] : '');
$priority = isset($_POST['priority']) ? $_POST['priority'] : 'Trung bình';
$validPriorities = array('Thấp', 'Trung bình', 'Cao');

if ($title === '' || $content === '') {
    set_flash('danger', 'Vui lòng nhập đầy đủ tiêu đề và nội dung thông báo.');
    redirect('../notifications.php');
}

if (!in_array($priority, $validPriorities, true)) {
    $priority = 'Trung bình';
}

if ($notificationId > 0) {
    $notification = fetch_notification_by_id($notificationId);

    if (!$notification) {
        set_flash('danger', 'Không tìm thấy thông báo cần cập nhật.');
        redirect('../notifications.php');
    }

    $statement = $pdo->prepare(
        'UPDATE notifications
        SET title = :title, content = :content, priority = :priority
        WHERE id = :id'
    );
    $statement->execute(
        array(
            'title' => $title,
            'content' => $content,
            'priority' => $priority,
            'id' => $notificationId,
        )
    );

    set_flash('success', 'Đã cập nhật thông báo.');
    redirect('../notifications.php');
}

$statement = $pdo->prepare(
    'INSERT INTO notifications (title, content, priority, created_by)
    VALUES (:title, :content, :priority, :created_by)'
);
$statement->execute(
    array(
        'title' => $title,
        'content' => $content,
        'priority' => $priority,
        'created_by' => current_user_id(),
    )
);

set_flash('success', 'Đã tạo thông báo mới.');
redirect('../notifications.php');
