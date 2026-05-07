<?php
require_once __DIR__ . '/../bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../boards.php');
}

function clamp_progress_value($value)
{
    $value = (int) $value;

    if ($value < 0) {
        return 0;
    }

    if ($value > 100) {
        return 100;
    }

    return $value;
}

function resolve_status_by_id($statusId, $statuses)
{
    foreach ($statuses as $status) {
        if ((int) $status['id'] === (int) $statusId) {
            return $status;
        }
    }

    return null;
}

$taskId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$boardId = isset($_POST['board_id']) ? (int) $_POST['board_id'] : 0;
$board = fetch_board_by_id($boardId);

if (!$board) {
    set_flash('danger', 'Không tìm thấy board để lưu task.');
    redirect('../boards.php');
}

$boardMembers = fetch_board_member_ids($boardId);
$statuses = fetch_statuses();
$validStatusIds = fetch_board_status_ids();
$selectedStatusId = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 0;

if (!in_array($selectedStatusId, $validStatusIds, true)) {
    $selectedStatusId = (int) $statuses[0]['id'];
}

$selectedStatus = resolve_status_by_id($selectedStatusId, $statuses);
$progressPercent = clamp_progress_value(isset($_POST['progress_percent']) ? $_POST['progress_percent'] : 0);

if ($selectedStatus && $selectedStatus['status_key'] === 'done') {
    $progressPercent = 100;
}

if ($taskId > 0) {
    $task = fetch_task_by_id($taskId);

    if (!$task || (int) $task['board_id'] !== $boardId) {
        set_flash('danger', 'Không tìm thấy task cần cập nhật.');
        redirect('../board.php?id=' . $boardId);
    }

    if (!can_edit_task($task)) {
        set_flash('danger', 'Bạn không có quyền cập nhật task này.');
        redirect('../board.php?id=' . $boardId);
    }

    if (is_manager()) {
        $title = trim(isset($_POST['title']) ? $_POST['title'] : '');

        if ($title === '') {
            set_flash('danger', 'Tên task không được để trống.');
            redirect('../board.php?id=' . $boardId);
        }

        $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $priority = isset($_POST['priority']) ? $_POST['priority'] : 'Trung bình';
        $assigneeId = !empty($_POST['assignee_id']) ? (int) $_POST['assignee_id'] : null;
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
        $note = trim(isset($_POST['note']) ? $_POST['note'] : '');

        if ($assigneeId !== null && !in_array($assigneeId, $boardMembers, true)) {
            set_flash('danger', 'Người được giao phải nằm trong board.');
            redirect('../board.php?id=' . $boardId);
        }

        $statement = $pdo->prepare(
            'UPDATE tasks
            SET title = :title,
                description = :description,
                assignee_id = :assignee_id,
                status_id = :status_id,
                priority = :priority,
                deadline = :deadline,
                progress_percent = :progress_percent,
                note = :note
            WHERE id = :id'
        );

        $statement->execute(
            array(
                'title' => $title,
                'description' => $description,
                'assignee_id' => $assigneeId,
                'status_id' => $selectedStatusId,
                'priority' => $priority,
                'deadline' => $deadline,
                'progress_percent' => $progressPercent,
                'note' => $note,
                'id' => $taskId,
            )
        );
    } else {
        $note = trim(isset($_POST['note']) ? $_POST['note'] : '');

        $statement = $pdo->prepare(
            'UPDATE tasks
            SET status_id = :status_id,
                progress_percent = :progress_percent,
                note = :note
            WHERE id = :id'
        );

        $statement->execute(
            array(
                'status_id' => $selectedStatusId,
                'progress_percent' => $progressPercent,
                'note' => $note,
                'id' => $taskId,
            )
        );
    }

    set_flash('success', 'Cập nhật task thành công.');
    redirect('../board.php?id=' . $boardId);
}

require_manager();

$title = trim(isset($_POST['title']) ? $_POST['title'] : '');
$description = trim(isset($_POST['description']) ? $_POST['description'] : '');
$priority = isset($_POST['priority']) ? $_POST['priority'] : 'Trung bình';
$assigneeId = !empty($_POST['assignee_id']) ? (int) $_POST['assignee_id'] : null;
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
$note = trim(isset($_POST['note']) ? $_POST['note'] : '');

if ($title === '') {
    set_flash('danger', 'Tên task không được để trống.');
    redirect('../board.php?id=' . $boardId);
}

if ($assigneeId !== null && !in_array($assigneeId, $boardMembers, true)) {
    set_flash('danger', 'Người được giao phải nằm trong board.');
    redirect('../board.php?id=' . $boardId);
}

$positionStatement = $pdo->prepare(
    'SELECT COALESCE(MAX(position_order), 0) + 1 AS next_position
    FROM tasks
    WHERE board_id = :board_id AND status_id = :status_id'
);
$positionStatement->execute(
    array(
        'board_id' => $boardId,
        'status_id' => $selectedStatusId,
    )
);
$positionOrder = (int) $positionStatement->fetchColumn();

$statement = $pdo->prepare(
    'INSERT INTO tasks
    (board_id, status_id, title, description, priority, assignee_id, deadline, progress_percent, note, position_order, created_by)
    VALUES
    (:board_id, :status_id, :title, :description, :priority, :assignee_id, :deadline, :progress_percent, :note, :position_order, :created_by)'
);

$statement->execute(
    array(
        'board_id' => $boardId,
        'status_id' => $selectedStatusId,
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'assignee_id' => $assigneeId,
        'deadline' => $deadline,
        'progress_percent' => $progressPercent,
        'note' => $note,
        'position_order' => $positionOrder,
        'created_by' => current_user_id(),
    )
);

set_flash('success', 'Đã tạo task mới thành công.');
redirect('../board.php?id=' . $boardId);
