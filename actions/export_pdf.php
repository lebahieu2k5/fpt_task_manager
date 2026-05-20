<?php
require_once __DIR__ . '/../bootstrap.php';
require_report_access();
require_once __DIR__ . '/report_export_helpers.php';

if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
    set_flash('danger', 'Máy chủ chưa bật thư viện GD nên chưa thể xuất PDF.');
    redirect('../reports.php');
}

$fontPath = report_pdf_find_font();

if ($fontPath === '') {
    set_flash('danger', 'Không tìm thấy font chữ hỗ trợ tiếng Việt để xuất PDF.');
    redirect('../reports.php');
}

$data = build_export_report_data();
$pages = report_pdf_build_pages($data, $fontPath);
$pdf = report_pdf_build_document($pages);

foreach ($pages as $page) {
    imagedestroy($page);
}

$filename = 'bao_cao_tong_hop_' . date('Ymd_His') . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Length: ' . strlen($pdf));
header('Cache-Control: max-age=0');

echo $pdf;
exit;

function report_pdf_find_font()
{
    $candidates = array(
        __DIR__ . '/../assets/fonts/DejaVuSans.ttf',
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/segoeui.ttf',
        'C:/Windows/Fonts/tahoma.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
    );

    foreach ($candidates as $font) {
        if (is_file($font)) {
            return $font;
        }
    }

    return '';
}

function report_pdf_new_page(&$colors)
{
    $image = imagecreatetruecolor(1240, 1754);

    if (function_exists('imageantialias')) {
        imageantialias($image, true);
    }

    $colors = array(
        'white' => imagecolorallocate($image, 255, 255, 255),
        'ink' => imagecolorallocate($image, 17, 24, 39),
        'muted' => imagecolorallocate($image, 100, 116, 139),
        'line' => imagecolorallocate($image, 203, 213, 225),
        'blue' => imagecolorallocate($image, 16, 104, 176),
        'soft' => imagecolorallocate($image, 248, 250, 252),
        'softBlue' => imagecolorallocate($image, 224, 242, 254),
    );

    imagefilledrectangle($image, 0, 0, 1240, 1754, $colors['white']);

    return $image;
}

function report_pdf_build_pages($data, $font)
{
    $pages = array();
    $colors = array();
    $image = report_pdf_new_page($colors);
    $y = 86;

    report_pdf_draw_centered_text($image, $font, 26, $y, $colors['blue'], 'BÁO CÁO TỔNG HỢP CÔNG VIỆC');
    $y += 38;
    report_pdf_draw_centered_text($image, $font, 15, $y, $colors['muted'], 'Ngày xuất báo cáo: ' . $data['generated_at']);
    $y += 46;

    $stats = $data['stats'];
    report_pdf_draw_section_title($pages, $image, $colors, $y, $font, 'Tổng quan');
    report_pdf_draw_table(
        $pages,
        $image,
        $colors,
        $y,
        $font,
        array('Bảng', 'Tổng task', 'Hoàn thành', 'Tỉ lệ', 'Trễ hạn'),
        array(array(
            $stats['board_count'],
            $stats['task_count'],
            $stats['done_count'],
            $stats['completion_rate'] . '%',
            $stats['overdue_count'],
        )),
        array(180, 220, 240, 220, 240)
    );

    $statusRows = array();
    foreach ($data['status_report'] as $row) {
        $percent = $stats['task_count'] > 0 ? round(((int) $row['task_count'] / $stats['task_count']) * 100) : 0;
        $statusRows[] = array($row['status_name'], (int) $row['task_count'], $percent . '%');
    }
    report_pdf_draw_section_title($pages, $image, $colors, $y, $font, 'Thống kê trạng thái');
    report_pdf_draw_table(
        $pages,
        $image,
        $colors,
        $y,
        $font,
        array('Trạng thái', 'Số task', 'Tỉ lệ'),
        report_pdf_rows_or_empty($statusRows, 3),
        array(620, 240, 240)
    );

    $boardRows = array();
    foreach ($data['boards'] as $board) {
        $progress = (int) $board['task_count'] > 0 ? round(((int) $board['done_count'] / (int) $board['task_count']) * 100) : 0;
        $boardRows[] = array(
            $board['name'],
            (int) $board['member_count'],
            (int) $board['task_count'],
            (int) $board['done_count'],
            $progress . '%',
        );
    }
    report_pdf_draw_section_title($pages, $image, $colors, $y, $font, 'Tiến độ theo bảng');
    report_pdf_draw_table(
        $pages,
        $image,
        $colors,
        $y,
        $font,
        array('Bảng', 'Thành viên', 'Task', 'Đã xong', 'Hoàn thành'),
        report_pdf_rows_or_empty($boardRows, 5),
        array(470, 170, 140, 160, 160)
    );

    $assigneeRows = array();
    foreach ($data['assignee_report'] as $row) {
        $assigneeRows[] = array(
            $row['assignee_name'],
            (int) $row['task_count'],
            (int) $row['done_count'],
            (int) $row['overdue_count'],
        );
    }
    report_pdf_draw_section_title($pages, $image, $colors, $y, $font, 'Báo cáo theo nhân sự');
    report_pdf_draw_table(
        $pages,
        $image,
        $colors,
        $y,
        $font,
        array('Nhân sự', 'Tổng task', 'Đã hoàn thành', 'Trễ hạn'),
        report_pdf_rows_or_empty($assigneeRows, 4),
        array(560, 180, 220, 140)
    );

    $pages[] = $image;
    report_pdf_draw_footers($pages, $font);

    return $pages;
}

