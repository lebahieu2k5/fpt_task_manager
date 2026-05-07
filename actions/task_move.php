<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('success' => false, 'message' => 'Phương thức không hợp lệ.'));
    exit;
}

$taskId = isset($_POST['task_id']) ? (int) $_POST['task_id'] : 0;
$statusId = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 0;
$task = fetch_task_by_id($taskId);

if (!$task) {
    http_response_code(404);
    echo json_encode(array('success' => false, 'message' => 'Không tìm thấy task.'));
    exit;
}

$board = fetch_board_by_id($task['board_id']);

if (!$board) {
    http_response_code(403);
    echo json_encode(array('success' => false, 'message' => 'Không có quyền truy cập board.'));
    exit;
}

if (!can_edit_task($task)) {
    http_response_code(403);
    echo json_encode(array('success' => false, 'message' => 'Bạn không có quyền di chuyển task này.'));
    exit;
}

$statuses = fetch_statuses();
$selectedStatus = null;

foreach ($statuses as $status) {
    if ((int) $status['id'] === $statusId) {
        $selectedStatus = $status;
        break;
    }
}

if (!$selectedStatus) {
    http_response_code(422);
    echo json_encode(array('success' => false, 'message' => 'Trạng thái không hợp lệ.'));
    exit;
}

$positionStatement = $pdo->prepare(
    'SELECT COALESCE(MAX(position_order), 0) + 1
    FROM tasks
    WHERE board_id = :board_id AND status_id = :status_id'
);
$positionStatement->execute(
    array(
        'board_id' => (int) $task['board_id'],
        'status_id' => $statusId,
    )
);
$nextPosition = (int) $positionStatement->fetchColumn();

$progress = (int) $task['progress_percent'];
if ($selectedStatus['status_key'] === 'done') {
    $progress = 100;
}

$statement = $pdo->prepare(
    'UPDATE tasks
    SET status_id = :status_id, position_order = :position_order, progress_percent = :progress_percent
    WHERE id = :id'
);
$statement->execute(
    array(
        'status_id' => $statusId,
        'position_order' => $nextPosition,
        'progress_percent' => $progress,
        'id' => $taskId,
    )
);

echo json_encode(
    array(
        'success' => true,
        'message' => 'Đã cập nhật trạng thái task.',
    )
);
