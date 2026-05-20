<?php

function build_export_report_data()
{
    $stats = fetch_dashboard_stats();
    $boards = fetch_boards_for_current_user();
    $statusReport = fetch_task_status_report();
    $assigneeReport = fetch_assignee_report();

    $taskCount = !empty($stats['task_count']) ? (int) $stats['task_count'] : 0;
    $doneCount = !empty($stats['done_count']) ? (int) $stats['done_count'] : 0;
    $completionRate = $taskCount > 0 ? round(($doneCount / $taskCount) * 100) : 0;

    return array(
        'generated_at' => date('d/m/Y H:i'),
        'stats' => array(
            'board_count' => !empty($stats['board_count']) ? (int) $stats['board_count'] : 0,
            'task_count' => $taskCount,
            'done_count' => $doneCount,
            'overdue_count' => !empty($stats['overdue_count']) ? (int) $stats['overdue_count'] : 0,
            'completion_rate' => $completionRate,
        ),
        'boards' => $boards,
        'status_report' => $statusReport,
        'assignee_report' => $assigneeReport,
    );
}

function render_report_html_document($title, $data)
{
    $stats = $data['stats'];
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            font-size: 13px;
            line-height: 1.45;
        }
        h1 {
            color: #1068b0;
            font-size: 22px;
            margin: 0 0 6px;
            text-align: center;
            text-transform: uppercase;
        }
        h2 {
            color: #1068b0;
            font-size: 16px;
            margin: 24px 0 8px;
        }
        p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #1068b0;
            color: #ffffff;
            font-weight: 700;
          
        }
        .number {
            text-align: right;
        }
        .muted {
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1><?php echo e($title); ?></h1>
    <p><strong>Ngày xuất báo cáo:</strong> <?php echo e($data['generated_at']); ?></p>

    <h2">Tổng quan</h2>
    <table>
        <thead>
            <tr>
                <th>Bảng công việc</th>
                <th>Tổng task</th>
                <th>Task hoàn thành</th>
                <th>Tỉ lệ hoàn thành</th>
                <th>Task trễ hạn</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="number"><?php echo e($stats['board_count']); ?></td>
                <td class="number"><?php echo e($stats['task_count']); ?></td>
                <td class="number"><?php echo e($stats['done_count']); ?></td>
                <td class="number"><?php echo e($stats['completion_rate']); ?>%</td>
                <td class="number"><?php echo e($stats['overdue_count']); ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Thống kê trạng thái</h2>
    <table>
        <thead>
            <tr>
                <th>Trạng thái</th>
                <th>Số task</th>
                <th>Tỉ lệ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['status_report'] as $row) { ?>
                <?php $percent = $stats['task_count'] > 0 ? round(((int) $row['task_count'] / $stats['task_count']) * 100) : 0; ?>
                <tr>
                    <td><?php echo e($row['status_name']); ?></td>
                    <td class="number"><?php echo e((int) $row['task_count']); ?></td>
                    <td class="number"><?php echo e($percent); ?>%</td>
                </tr>
            <?php } ?>
            <?php if (empty($data['status_report'])) { ?>
                <tr><td colspan="3" class="muted">Chưa có dữ liệu.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Tiến độ theo bảng</h2>
    <table>
        <thead>
            <tr>
                <th>Bảng</th>
                <th>Thành viên</th>
                <th>Task</th>
                <th>Đã xong</th>
                <th>Hoàn thành</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['boards'] as $board) { ?>
                <?php $progress = (int) $board['task_count'] > 0 ? round(((int) $board['done_count'] / (int) $board['task_count']) * 100) : 0; ?>
                <tr>
                    <td><?php echo e($board['name']); ?></td>
                    <td class="number"><?php echo e((int) $board['member_count']); ?></td>
                    <td class="number"><?php echo e((int) $board['task_count']); ?></td>
                    <td class="number"><?php echo e((int) $board['done_count']); ?></td>
                    <td class="number"><?php echo e($progress); ?>%</td>
                </tr>
            <?php } ?>
            <?php if (empty($data['boards'])) { ?>
                <tr><td colspan="5" class="muted">Chưa có dữ liệu.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Báo cáo theo nhân sự</h2>
    <table>
        <thead>
            <tr>
                <th>Nhân sự</th>
                <th>Tổng task</th>
                <th>Đã hoàn thành</th>
                <th>Trễ hạn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['assignee_report'] as $row) { ?>
                <tr>
                    <td><?php echo e($row['assignee_name']); ?></td>
                    <td class="number"><?php echo e((int) $row['task_count']); ?></td>
                    <td class="number"><?php echo e((int) $row['done_count']); ?></td>
                    <td class="number"><?php echo e((int) $row['overdue_count']); ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($data['assignee_report'])) { ?>
                <tr><td colspan="4" class="muted">Chưa có dữ liệu.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
    <?php
    return ob_get_clean();
}

