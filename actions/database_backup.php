<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
require_once __DIR__ . '/database_tools.php';

$errorMessage = '';
$backupFile = database_create_backup_file($errorMessage);

if ($backupFile === '') {
    set_flash('danger', 'Không thể sao lưu database: ' . $errorMessage);
    redirect('../data_management.php');
}

$filename = 'fpt_task_manager_backup_' . date('Ymd_His') . '.sql';

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Length: ' . filesize($backupFile));
header('Cache-Control: no-store, no-cache, must-revalidate');

readfile($backupFile);
@unlink($backupFile);
exit;
