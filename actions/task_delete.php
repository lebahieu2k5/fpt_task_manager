<?php
require_once __DIR__ . '/../bootstrap.php';
require_manager();

$taskId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$boardId = isset($_GET['board_id']) ? (int) $_GET['board_id'] : 0;
$task = fetch_task_by_id($taskId);

if (!$task) {
    set_flash('danger', 'Không tìm thấy task cần xóa.');
    redirect('../board.php?id=' . $boardId);
}

$statement = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
$statement->execute(array('id' => $taskId));

set_flash('success', 'Đã xóa task thành công.');
redirect('../board.php?id=' . (int) $task['board_id']);
