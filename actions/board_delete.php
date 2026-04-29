<?php
require_once __DIR__ . '/../bootstrap.php';
require_manager();

$boardId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$board = fetch_board_by_id($boardId);

if (!$board) {
    set_flash('danger', 'Khong tim thay board can xoa.');
    redirect('../boards.php');
}

$statement = $pdo->prepare('DELETE FROM boards WHERE id = :id');
$statement->execute(array('id' => $boardId));

set_flash('success', 'Da xoa board thanh cong.');
redirect('../boards.php');
