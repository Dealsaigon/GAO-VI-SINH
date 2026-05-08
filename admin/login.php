<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';

if (current_admin() !== null) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_login($email, $password)) {
        redirect('admin/index.php');
    }

    $error = 'Email hoặc mật khẩu không đúng.';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị - Gạo Vi Sinh</title>
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body class="login-page">
    <main class="login-box admin-card">
        <p class="eyebrow">Backend quản trị</p>
        <h1 style="font-size:44px">Đăng nhập</h1>
        <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <label>Email<input type="email" name="email" required autocomplete="email" value="admin@gaovisinh.vn"></label>
            <label>Mật khẩu<input type="password" name="password" required autocomplete="current-password" placeholder="Admin@123"></label>
            <button class="admin-btn" type="submit">Đăng nhập</button>
        </form>
    </main>
</body>
</html>
