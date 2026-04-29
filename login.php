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
        $error = 'Vui long nhap day du ten dang nhap va mat khau.';
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

            set_flash('success', 'Dang nhap thanh cong. Chao mung ban den voi he thong!');
            redirect('dashboard.php');
        }

        $error = 'Thong tin dang nhap khong dung.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dang nhap - FPT Workflow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-layout">
        <section class="login-hero">
            <div class="hero-card">
                <span class="hero-badge">De tai goi y tu Use Case + Trello</span>
                <h1>Web quan li cong viec va tien do cho cong ty FPT</h1>
                <p>
                    Giao dien mo phong kieu Trello o muc don gian: co bang cong viec, cot trang thai,
                    the task, giao viec, deadline, uu tien va dashboard tong hop tien do.
                </p>
                <ul class="hero-points">
                    <li>Chu bang tao bang, moi thanh vien, giao task.</li>
                    <li>Thanh vien nhan viec, cap nhat tien do, doi trang thai.</li>
                    <li>Dashboard nhin nhanh task hoan thanh, task tre han, deadline sap toi.</li>
                </ul>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="mb-4">
                    <p class="text-uppercase text-secondary fw-semibold small mb-2">FPT Workflow</p>
                    <h2 class="mb-2">Dang nhap he thong</h2>
                    <p class="text-secondary mb-0">Dang nhap bang tai khoan mau de chay demo tren XAMPP.</p>
                </div>

                <?php if (!empty($flash)) { ?>
                    <div class="alert alert-<?php echo e($flash['type']); ?>">
                        <?php echo e($flash['message']); ?>
                    </div>
                <?php } ?>

                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php } ?>

                <form method="post" action="login.php" class="vstack gap-3">
                    <div>
                        <label class="form-label">Ten dang nhap</label>
                        <input class="form-control form-control-lg" type="text" name="username" placeholder="Nhap username..." required>
                    </div>
                    <div>
                        <label class="form-label">Mat khau</label>
                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Nhap mat khau..." required>
                    </div>
                    <button class="btn btn-brand btn-lg w-100" type="submit">Dang nhap</button>
                </form>

                <div class="demo-box mt-4">
                    <h3 class="h6 mb-3">Tai khoan demo</h3>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Vai tro</th>
                                    <th>Username</th>
                                    <th>Mat khau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Quan ly</td>
                                    <td><code>manager01</code></td>
                                    <td><code>123456</code></td>
                                </tr>
                                <tr>
                                    <td>Thanh vien</td>
                                    <td><code>nhanvien01</code></td>
                                    <td><code>123456</code></td>
                                </tr>
                                <tr>
                                    <td>Thanh vien</td>
                                    <td><code>nhanvien02</code></td>
                                    <td><code>123456</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
