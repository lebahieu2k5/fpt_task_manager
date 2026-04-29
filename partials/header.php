<?php
$pageTitle = isset($pageTitle) ? $pageTitle : 'FPT Task Manager';
$activePage = isset($activePage) ? $activePage : '';
$flash = pull_flash();
$user = current_user();
$userInitial = $user ? strtoupper(substr($user['full_name'], 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="app-sidebar">
            <a class="brand-box" href="dashboard.php">
                <span class="brand-mark">F</span>
                <div>
                    <strong>FPT Workflow</strong>
                    <small>Quan li cong viec noi bo</small>
                </div>
            </a>

            <div class="sidebar-section">
                <p class="sidebar-label">Dieu huong</p>
                <nav class="nav flex-column gap-2">
                    <a class="nav-link-custom <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link-custom <?php echo $activePage === 'boards' ? 'active' : ''; ?>" href="boards.php">
                        <i class="bi bi-kanban"></i>
                        <span>Bang cong viec</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-section mt-auto">
                <div class="role-box">
                    <span class="role-title">Vai tro hien tai</span>
                    <strong><?php echo current_user_role() === 'manager' ? 'Chu bang / Quan ly' : 'Thanh vien'; ?></strong>
                </div>
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <div>
                    <p class="topbar-eyebrow">Bai tap lon mon lap trinh web</p>
                    <h1 class="topbar-title"><?php echo e($pageTitle); ?></h1>
                </div>
                <div class="user-chip">
                    <span class="user-avatar"><?php echo e($userInitial); ?></span>
                    <div>
                        <strong><?php echo e($user['full_name']); ?></strong>
                        <small><?php echo e($user['department']); ?></small>
                    </div>
                    <a class="btn btn-outline-secondary btn-sm ms-2" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <?php if (!empty($flash)) { ?>
                    <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show" role="alert">
                        <?php echo e($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
