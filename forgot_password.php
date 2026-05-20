<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$flash = pull_flash();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - FPT Workflow</title>
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
                    <h1 class="h3 fw-bold mt-2">Quên mật khẩu</h1>
                    <p class="text-secondary small">Nhập đúng username và email tài khoản để nhận mã xác nhận.</p>
                </div>

                <form method="post" action="actions/password_reset_request.php" class="vstack gap-3">
                    <div>
                        <label class="form-label">Tên đăng nhập</label>
                        <input class="form-control form-control-lg" type="text" name="username" placeholder="Nhập username..." required>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Nhập email tài khoản..." required>
                    </div>
                    <button class="btn btn-brand btn-lg w-100" type="submit">Tạo mã xác nhận</button>
                </form>

                <div class="text-center mt-4">
                    <a class="text-decoration-none" href="login.php">Quay lại đăng nhập</a>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