function render_report_excel_document($title, $data)
{
    $stats = $data['stats'];
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="vi" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Báo cáo</x:Name>
                    <x:WorksheetOptions>
                        <x:FitToPage/>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                            <x:PaperSizeIndex>9</x:PaperSizeIndex>
                            <x:Scale>82</x:Scale>
                            <x:FitWidth>1</x:FitWidth>
                            <x:FitHeight>1</x:FitHeight>
                        </x:Print>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: A4;
            margin: 0.25in 0.25in 0.25in 0.25in;
            mso-page-orientation: portrait;
            mso-header-margin: 0.1in;
            mso-footer-margin: 0.1in;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #111827;
        }
        table.report-sheet {
            border-collapse: collapse;
            table-layout: fixed;
            width: 535pt;
        }
        td,
        th {
            border: 1px solid #cbd5e1;
            padding: 2pt 3pt;
            height: 17pt;
            vertical-align: middle;
            mso-font-charset: 0;
        }
        .no-border {
            border: 0;
        }
        .report-title {
            color: #000000;
            font-size: 20pt;
            font-weight: 700;
            text-align: center;
            height: 28pt;
        }
        .report-date {
            font-size: 8pt;
            font-weight: 700;
        }
        .section-title {
            color: #000000;
            font-size: 14pt;
            font-weight: 700;
            height: 20pt;
        }
        .table-header {
            font-size: 10pt;
            font-weight: 700;
            background: #eef6ff;
            white-space: nowrap;
        }
        .number {
            text-align: right;
        }
        .empty-row {
            text-align: center;
            color: #64748b;
        }
        .spacer {
            height: 3pt;
            border: 0;
        }
    </style>
