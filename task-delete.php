<?php
require 'tasks.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$back = isset($_GET['back']) && $_GET['back'] === 'board' ? 'board.php' : 'task-list.php';

if ($id > 0) {
    delete_task($id);
}

disconnect_db();

header('location: ' . $back);
exit;
