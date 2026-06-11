<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
require_once __DIR__ . '/user_export_helpers.php';

$filters = get_user_filters($_GET);
$data = build_user_export_data($filters);
$filename = 'danh_sach_tai_khoan_phan_quyen_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Cache-Control: max-age=0');

echo chr(0xEF) . chr(0xBB) . chr(0xBF);
echo render_user_export_html_document('Danh sách tài khoản và phân quyền', $data, true);
exit;