</head>
<body>
    <table class="report-sheet">
        <col style="width: 185pt;">
        <col style="width: 72pt;">
        <col style="width: 112pt;">
        <col style="width: 92pt;">
        <col style="width: 74pt;">

        <tr>
            <td class="report-title no-border" colspan="5" align="center" style="border: 0; color: #000000; font-size: 20pt; font-weight: 700; text-align: center; height: 28pt;">
                <span style="color: #000000; font-size: 20pt; font-weight: 700;"><?php echo e($title); ?></span>
            </td>
        </tr>
        <tr>
            <td class="report-date no-border" colspan="5">Ngày xuất báo cáo: <?php echo e($data['generated_at']); ?></td>
        </tr>

        <tr>
            <td class="section-title no-border" colspan="5" style="border: 0; color: #000000; font-size: 14pt; font-weight: 700; text-align: left; height: 20pt;">
                <span style="color: #000000; font-size: 14pt; font-weight: 700;">Tổng quan</span>
            </td>
        </tr>
        <tr>
            <th class="table-header">Bảng công việc</th>
            <th class="table-header">Tổng task</th>
            <th class="table-header">Task hoàn thành</th>
            <th class="table-header">Tỉ lệ hoàn thành</th>
            <th class="table-header">Task trễ hạn</th>
        </tr>
        <tr>
            <td class="number"><?php echo e($stats['board_count']); ?></td>
            <td class="number"><?php echo e($stats['task_count']); ?></td>
            <td class="number"><?php echo e($stats['done_count']); ?></td>
            <td class="number"><?php echo e($stats['completion_rate']); ?>%</td>
            <td class="number"><?php echo e($stats['overdue_count']); ?></td>
        </tr>
        <tr><td class="spacer" colspan="5"></td></tr>

        <tr>
            <td class="section-title no-border" colspan="5" style="border: 0; color: #000000; font-size: 14pt; font-weight: 700; text-align: left; height: 20pt;">
                <span style="color: #000000; font-size: 14pt; font-weight: 700;">Thống kê trạng thái</span>
            </td>
        </tr>
        <tr>
            <th class="table-header">Trạng thái</th>
            <th class="table-header">Số task</th>
            <th class="table-header">Tỉ lệ</th>
            <td class="no-border"></td>
            <td class="no-border"></td>
        </tr>
        <?php foreach ($data['status_report'] as $row) { ?>
            <?php $percent = $stats['task_count'] > 0 ? round(((int) $row['task_count'] / $stats['task_count']) * 100) : 0; ?>
            <tr>
                <td><?php echo e($row['status_name']); ?></td>
                <td class="number"><?php echo e((int) $row['task_count']); ?></td>
                <td class="number"><?php echo e($percent); ?>%</td>
                <td class="no-border"></td>
                <td class="no-border"></td>
            </tr>
        <?php } ?>
        <?php if (empty($data['status_report'])) { ?>
            <tr><td class="empty-row" colspan="3">Chưa có dữ liệu.</td><td class="no-border"></td><td class="no-border"></td></tr>
        <?php } ?>
        <tr><td class="spacer" colspan="5"></td></tr>

        <tr>
            <td class="section-title no-border" colspan="5" style="border: 0; color: #000000; font-size: 14pt; font-weight: 700; text-align: left; height: 20pt;">
                <span style="color: #000000; font-size: 14pt; font-weight: 700;">Tiến độ theo bảng</span>
            </td>
        </tr>
        <tr>
            <th class="table-header">Bảng</th>
            <th class="table-header">Thành viên</th>
            <th class="table-header">Task</th>
            <th class="table-header">Đã xong</th>
            <th class="table-header">Hoàn thành</th>
        </tr>
        <?php foreach ($data['boards'] as $board) { ?>
            <?php $progress = (int) $board['task_count'] > 0 ? round(((int) $board['done_count'] / (int) $board['task_count']) * 100) : 0; ?>
            <tr>
                <td><?php echo e($board['name']); ?></td>
                <td class="number"><?php echo e((int) $board['member_count']); ?></td>
                <td class="number"><?php echo e((int) $board['task_count']); ?></td>
                <td class="number"><?php echo e((int) $board['done_count']); ?></td>
                <td class="number"><?php echo e($progress); ?>%</td>
            </tr>
        <?php } ?>
        <?php if (empty($data['boards'])) { ?>
            <tr><td class="empty-row" colspan="5">Chưa có dữ liệu.</td></tr>
        <?php } ?>
        <tr><td class="spacer" colspan="5"></td></tr>

        <tr>
            <td class="section-title no-border" colspan="5" style="border: 0; color: #000000; font-size: 14pt; font-weight: 700; text-align: left; height: 20pt;">
                <span style="color: #000000; font-size: 14pt; font-weight: 700;">Báo cáo theo nhân sự</span>
            </td>
        </tr>
        <tr>
            <th class="table-header">Nhân sự</th>
            <th class="table-header">Tổng task</th>
            <th class="table-header">Đã hoàn thành</th>
            <th class="table-header">Trễ hạn</th>
            <td class="no-border"></td>
        </tr>
        <?php foreach ($data['assignee_report'] as $row) { ?>
            <tr>
                <td><?php echo e($row['assignee_name']); ?></td>
                <td class="number"><?php echo e((int) $row['task_count']); ?></td>
                <td class="number"><?php echo e((int) $row['done_count']); ?></td>
                <td class="number"><?php echo e((int) $row['overdue_count']); ?></td>
                <td class="no-border"></td>
            </tr>
        <?php } ?>
        <?php if (empty($data['assignee_report'])) { ?>
            <tr><td class="empty-row" colspan="4">Chưa có dữ liệu.</td><td class="no-border"></td></tr>
        <?php } ?>
    </table>
</body>
</html>
    <?php
    return ob_get_clean();
}
