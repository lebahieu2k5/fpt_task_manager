<?php

function get_user_filters($source)
{
    $filters = array(
        'full_name' => isset($source['full_name']) ? trim((string) $source['full_name']) : '',
        'username' => isset($source['username']) ? trim((string) $source['username']) : '',
        'email' => isset($source['email']) ? trim((string) $source['email']) : '',
        'department' => isset($source['department']) ? trim((string) $source['department']) : '',
        'role' => isset($source['role']) ? trim((string) $source['role']) : '',
        'created_from' => isset($source['created_from']) ? trim((string) $source['created_from']) : '',
        'created_to' => isset($source['created_to']) ? trim((string) $source['created_to']) : '',
    );

    if (!in_array($filters['role'], array('', 'admin', 'manager', 'member'), true)) {
        $filters['role'] = '';
    }

    foreach (array('created_from', 'created_to') as $dateKey) {
        if ($filters[$dateKey] !== '' && !user_filter_valid_date($filters[$dateKey])) {
            $filters[$dateKey] = '';
        }
    }

    return $filters;
}

function user_filter_valid_date($value)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return false;
    }

    $date = DateTime::createFromFormat('!Y-m-d', $value);

    return $date && $date->format('Y-m-d') === $value;
}

function user_filter_query($filters)
{
    $active = array_filter($filters, function ($value) {
        return $value !== '';
    });

    return http_build_query($active);
}

function user_filter_summary($filters)
{
    $labels = array();
    $mapping = array(
        'full_name' => 'Họ tên',
        'username' => 'Username',
        'email' => 'Email',
        'department' => 'Bộ phận',
        'role' => 'Vai trò',
        'created_from' => 'Tạo từ ngày',
        'created_to' => 'Tạo đến ngày',
    );

    foreach ($mapping as $key => $label) {
        if (empty($filters[$key])) {
            continue;
        }

        $value = $filters[$key];
        if ($key === 'role') {
            $value = role_label($value);
        } elseif ($key === 'created_from' || $key === 'created_to') {
            $value = format_date_vn($value);
        }

        $labels[] = $label . ': ' . $value;
    }

    return empty($labels) ? 'Không áp dụng bộ lọc' : implode(' | ', $labels);
}

function build_user_export_data($filters = array())
{
    $users = fetch_all_users($filters);
    $roleCounts = array(
        'admin' => 0,
        'manager' => 0,
        'member' => 0,
    );

    foreach ($users as $user) {
        if (isset($roleCounts[$user['role']])) {
            $roleCounts[$user['role']]++;
        }
    }

    $currentUser = current_user();

    return array(
        'generated_at' => date('d/m/Y H:i:s'),
        'generated_by' => $currentUser ? $currentUser['full_name'] . ' (' . $currentUser['username'] . ')' : '',
        'users' => $users,
        'total' => count($users),
        'role_counts' => $roleCounts,
        'filter_summary' => user_filter_summary($filters),
    );
}

function user_export_value($value, $emptyText = 'Chưa cập nhật')
{
    $value = trim((string) $value);

    return $value !== '' ? $value : $emptyText;
}

