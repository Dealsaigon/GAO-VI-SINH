<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/_layout.php';

$admin = require_admin();
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
        $stmt = db()->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        redirect('admin/posts.php');
    }

    $data = [
        scalar_input($_POST['title'] ?? ''),
        slugify(scalar_input($_POST['title'] ?? '')),
        scalar_input($_POST['excerpt'] ?? ''),
        scalar_input($_POST['content'] ?? ''),
        scalar_input($_POST['status'] ?? 'draft'),
        scalar_input($_POST['published_at'] ?? '') ?: date('Y-m-d H:i:s'),
    ];

    if ($id > 0) {
        $stmt = db()->prepare('UPDATE posts SET title=?, slug=?, excerpt=?, content=?, status=?, published_at=? WHERE id=?');
        $stmt->execute([...$data, $id]);
    } else {
        $stmt = db()->prepare('INSERT INTO posts (title, slug, excerpt, content, status, published_at) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute($data);
    }
    redirect('admin/posts.php');
}

$posts = db()->query('SELECT * FROM posts ORDER BY published_at DESC, id DESC')->fetchAll();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

admin_header('Bài viết', $admin);
?>
<section class="admin-card">
    <h2 style="font-size:30px"><?= $edit ? 'Cập nhật bài viết' : 'Thêm bài viết' ?></h2>
    <form class="admin-form" method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string) ($edit['id'] ?? 0)) ?>">
        <label>Tiêu đề<input name="title" required value="<?= e($edit['title'] ?? '') ?>"></label>
        <label>Tóm tắt<textarea name="excerpt" rows="2" required><?= e($edit['excerpt'] ?? '') ?></textarea></label>
        <label>Nội dung<textarea name="content" rows="7" required><?= e($edit['content'] ?? '') ?></textarea></label>
        <label>Trạng thái<select name="status"><option value="draft" <?= ($edit['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Nháp</option><option value="published" <?= ($edit['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Xuất bản</option></select></label>
        <label>Ngày xuất bản<input name="published_at" value="<?= e($edit['published_at'] ?? date('Y-m-d H:i:s')) ?>"></label>
        <button class="admin-btn" type="submit">Lưu bài viết</button>
    </form>
</section>
<section class="admin-card">
    <h2 style="font-size:30px">Danh sách bài viết</h2>
    <table class="admin-table">
        <thead><tr><th>Tiêu đề</th><th>Trạng thái</th><th>Ngày xuất bản</th><th>Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= e($post['title']) ?></td>
                <td><?= e($post['status']) ?></td>
                <td><?= e($post['published_at']) ?></td>
                <td>
                    <a href="?edit=<?= (int) $post['id'] ?>">Sửa</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Xóa bài viết này?')">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
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
