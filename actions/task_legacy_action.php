<?php
require_once __DIR__ . '/../tasks.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'save';

if ($action === 'delete') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id > 0) {
        delete_task($id);
    }
    disconnect_db();
    header('location: ../legacy_crud/list.php');
    exit;
}

// Default action: Save (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $assignee_id = isset($_POST['assignee_id']) ? $_POST['assignee_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'todo';
    $priority = isset($_POST['priority']) ? $_POST['priority'] : 'Trung bình';
    $deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
    $note = isset($_POST['note']) ? $_POST['note'] : '';

    // Validation
    if (trim($project_name) === '' || trim($title) === '') {
        die('Dữ liệu không hợp lệ');
    }

    // Deadline validation
    if (!empty($deadline)) {
        $today = date('Y-m-d');
        if ($deadline < $today) {
            die('Hạn chót không được nhỏ hơn ngày hiện tại');
        }
    }

    if ($id > 0) {
        edit_task($id, $project_name, $title, $description, $assignee_id, $status, $priority, $deadline, 0, $note);
    } else {
        add_task($project_name, $title, $description, $assignee_id, $status, $priority, $deadline, 0, $note);
    }

    disconnect_db();
    header('location: ../legacy_crud/list.php');
    exit;
}

header('location: ../legacy_crud/list.php');
exit;
