<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/_layout.php';

$admin = require_admin();
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = scalar_input($_POST['action'] ?? 'save');

    if ($action === 'delete' && $id > 0) {
        $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        redirect('admin/products.php');
    }

    $name = scalar_input($_POST['name'] ?? '');
    $data = [
        $name,
        slugify($name),
        scalar_input($_POST['badge'] ?? ''),
        scalar_input($_POST['description'] ?? ''),
        max(0, (int) ($_POST['price'] ?? 0)),
        scalar_input($_POST['weight'] ?? '5kg'),
        scalar_input($_POST['image_url'] ?? ''),
        isset($_POST['is_active']) ? 1 : 0,
        (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($id > 0) {
        $stmt = db()->prepare('UPDATE products SET name=?, slug=?, badge=?, description=?, price=?, weight=?, image_url=?, is_active=?, sort_order=? WHERE id=?');
        $stmt->execute([...$data, $id]);
    } else {
        $stmt = db()->prepare('INSERT INTO products (name, slug, badge, description, price, weight, image_url, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute($data);
    }

    redirect('admin/products.php');
}

$products = db()->query('SELECT * FROM products ORDER BY sort_order ASC, id DESC')->fetchAll();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

admin_header('Sản phẩm', $admin);
?>
<section class="admin-card">
    <h2 style="font-size:30px"><?= $edit ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' ?></h2>
    <form class="admin-form" method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string) ($edit['id'] ?? 0)) ?>">
        <label>Tên sản phẩm<input name="name" required value="<?= e($edit['name'] ?? '') ?>"></label>
        <label>Nhãn nổi bật<input name="badge" value="<?= e($edit['badge'] ?? 'Gạo vi sinh') ?>"></label>
        <label>Mô tả<textarea name="description" rows="3" required><?= e($edit['description'] ?? '') ?></textarea></label>
        <label>Giá bán (VND)<input type="number" name="price" min="0" required value="<?= e((string) ($edit['price'] ?? 0)) ?>"></label>
        <label>Khối lượng<input name="weight" value="<?= e($edit['weight'] ?? '5kg') ?>"></label>
        <label>Ảnh sản phẩm<input name="image_url" value="<?= e($edit['image_url'] ?? '') ?>"></label>
        <label>Thứ tự<input type="number" name="sort_order" value="<?= e((string) ($edit['sort_order'] ?? 0)) ?>"></label>
        <label><input type="checkbox" name="is_active" <?= !isset($edit['is_active']) || (int) $edit['is_active'] === 1 ? 'checked' : '' ?>> Hiển thị ngoài website</label>
        <button class="admin-btn" type="submit">Lưu sản phẩm</button>
    </form>
</section>
<section class="admin-card">
    <h2 style="font-size:30px">Danh sách sản phẩm</h2>
    <table class="admin-table">
        <thead><tr><th>Tên</th><th>Giá</th><th>Khối lượng</th><th>Hiển thị</th><th>Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= e($product['name']) ?></td>
                <td><?= money_vnd((int) $product['price']) ?></td>
                <td><?= e($product['weight']) ?></td>
                <td><?= (int) $product['is_active'] === 1 ? 'Có' : 'Không' ?></td>
                <td>
                    <a href="?edit=<?= (int) $product['id'] ?>">Sửa</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php admin_footer(); ?>
