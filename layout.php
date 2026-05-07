<?php

function render_header($pageTitle, $activePage)
{
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($pageTitle); ?> - FPT Task Manager</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="dashboard.php">
            <span class="brand-mark">F</span>
            <span>
                <strong>FPT Task Manager</strong>
                <small>Quản lý tiến độ công việc</small>
            </span>
        </a>

        <nav class="main-nav">
            <a class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
            <a class="<?php echo $activePage === 'board' ? 'active' : ''; ?>" href="board.php">Board</a>
            <a class="<?php echo $activePage === 'tasks' ? 'active' : ''; ?>" href="task-list.php">Danh sách task</a>
            <a class="nav-button <?php echo $activePage === 'add' ? 'active' : ''; ?>" href="task-add.php">Thêm task</a>
        </nav>
    </header>

    <main class="container">
<?php
}

function render_footer()
{
?>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
<?php
}
