<?php
require_once __DIR__ . '/../bootstrap.php';
require_admin();
require_once __DIR__ . '/user_export_helpers.php';

if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
    set_flash('danger', 'Máy chủ chưa bật thư viện GD nên chưa thể xuất PDF.');
    redirect('../users.php');
}

$fontPath = user_export_pdf_find_font();

if ($fontPath === '') {
    set_flash('danger', 'Không tìm thấy font chữ hỗ trợ tiếng Việt để xuất PDF.');
    redirect('../users.php');
}

$filters = get_user_filters($_GET);
$data = build_user_export_data($filters);
$pages = user_export_pdf_build_pages($data, $fontPath);
$pdf = user_export_pdf_build_document($pages);

foreach ($pages as $page) {
    imagedestroy($page);
}

$filename = 'danh_sach_tai_khoan_phan_quyen_' . date('Ymd_His') . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Length: ' . strlen($pdf));
header('Cache-Control: max-age=0');

echo $pdf;
exit;