function render_user_export_html_document($title, $data, $excel = false)
{
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="vi"<?php echo $excel ? ' xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"' : ''; ?>>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <?php if ($excel) { ?>
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Danh sách tài khoản</x:Name>
                        <x:WorksheetOptions><x:FreezePanes/><x:FrozenNoSplit/><x:SplitHorizontal>5</x:SplitHorizontal><x:TopRowBottomPane>5</x:TopRowBottomPane></x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
    <?php } ?>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        h1 { color: #1068b0; font-size: 22px; margin: 0 0 8px; text-align: center; text-transform: uppercase; }
        p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #1068b0; color: #ffffff; font-weight: bold; }
        .summary { margin-top: 12px; }
        .summary td { background: #f8fafc; font-weight: bold; text-align: center; }
        .number { text-align: center; }
        .text { mso-number-format: "\@"; }
        .muted { color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <h1><?php echo e($title); ?></h1>
    <p><strong>Thời gian xuất:</strong> <?php echo e($data['generated_at']); ?></p>
    <p><strong>Người xuất:</strong> <?php echo e($data['generated_by']); ?></p>
    <p><strong>Điều kiện lọc:</strong> <?php echo e($data['filter_summary']); ?></p>

    <table class="summary">
        <tr>
            <td>Tổng tài khoản: <?php echo e($data['total']); ?></td>
            <td>Admin: <?php echo e($data['role_counts']['admin']); ?></td>
            <td>Quản lý: <?php echo e($data['role_counts']['manager']); ?></td>
            <td>Nhân viên: <?php echo e($data['role_counts']['member']); ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Username</th>
                <th>Email</th>
                <th>Bộ phận</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['users'] as $index => $user) { ?>
                <tr>
                    <td class="number"><?php echo e($index + 1); ?></td>
                    <td><?php echo e(user_export_value($user['full_name'])); ?></td>
                    <td class="text"><?php echo e(user_export_value($user['username'])); ?></td>
                    <td class="text"><?php echo e(user_export_value($user['email'])); ?></td>
                    <td><?php echo e(user_export_value($user['department'])); ?></td>
                    <td><?php echo e(role_label($user['role'])); ?></td>
                    <td><?php echo e(format_datetime_vn($user['created_at'])); ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($data['users'])) { ?>
                <tr><td colspan="7" class="muted">Chưa có tài khoản nào.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
    <?php
    return ob_get_clean();
}

function user_export_pdf_find_font()
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

function user_export_pdf_new_page(&$colors)
{
    $image = imagecreatetruecolor(1754, 1240);

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
    );
    imagefilledrectangle($image, 0, 0, 1754, 1240, $colors['white']);

    return $image;
}

function user_export_pdf_build_pages($data, $font)
{
    $pages = array();
    $colors = array();
    $image = user_export_pdf_new_page($colors);
    $y = 70;

    user_export_pdf_center_text($image, $font, 24, $y, $colors['blue'], 'DANH SÁCH TÀI KHOẢN VÀ PHÂN QUYỀN');
    $y += 36;
    user_export_pdf_center_text($image, $font, 12, $y, $colors['muted'], 'Thời gian xuất: ' . $data['generated_at'] . ' | Người xuất: ' . $data['generated_by']);
    $y += 28;
    $filterSummary = user_export_pdf_fit_text($font, 11, 'Bộ lọc: ' . $data['filter_summary'], 1600);
    user_export_pdf_center_text($image, $font, 11, $y, $colors['muted'], $filterSummary);
    $y += 30;
    user_export_pdf_center_text(
        $image,
        $font,
        12,
        $y,
        $colors['ink'],
        'Tổng: ' . $data['total'] . ' | Admin: ' . $data['role_counts']['admin'] . ' | Quản lý: ' . $data['role_counts']['manager'] . ' | Nhân viên: ' . $data['role_counts']['member']
    );
    $y += 28;

    $headers = array('STT', 'Họ tên', 'Username', 'Email', 'Bộ phận', 'Vai trò', 'Ngày tạo');
    $widths = array(60, 285, 185, 300, 280, 150, 230);
    $rows = array();

    foreach ($data['users'] as $index => $user) {
        $rows[] = array(
            $index + 1,
            user_export_value($user['full_name']),
            user_export_value($user['username']),
            user_export_value($user['email']),
            user_export_value($user['department']),
            role_label($user['role']),
            format_datetime_vn($user['created_at']),
        );
    }

    if (empty($rows)) {
        $rows[] = array('', 'Chưa có tài khoản nào.', '', '', '', '', '');
    }

    user_export_pdf_draw_table($pages, $image, $colors, $y, $font, $headers, $rows, $widths);
    $pages[] = $image;
    user_export_pdf_draw_footers($pages, $font);

    return $pages;
}

function user_export_pdf_draw_table(&$pages, &$image, &$colors, &$y, $font, $headers, $rows, $widths)
{
    $x = 62;
    $headerHeight = 48;
    $rowHeight = 46;
    user_export_pdf_draw_header($image, $font, $colors, $x, $y, $headers, $widths, $headerHeight);
    $y += $headerHeight;

    foreach ($rows as $rowIndex => $row) {
        if ($y + $rowHeight > 1140) {
            $pages[] = $image;
            $image = user_export_pdf_new_page($colors);
            $y = 64;
            user_export_pdf_draw_header($image, $font, $colors, $x, $y, $headers, $widths, $headerHeight);
            $y += $headerHeight;
        }

        $cellX = $x;
        $fill = $rowIndex % 2 === 0 ? $colors['white'] : $colors['soft'];

        foreach ($headers as $cellIndex => $header) {
            $width = $widths[$cellIndex];
            imagefilledrectangle($image, $cellX, $y, $cellX + $width, $y + $rowHeight, $fill);
            imagerectangle($image, $cellX, $y, $cellX + $width, $y + $rowHeight, $colors['line']);
            $text = isset($row[$cellIndex]) ? (string) $row[$cellIndex] : '';
            $text = user_export_pdf_fit_text($font, 11, $text, $width - 16);
            imagettftext($image, 11, 0, $cellX + 8, $y + 29, $colors['ink'], $font, $text);
            $cellX += $width;
        }

        $y += $rowHeight;
    }
}

function user_export_pdf_draw_header(&$image, $font, $colors, $x, $y, $headers, $widths, $height)
{
    $cellX = $x;

    foreach ($headers as $index => $header) {
        $width = $widths[$index];
        imagefilledrectangle($image, $cellX, $y, $cellX + $width, $y + $height, $colors['blue']);
        imagerectangle($image, $cellX, $y, $cellX + $width, $y + $height, $colors['blue']);
        $text = user_export_pdf_fit_text($font, 11, $header, $width - 16);
        imagettftext($image, 11, 0, $cellX + 8, $y + 30, $colors['white'], $font, $text);
        $cellX += $width;
    }
}

function user_export_pdf_center_text(&$image, $font, $size, $y, $color, $text)
{
    $width = user_export_pdf_text_width($font, $size, $text);
    imagettftext($image, $size, 0, (int) round((1754 - $width) / 2), $y, $color, $font, $text);
}

function user_export_pdf_fit_text($font, $size, $text, $maxWidth)
{
    $text = (string) $text;

    if (user_export_pdf_text_width($font, $size, $text) <= $maxWidth) {
        return $text;
    }

    $suffix = '...';
    $length = mb_strlen($text, 'UTF-8');

    while ($length > 0) {
        $candidate = mb_substr($text, 0, $length, 'UTF-8') . $suffix;

        if (user_export_pdf_text_width($font, $size, $candidate) <= $maxWidth) {
            return $candidate;
        }

        $length--;
    }

    return $suffix;
}

function user_export_pdf_text_width($font, $size, $text)
{
    $box = imagettfbbox($size, 0, $font, (string) $text);

    return abs($box[2] - $box[0]);
}

function user_export_pdf_draw_footers(&$pages, $font)
{
    $total = count($pages);

    foreach ($pages as $index => $page) {
        $line = imagecolorallocate($page, 226, 232, 240);
        $muted = imagecolorallocate($page, 100, 116, 139);
        imageline($page, 62, 1172, 1692, 1172, $line);
        imagettftext($page, 10, 0, 62, 1205, $muted, $font, 'FPT Workflow - Danh sách tài khoản và phân quyền');
        imagettftext($page, 10, 0, 1590, 1205, $muted, $font, 'Trang ' . ($index + 1) . '/' . $total);
    }
}

function user_export_pdf_build_document($pages)
{
    $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    $offsets = array();
    $pageRefs = array();
    $objectId = 3;
    user_export_pdf_add_object($pdf, $offsets, 1, '<< /Type /Catalog /Pages 2 0 R >>');

    foreach ($pages as $index => $page) {
        $pageId = $objectId++;
        $contentId = $objectId++;
        $imageId = $objectId++;
        $imageName = 'Im' . ($index + 1);
        $pageRefs[] = $pageId . ' 0 R';
        $content = "q\n841.89 0 0 595.28 0 0 cm\n/" . $imageName . " Do\nQ\n";
        ob_start();
        imagejpeg($page, null, 92);
        $jpeg = ob_get_clean();

        user_export_pdf_add_object($pdf, $offsets, $pageId, '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 841.89 595.28] /Resources << /XObject << /' . $imageName . ' ' . $imageId . ' 0 R >> /ProcSet [/PDF /ImageC] >> /Contents ' . $contentId . ' 0 R >>');
        user_export_pdf_add_object($pdf, $offsets, $contentId, '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "endstream");
        user_export_pdf_add_object($pdf, $offsets, $imageId, '<< /Type /XObject /Subtype /Image /Width ' . imagesx($page) . ' /Height ' . imagesy($page) . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($jpeg) . " >>\nstream\n" . $jpeg . "\nendstream");
    }

    user_export_pdf_add_object($pdf, $offsets, 2, '<< /Type /Pages /Count ' . count($pages) . ' /Kids [' . implode(' ', $pageRefs) . '] >>');
    $maxObjectId = $objectId - 1;
    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . ($maxObjectId + 1) . "\n0000000000 65535 f \n";

    for ($i = 1; $i <= $maxObjectId; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer\n<< /Size " . ($maxObjectId + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";

    return $pdf;
}

function user_export_pdf_add_object(&$pdf, &$offsets, $id, $body)
{
    $offsets[$id] = strlen($pdf);
    $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
}
