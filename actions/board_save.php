<?php
require_once __DIR__ . '/../bootstrap.php';
require_manager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../boards.php');
}

$boardId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$description = trim(isset($_POST['description']) ? $_POST['description'] : '');
$startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
$endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$memberIds = isset($_POST['member_ids']) ? $_POST['member_ids'] : array();

if ($name === '') {
    set_flash('danger', 'Ten board khong duoc de trong.');
    redirect('../boards.php');
}

$memberIds = array_map('intval', $memberIds);
$memberIds = array_values(array_unique($memberIds));

try {
    $pdo->beginTransaction();

    if ($boardId > 0) {
        $board = fetch_board_by_id($boardId);

        if (!$board) {
            throw new Exception('Khong tim thay board can cap nhat.');
        }

        $statement = $pdo->prepare(
            'UPDATE boards
            SET name = :name, description = :description, start_date = :start_date, end_date = :end_date
            WHERE id = :id'
        );
        $statement->execute(
            array(
                'name' => $name,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'id' => $boardId,
            )
        );

        $deleteStatement = $pdo->prepare('DELETE FROM board_members WHERE board_id = :board_id');
        $deleteStatement->execute(array('board_id' => $boardId));

        $savedBoardId = $boardId;
        $flashMessage = 'Cap nhat board thanh cong.';
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO boards (name, description, owner_id, start_date, end_date)
            VALUES (:name, :description, :owner_id, :start_date, :end_date)'
        );
        $statement->execute(
            array(
                'name' => $name,
                'description' => $description,
                'owner_id' => current_user_id(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            )
        );

        $savedBoardId = (int) $pdo->lastInsertId();
        $flashMessage = 'Tao board moi thanh cong.';
    }

    $insertMemberStatement = $pdo->prepare(
        'INSERT INTO board_members (board_id, user_id) VALUES (:board_id, :user_id)'
    );

    foreach ($memberIds as $memberId) {
        if ($memberId === (int) current_user_id()) {
            continue;
        }

        $insertMemberStatement->execute(
            array(
                'board_id' => $savedBoardId,
                'user_id' => $memberId,
            )
        );
    }

    $pdo->commit();
    set_flash('success', $flashMessage);
} catch (Exception $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    set_flash('danger', 'Khong the luu board: ' . $exception->getMessage());
}

redirect('../boards.php');
