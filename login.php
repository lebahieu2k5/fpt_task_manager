<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$flash = pull_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        $user = find_user_by_username($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'username' => $user['username'],
                'role' => $user['role'],
                'department' => $user['department'],
            );

            set_flash('success', 'Đăng nhập thành công. Chào mừng bạn đến với hệ thống!');
            redirect('dashboard.php');
        }

        $error = 'Thông tin đăng nhập không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - FPT Workflow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-layout">
        <section class="login-panel">
            <div class="login-card">
                <?php if (!empty($flash)) { ?>
                    <div class="alert alert-<?php echo e($flash['type']); ?>">
                        <?php echo e($flash['message']); ?>
                    </div>
                <?php } ?>

                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php } ?>

                <div class="text-center mb-4 vstack gap-2">
                    <span class="brand-mark mx-auto" style="width: 64px; height: 64px; border-radius: 16px; padding: 8px;">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <g transform="skewX(-15) translate(5, 0)">
                                <rect x="5" y="15" width="22" height="70" rx="5" fill="#F37021" />
                                <rect x="36" y="15" width="22" height="70" rx="5" fill="#005CAA" />
                                <rect x="67" y="15" width="22" height="70" rx="5" fill="#009F4D" />
                            </g>
                        </svg>
                    </span>
                    <h1 class="h3 fw-bold mt-2">FPT Workflow</h1>
                    <p class="text-secondary small">Hệ thống quản lý công việc nội bộ</p>
                </div>

                <form method="post" action="login.php" class="vstack gap-3">
                    <div>
                        <label class="form-label">Tên đăng nhập</label>
                        <input class="form-control form-control-lg" type="text" name="username" placeholder="Nhập username..." required>
                    </div>
                    <div>
                        <label class="form-label">Mật khẩu</label>
                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Nhập mật khẩu..." required>
                    </div>
                    <button class="btn btn-brand btn-lg w-100" type="submit">Đăng nhập</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