function report_pdf_rows_or_empty($rows, $columnCount)
{
    if (!empty($rows)) {
        return $rows;
    }

    $empty = array('Chưa có dữ liệu.');
    for ($i = 1; $i < $columnCount; $i++) {
        $empty[] = '';
    }

    return array($empty);
}

function report_pdf_draw_section_title(&$pages, &$image, &$colors, &$y, $font, $title)
{
    report_pdf_ensure_space(82, $image, $colors, $y, $font, $pages);
    $y += 18;
    imagettftext($image, 18, 0, 70, $y, $colors['blue'], $font, $title);
    $y += 18;
}

function report_pdf_draw_table(&$pages, &$image, &$colors, &$y, $font, $headers, $rows, $widths)
{
    $rowHeight = 46;
    $headerHeight = 48;
    $x = 70;

    report_pdf_ensure_space($headerHeight + $rowHeight, $image, $colors, $y, $font, $pages);
    report_pdf_draw_table_header($image, $font, $colors, $x, $y, $headers, $widths, $headerHeight);
    $y += $headerHeight;

    foreach ($rows as $index => $row) {
        if ($y + $rowHeight > 1645) {
            $pages[] = $image;
            $image = report_pdf_new_page($colors);
            $y = 80;
            report_pdf_draw_table_header($image, $font, $colors, $x, $y, $headers, $widths, $headerHeight);
            $y += $headerHeight;
        }

        $fillColor = $index % 2 === 0 ? $colors['white'] : $colors['soft'];
        $cellX = $x;

        foreach ($headers as $cellIndex => $header) {
            $width = $widths[$cellIndex];
            imagefilledrectangle($image, $cellX, $y, $cellX + $width, $y + $rowHeight, $fillColor);
            imagerectangle($image, $cellX, $y, $cellX + $width, $y + $rowHeight, $colors['line']);

            $text = isset($row[$cellIndex]) ? (string) $row[$cellIndex] : '';
            $text = report_pdf_fit_text($font, 13, $text, $width - 18);
            imagettftext($image, 13, 0, $cellX + 9, $y + 29, $colors['ink'], $font, $text);

            $cellX += $width;
        }

        $y += $rowHeight;
    }

    $y += 16;
}

function report_pdf_draw_table_header(&$image, $font, $colors, $x, $y, $headers, $widths, $height)
{
    $cellX = $x;

    foreach ($headers as $index => $header) {
        $width = $widths[$index];
        imagefilledrectangle($image, $cellX, $y, $cellX + $width, $y + $height, $colors['blue']);
        imagerectangle($image, $cellX, $y, $cellX + $width, $y + $height, $colors['blue']);
        $text = report_pdf_fit_text($font, 13, $header, $width - 18);
        imagettftext($image, 13, 0, $cellX + 9, $y + 30, $colors['white'], $font, $text);
        $cellX += $width;
    }
}

