<?php

declare(strict_types=1);

function admin_header(string $title, array $admin)
{
    send_utf8_header();
    ?>
    <!doctype html>
    <html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> - Quản trị Gạo Vi Sinh</title>
        <link rel="stylesheet" href="../public/assets/style.css">
    </head>
    <body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2 style="font-size:28px;margin-bottom:8px">Gạo Vi Sinh</h2>
            <p style="color:#bbf7d0;margin-bottom:24px"><?= e($admin['name']) ?> • <?= e($admin['role']) ?></p>
            <a href="index.php">Tổng quan</a>
            <a href="products.php">Sản phẩm</a>
            <a href="orders.php">Đơn hàng</a>
            <a href="posts.php">Bài viết</a>
            <a href="settings.php">Cấu hình</a>
            <a href="../index.php">Xem website</a>
            <a href="logout.php">Đăng xuất</a>
        </aside>
        <main class="admin-main">
            <p class="eyebrow">Quản trị nội dung</p>
            <h1 style="font-size:48px"><?= e($title) ?></h1>
    <?php
}

function admin_footer()
{
    ?>
        </main>
    </div>
    </body>
    </html>
    <?php
}
