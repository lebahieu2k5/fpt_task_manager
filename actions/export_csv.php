<?php
require_once __DIR__ . '/../bootstrap.php';
require_report_access();

$assigneeReport = fetch_assignee_report();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bao_cao_nhan_su_' . date('Ymd') . '.csv');

$output = fopen('php://output', 'w');

// Fix UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, array('Nhân sự', 'Tổng số task', 'Đã hoàn thành', 'Trễ hạn'));

foreach ($assigneeReport as $row) {
    fputcsv($output, array(
        $row['assignee_name'],
        (int) $row['task_count'],
        (int) $row['done_count'],
        (int) $row['overdue_count']
    ));
}

fclose($output);
exit;
