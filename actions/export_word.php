<?php
require_once __DIR__ . '/../bootstrap.php';
require_report_access();

$assigneeReport = fetch_assignee_report();

header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=bao_cao_nhan_su_" . date('Ymd') . ".doc");

?>
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Báo cáo nhân sự</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>BÁO CÁO TIẾN ĐỘ THEO NHÂN SỰ</h2>
    <p>Ngày xuất báo cáo: <?php echo date('d/m/Y H:i'); ?></p>
    <table>
        <thead>
            <tr>
                <th>Nhân sự</th>
                <th>Tổng số task</th>
                <th>Đã hoàn thành</th>
                <th>Trễ hạn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assigneeReport as $row) { ?>
                <tr>
                    <td><?php echo e($row['assignee_name']); ?></td>
                    <td><?php echo (int) $row['task_count']; ?></td>
                    <td><?php echo (int) $row['done_count']; ?></td>
                    <td><?php echo (int) $row['overdue_count']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
<?php
exit;
