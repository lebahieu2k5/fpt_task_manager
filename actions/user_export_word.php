<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
require_once __DIR__ . '/user_export_helpers.php';

$filters = get_user_filters($_GET);
$data = build_user_export_data($filters);
$filename = 'danh_sach_tai_khoan_phan_quyen_' . date('Ymd_His') . '.doc';

header('Content-Type: application/vnd.ms-word; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Cache-Control: max-age=0');

echo render_user_export_html_document('Danh sách tài khoản và phân quyền', $data);
exit;
