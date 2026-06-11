<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$flash = pull_flash();
$username = trim(isset($_GET['username']) ? $_GET['username'] : '');
$email = trim(isset($_GET['email']) ? $_GET['email'] : '');
$resetDemo = isset($_SESSION['password_reset_demo']) ? $_SESSION['password_reset_demo'] : null;

if (!empty($resetDemo) && strtotime($resetDemo['expires_at']) < time()) {
    unset($_SESSION['password_reset_demo']);
    $resetDemo = null;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - FPT Workflow</title>
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
                <!-- e(format_datetime_vn($resetDemo['expires_at'], 'Y-m-d')) -->
                 <?php $currentDate = new DateTime(); // Tự động lấy mốc hiện tại ?>
                <?php if (!empty($resetDemo)) { ?>
                    <div class="alert alert-info">
                        Mã xác nhận demo: <strong><?php echo e($resetDemo['code']); ?></strong>
                        <div class="small mt-1">Hết hạn lúc <?php echo e(format_datetime_vn($resetDemo['expires_at'])); ?></div>
                        
                        <!-- <div> <?= $currentDate->format('d/m/Y H:i:s') ?> </div> -->
                        <!-- <div class="small mt-1">Hết hạn lúc <?php echo e(date('m/d/Y H:i', strtotime($resetDemo['expires_at']))); ?></div>
                     -->
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
                    <h1 class="h3 fw-bold mt-2">Đặt lại mật khẩu</h1>
                    <p class="text-secondary small">Mã xác nhận có hiệu lực trong 15 phút.</p>
                </div>

                <form method="post" action="actions/password_reset_save.php" class="vstack gap-3" id="resetPasswordForm">
                    <div>
                        <label class="form-label">Tên đăng nhập</label>
                        <input class="form-control form-control-lg" type="text" name="username" value="<?php echo e($username); ?>" placeholder="Nhập username..." required>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input class="form-control form-control-lg" type="email" name="email" value="<?php echo e($email); ?>" placeholder="Nhập email tài khoản..." required>
                    </div>
                    <div>
                        <label class="form-label">Mã xác nhận</label>
                        <input class="form-control form-control-lg" type="text" name="reset_code" placeholder="Nhập mã xác nhận..." maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                    </div>
                    <div>
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control form-control-lg" type="password" name="password" id="reset_password" placeholder="Nhập mật khẩu mới..." autocomplete="new-password">
                        <div id="reset_password_error" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
                    </div>
                    <div>
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input class="form-control form-control-lg" type="password" name="password_confirm" id="reset_password_confirm" placeholder="Nhập lại mật khẩu mới..." autocomplete="new-password">
                        <div id="reset_password_confirm_error" class="text-danger small mt-1" style="display: none; font-weight: 500;"></div>
                    </div>
                    <button class="btn btn-brand btn-lg w-100" type="submit">Đặt lại mật khẩu</button>
                </form>

                <div class="d-flex justify-content-between gap-3 mt-4">
                    <a class="text-decoration-none" href="forgot_password.php">Tạo mã mới</a>
                    <a class="text-decoration-none" href="login.php">Đăng nhập</a>
                </div>
            </div>
        </section>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
