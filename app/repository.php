<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

function fallback_products(): array
{
    return [
        [
            'id' => 1,
            'name' => 'Gạo ST25 Vi Sinh',
            'slug' => 'gao-st25-vi-sinh',
            'badge' => 'Bán chạy',
            'description' => 'Hạt dài, thơm tự nhiên, phù hợp bữa cơm gia đình cần gạo sạch và mềm dẻo.',
            'price' => 185000,
            'weight' => '5kg',
            'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80',
            'is_active' => 1,
            'sort_order' => 1,
        ],
        [
            'id' => 2,
            'name' => 'Gạo Lứt Vi Sinh',
            'slug' => 'gao-lut-vi-sinh',
            'badge' => 'Ăn lành',
            'description' => 'Gạo lứt giữ lớp cám, giàu chất xơ, đóng gói theo lô có truy xuất nguồn gốc.',
            'price' => 155000,
            'weight' => '3kg',
            'image_url' => 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800&q=80',
            'is_active' => 1,
            'sort_order' => 2,
        ],
        [
            'id' => 3,
            'name' => 'Gạo Jasmine Vi Sinh',
            'slug' => 'gao-jasmine-vi-sinh',
            'badge' => 'Thơm nhẹ',
            'description' => 'Dòng gạo thơm nhẹ, cơm tơi, canh tác theo hướng vi sinh và tiết kiệm nước.',
            'price' => 168000,
            'weight' => '5kg',
            'image_url' => 'https://images.unsplash.com/photo-1603569283847-aa295f0d016a?w=800&q=80',
            'is_active' => 1,
            'sort_order' => 3,
        ],
    ];
}

function fallback_posts(): array
{
    return [
        [
            'title' => 'Vì sao đất khỏe tạo nên hạt gạo ngon?',
            'excerpt' => 'Canh tác vi sinh tập trung nuôi hệ đất, giúp cây lúa hấp thu ổn định hơn.',
            'published_at' => '2026-05-01 08:00:00',
        ],
        [
            'title' => 'Tưới ướt khô xen kẽ giúp giảm phát thải như thế nào?',
            'excerpt' => 'Kỹ thuật quản lý nước góp phần giảm methane và tiết kiệm tài nguyên.',
            'published_at' => '2026-05-03 08:00:00',
        ],
        [
            'title' => 'Hướng dẫn đọc mã truy xuất trên bao gạo',
            'excerpt' => 'Khách hàng có thể kiểm tra vùng trồng, mùa vụ, ngày đóng gói và chứng nhận.',
            'published_at' => '2026-05-05 08:00:00',
        ],
    ];
}

function fallback_settings(): array
{
    return [
        'site_title' => 'Gạo Vi Sinh - Gạo sạch giảm phát thải',
        'site_description' => 'Website thương hiệu Gạo Vi Sinh: giới thiệu sản phẩm, truy xuất nguồn gốc và backend quản trị.',
        'hotline' => 'Hotline: 0900 000 000',
        'email' => 'hello@gaovisinh.vn',
    ];
}

function featured_products(int $limit = 6): array
{
    try {
        $stmt = db()->prepare('SELECT * FROM products WHERE is_active = 1 ORDER BY sort_order ASC, id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Throwable $exception) {
        return array_slice(fallback_products(), 0, $limit);
    }
}

function published_posts(int $limit = 3): array
{
    try {
        $stmt = db()->prepare('SELECT * FROM posts WHERE status = "published" ORDER BY published_at DESC, id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Throwable $exception) {
        return array_slice(fallback_posts(), 0, $limit);
    }
}

function site_settings(): array
{
    try {
        $rows = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    } catch (Throwable $exception) {
        return fallback_settings();
    }
}

function find_trace_batch(string $batchCode)
{
    if ($batchCode === '') {
        return null;
    }

    try {
        $stmt = db()->prepare('SELECT trace_batches.*, products.name AS product_name FROM trace_batches INNER JOIN products ON products.id = trace_batches.product_id WHERE trace_batches.batch_code = ? LIMIT 1');
        $stmt->execute([$batchCode]);
        $batch = $stmt->fetch();

        return $batch ?: null;
    } catch (Throwable $exception) {
        if (strtoupper($batchCode) !== 'GVS-2026-ST25-001') {
            return null;
        }

        return [
            'batch_code' => 'GVS-2026-ST25-001',
            'product_name' => 'Gạo ST25 Vi Sinh',
            'farm_name' => 'Hợp tác xã Lúa Xanh',
            'province' => 'Sóc Trăng',
            'season' => 'Đông Xuân 2026',
            'packed_at' => '2026-04-25',
        ];
    }
}
