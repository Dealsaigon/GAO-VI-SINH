<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/_layout.php';

$admin = require_admin();
$stats = [
    'products' => (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders' => (int) db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'posts' => (int) db()->query('SELECT COUNT(*) FROM posts')->fetchColumn(),
    'revenue' => (int) db()->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != "cancelled"')->fetchColumn(),
];
$orders = db()->query('SELECT customer_name, phone, total_amount, status, created_at FROM orders ORDER BY id DESC LIMIT 5')->fetchAll();

admin_header('Tổng quan', $admin);
?>
<section class="features" style="margin-bottom:22px">
    <article><strong><?= $stats['products'] ?></strong><span>Sản phẩm</span></article>
    <article><strong><?= $stats['orders'] ?></strong><span>Đơn hàng</span></article>
    <article><strong><?= money_vnd($stats['revenue']) ?></strong><span>Doanh thu ghi nhận</span></article>
</section>
<section class="admin-card">
    <h2 style="font-size:30px">Đơn hàng mới</h2>
    <table class="admin-table">
        <thead><tr><th>Khách hàng</th><th>Điện thoại</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order['customer_name']) ?></td>
                    <td><?= e($order['phone']) ?></td>
                    <td><?= money_vnd((int) $order['total_amount']) ?></td>
                    <td><?= e($order['status']) ?></td>
                    <td><?= e($order['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php admin_footer(); ?>
