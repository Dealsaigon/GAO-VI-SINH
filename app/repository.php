<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

function featured_products(int $limit = 6): array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE is_active = 1 ORDER BY sort_order ASC, id DESC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function published_posts(int $limit = 3): array
{
    $stmt = db()->prepare('SELECT * FROM posts WHERE status = "published" ORDER BY published_at DESC, id DESC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function site_settings(): array
{
    $rows = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function find_trace_batch(string $batchCode): ?array
{
    if ($batchCode === '') {
        return null;
    }

    $stmt = db()->prepare('SELECT trace_batches.*, products.name AS product_name FROM trace_batches INNER JOIN products ON products.id = trace_batches.product_id WHERE trace_batches.batch_code = ? LIMIT 1');
    $stmt->execute([$batchCode]);
    $batch = $stmt->fetch();

    return $batch ?: null;
}
