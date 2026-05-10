<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/repository.php';
require_once __DIR__ . '/_layout.php';

$admin = require_admin();
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach (['site_title', 'site_description', 'hotline', 'email'] as $key) {
        $stmt->execute([$key, scalar_input($_POST[$key] ?? '')]);
    }
    redirect('admin/settings.php');
}

$settings = site_settings();
admin_header('Cấu hình website', $admin);
?>
<section class="admin-card">
    <form class="admin-form" method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <label>Tiêu đề website<input name="site_title" value="<?= e($settings['site_title'] ?? '') ?>"></label>
        <label>Mô tả SEO<textarea name="site_description" rows="3"><?= e($settings['site_description'] ?? '') ?></textarea></label>
        <label>Hotline<input name="hotline" value="<?= e($settings['hotline'] ?? '') ?>"></label>
        <label>Email<input type="email" name="email" value="<?= e($settings['email'] ?? '') ?>"></label>
        <button class="admin-btn" type="submit">Lưu cấu hình</button>
    </form>
</section>
<?php admin_footer(); ?>
