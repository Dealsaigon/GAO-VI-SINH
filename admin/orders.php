<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/_layout.php';

$admin = require_admin();
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('UPDATE orders SET status = ?, note = ? WHERE id = ?');
    $stmt->execute([(string) $_POST['status'], (string) $_POST['note'], (int) $_POST['id']]);
    redirect('admin/orders.php');
}

$orders = db()->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
admin_header('Đơn hàng', $admin);
?>
<section class="admin-card">
    <table class="admin-table">
        <thead><tr><th>Mã</th><th>Khách hàng</th><th>Liên hệ</th><th>Sản phẩm</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ghi chú</th><th>Cập nhật</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= (int) $order['id'] ?></td>
                <td><?= e($order['customer_name']) ?><br><?= e($order['address']) ?></td>
                <td><?= e($order['phone']) ?><br><?= e($order['email']) ?></td>
                <td><?= e($order['items_summary']) ?></td>
                <td><?= money_vnd((int) $order['total_amount']) ?></td>
                <td colspan="3">
                    <form class="admin-form" method="post">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                        <select name="status">
                            <?php foreach (['new' => 'Mới', 'confirmed' => 'Đã xác nhận', 'shipping' => 'Đang giao', 'completed' => 'Hoàn tất', 'cancelled' => 'Đã hủy'] as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $order['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea name="note" rows="2"><?= e($order['note']) ?></textarea>
                        <button class="admin-btn" type="submit">Lưu</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php admin_footer(); ?>