function report_pdf_ensure_space($height, &$image, &$colors, &$y, $font, &$pages)
{
    if ($y + $height <= 1645) {
        return;
    }

    $pages[] = $image;
    $image = report_pdf_new_page($colors);
    $y = 80;
}

function report_pdf_draw_centered_text(&$image, $font, $size, $y, $color, $text)
{
    $width = report_pdf_text_width($font, $size, $text);
    $x = (int) round((1240 - $width) / 2);
    imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
}

function report_pdf_fit_text($font, $size, $text, $maxWidth)
{
    $text = (string) $text;

    if (report_pdf_text_width($font, $size, $text) <= $maxWidth) {
        return $text;
    }

    $suffix = '...';
    $length = mb_strlen($text, 'UTF-8');

    while ($length > 0) {
        $candidate = mb_substr($text, 0, $length, 'UTF-8') . $suffix;

        if (report_pdf_text_width($font, $size, $candidate) <= $maxWidth) {
            return $candidate;
        }

        $length--;
    }

    return $suffix;
}

function report_pdf_text_width($font, $size, $text)
{
    $box = imagettfbbox($size, 0, $font, (string) $text);

    return abs($box[2] - $box[0]);
}

function report_pdf_draw_footers(&$pages, $font)
{
    $total = count($pages);

    foreach ($pages as $index => $page) {
        $line = imagecolorallocate($page, 226, 232, 240);
        $muted = imagecolorallocate($page, 100, 116, 139);
        imageline($page, 70, 1680, 1170, 1680, $line);
        imagettftext($page, 12, 0, 70, 1718, $muted, $font, 'FPT Workflow');
        imagettftext($page, 12, 0, 1080, 1718, $muted, $font, 'Trang ' . ($index + 1) . '/' . $total);
    }
}

function report_pdf_build_document($pages)
{
    $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    $offsets = array();
    $pageRefs = array();
    $objectId = 3;

    report_pdf_add_object($pdf, $offsets, 1, '<< /Type /Catalog /Pages 2 0 R >>');

    foreach ($pages as $index => $page) {
        $pageId = $objectId++;
        $contentId = $objectId++;
        $imageId = $objectId++;
        $imageName = 'Im' . ($index + 1);
        $pageRefs[] = $pageId . ' 0 R';

        $content = "q\n595.28 0 0 841.89 0 0 cm\n/" . $imageName . " Do\nQ\n";
        ob_start();
        imagejpeg($page, null, 92);
        $jpeg = ob_get_clean();

        report_pdf_add_object(
            $pdf,
            $offsets,
            $pageId,
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /XObject << /' . $imageName . ' ' . $imageId . ' 0 R >> /ProcSet [/PDF /ImageC] >> /Contents ' . $contentId . ' 0 R >>'
        );
        report_pdf_add_object(
            $pdf,
            $offsets,
            $contentId,
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "endstream"
        );
        report_pdf_add_object(
            $pdf,
            $offsets,
            $imageId,
            '<< /Type /XObject /Subtype /Image /Width ' . imagesx($page) . ' /Height ' . imagesy($page) . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($jpeg) . " >>\nstream\n" . $jpeg . "\nendstream"
        );
    }

    $pagesObject = '<< /Type /Pages /Count ' . count($pages) . ' /Kids [' . implode(' ', $pageRefs) . '] >>';
    report_pdf_add_object($pdf, $offsets, 2, $pagesObject);

    $maxObjectId = $objectId - 1;
    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . ($maxObjectId + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";

    for ($i = 1; $i <= $maxObjectId; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer\n<< /Size " . ($maxObjectId + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

    return $pdf;
}

function report_pdf_add_object(&$pdf, &$offsets, $id, $body)
{
    $offsets[$id] = strlen($pdf);
    $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
}
