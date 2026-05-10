<?php

declare(strict_types=1);

require_once __DIR__ . '/repository.php';

function render_frontend(string $assetPath, string $adminPath)
{
    send_utf8_header();

    $products = featured_products();
    $posts = published_posts();
    $settings = site_settings();
    $batchCode = scalar_input($_GET['batch'] ?? '');
    $traceBatch = find_trace_batch($batchCode);
    $pageTitle = $settings['site_title'] ?? 'Gạo Vi Sinh';
    ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($settings['site_description'] ?? 'Gạo sạch vi sinh, truy xuất nguồn gốc và giảm phát thải carbon.') ?>">
    <link rel="stylesheet" href="<?= e($assetPath) ?>">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="#top" aria-label="Gạo Vi Sinh"><span></span>Gạo Vi Sinh</a>
        <nav>
            <a href="#products">Sản phẩm</a>
            <a href="#process">Quy trình</a>
            <a href="#trace">Truy xuất</a>
            <a href="#contact">Liên hệ</a>
            <a class="admin-link" href="<?= e($adminPath) ?>">Quản trị</a>
        </nav>
    </header>

    <main id="top">
        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">Canh tác vi sinh • Không hóa chất • Giảm phát thải</p>
                <h1>Ăn sạch bụng, nhẹ hành tinh.</h1>
                <p class="lead">Gạo Vi Sinh được quản lý từ giống, ruộng, mùa vụ đến đóng gói bằng dữ liệu SQL tập trung, giúp khách hàng xem sản phẩm minh bạch và đội ngũ vận hành quản trị dễ dàng.</p>
                <div class="hero-actions">
                    <a class="btn primary" href="#products">Xem sản phẩm</a>
                    <a class="btn ghost" href="#trace">Tra cứu lô gạo</a>
                </div>
            </div>
            <div class="hero-card">
                <img src="https://images.unsplash.com/photo-1574323347407-28d5d4d3c907?w=1200&q=80" alt="Cánh đồng lúa xanh">
                <div class="metric"><strong>-32%</strong><span>Mục tiêu giảm phát thải CO₂e/vụ</span></div>
            </div>
        </section>

        <section class="features" aria-label="Giá trị nổi bật">
            <article><strong>100% vi sinh</strong><span>Hạn chế phân bón hóa học, ưu tiên hệ vi sinh đất khỏe.</span></article>
            <article><strong>Truy xuất lô</strong><span>Quản lý mã QR, vùng trồng, ngày xay xát trong cơ sở dữ liệu.</span></article>
            <article><strong>Đặt hàng nhanh</strong><span>Form đặt hàng lưu về backend để nhân viên chăm sóc.</span></article>
        </section>

        <section id="products" class="section">
            <div class="section-heading">
                <p class="eyebrow">Danh mục sản phẩm</p>
                <h2>Sản phẩm đang bán</h2>
            </div>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
                        <div>
                            <p class="tag"><?= e($product['badge']) ?></p>
                            <h3><?= e($product['name']) ?></h3>
                            <p><?= e($product['description']) ?></p>
                            <div class="product-footer">
                                <strong><?= money_vnd((int) $product['price']) ?></strong>
                                <span><?= e($product['weight']) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="process" class="section split">
            <div>
                <p class="eyebrow">Quy trình</p>
                <h2>Từ ruộng đến bàn ăn</h2>
                <p>Hệ thống PHP + MySQL lưu nhật ký mùa vụ, tiêu chuẩn canh tác, sản phẩm, bài viết và đơn hàng để đồng bộ phần giao diện khách hàng với backend quản trị.</p>
            </div>
            <ol class="timeline">
                <li><span>01</span>Ủ phân hữu cơ vi sinh và cải tạo đất.</li>
                <li><span>02</span>Áp dụng tưới ướt khô xen kẽ để tiết kiệm nước.</li>
                <li><span>03</span>Xay xát, đóng gói, gắn mã lô và nhập kho.</li>
                <li><span>04</span>Công bố sản phẩm, nhận đơn và chăm sóc khách hàng.</li>
            </ol>
        </section>

        <section id="trace" class="section trace-box">
            <div>
                <p class="eyebrow">Truy xuất nguồn gốc</p>
                <h2>Nhập mã lô để kiểm tra</h2>
                <p>Module tra cứu đã sẵn sàng kết nối bảng <code>trace_batches</code> trong cơ sở dữ liệu để hiển thị vùng trồng, mùa vụ và chứng nhận.</p>
            </div>
            <div>
                <form class="trace-form" method="get" action="#trace">
                    <input name="batch" placeholder="VD: GVS-2026-ST25-001" value="<?= e($batchCode) ?>">
                    <button type="submit">Tra cứu</button>
                </form>
                <?php if ($batchCode !== ''): ?>
                    <?php if ($traceBatch): ?>
                        <div class="trace-result">
                            <strong><?= e($traceBatch['batch_code']) ?> • <?= e($traceBatch['product_name']) ?></strong>
                            <span>Vùng trồng: <?= e($traceBatch['farm_name']) ?>, <?= e($traceBatch['province']) ?></span>
                            <span>Mùa vụ: <?= e($traceBatch['season']) ?> • Đóng gói: <?= e($traceBatch['packed_at']) ?></span>
                        </div>
                    <?php else: ?>
                        <p class="trace-result is-warning">Chưa tìm thấy mã lô này. Vui lòng kiểm tra lại mã trên bao bì hoặc liên hệ hotline.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <div class="section-heading">
                <p class="eyebrow">Tin tức</p>
                <h2>Câu chuyện canh tác</h2>
            </div>
            <div class="post-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <p><?= date('d/m/Y', strtotime($post['published_at'])) ?></p>
                        <h3><?= e($post['title']) ?></h3>
                        <span><?= e($post['excerpt']) ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="contact" class="section contact-panel">
            <div>
                <p class="eyebrow">Liên hệ</p>
                <h2>Nhận báo giá đại lý hoặc đặt gạo gia đình</h2>
                <p><?= e($settings['hotline'] ?? 'Hotline: 0900 000 000') ?> • <?= e($settings['email'] ?? 'hello@gaovisinh.vn') ?></p>
            </div>
            <a class="btn primary" href="mailto:<?= e($settings['email'] ?? 'hello@gaovisinh.vn') ?>">Gửi yêu cầu</a>
        </section>
    </main>

    <footer class="site-footer">© <?= date('Y') ?> Gạo Vi Sinh. Vận hành bằng PHP & MySQL.</footer>
</body>
</html>

    <?php
}
