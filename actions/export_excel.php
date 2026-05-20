<?php
require_once __DIR__ . '/../bootstrap.php';
require_report_access();
require_once __DIR__ . '/report_export_helpers.php';

$data = build_export_report_data();
$filename = 'bao_cao_tong_hop_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Cache-Control: max-age=0');

echo chr(0xEF) . chr(0xBB) . chr(0xBF);
echo render_report_excel_document('Báo cáo tổng hợp công việc', $data);
exit;
