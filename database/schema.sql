SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS gao_vi_sinh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gao_vi_sinh;

CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'manager',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    badge VARCHAR(120) DEFAULT NULL,
    description TEXT NOT NULL,
    price INT UNSIGNED NOT NULL DEFAULT 0,
    weight VARCHAR(40) NOT NULL DEFAULT '5kg',
    image_url VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_products_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(220) NOT NULL,
    slug VARCHAR(240) NOT NULL UNIQUE,
    excerpt VARCHAR(500) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    published_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_posts_status_published (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    email VARCHAR(180) DEFAULT NULL,
    address VARCHAR(500) DEFAULT NULL,
    items_summary TEXT NOT NULL,
    total_amount INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('new', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_orders_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trace_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_code VARCHAR(80) NOT NULL UNIQUE,
    product_id BIGINT UNSIGNED NOT NULL,
    farm_name VARCHAR(180) NOT NULL,
    province VARCHAR(120) NOT NULL,
    season VARCHAR(80) NOT NULL,
    cultivation_log JSON DEFAULT NULL,
    certificate_url VARCHAR(500) DEFAULT NULL,
    packed_at DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trace_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    setting_key VARCHAR(120) PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (name, email, password_hash, role) VALUES
('Quản trị viên', 'admin@gaovisinh.vn', '$2y$12$DxEkwFyQzxYPJtYCHe0D2eQIbOwtoFLQLzP0HNaxon5LZmzBCzIDS', 'admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (name, slug, badge, description, price, weight, image_url, is_active, sort_order) VALUES
('Gạo ST25 Vi Sinh', 'gao-st25-vi-sinh', 'Bán chạy', 'Hạt dài, thơm tự nhiên, phù hợp bữa cơm gia đình cần gạo sạch và mềm dẻo.', 185000, '5kg', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80', 1, 1),
('Gạo Lứt Vi Sinh', 'gao-lut-vi-sinh', 'Ăn lành', 'Gạo lứt giữ lớp cám, giàu chất xơ, đóng gói theo lô có truy xuất nguồn gốc.', 155000, '3kg', 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800&q=80', 1, 2),
('Gạo Jasmine Vi Sinh', 'gao-jasmine-vi-sinh', 'Thơm nhẹ', 'Dòng gạo thơm nhẹ, cơm tơi, canh tác theo hướng vi sinh và tiết kiệm nước.', 168000, '5kg', 'https://images.unsplash.com/photo-1603569283847-aa295f0d016a?w=800&q=80', 1, 3)
ON DUPLICATE KEY UPDATE name = VALUES(name), badge = VALUES(badge), description = VALUES(description), price = VALUES(price), weight = VALUES(weight), image_url = VALUES(image_url), is_active = VALUES(is_active), sort_order = VALUES(sort_order);

INSERT INTO posts (title, slug, excerpt, content, status, published_at) VALUES
('Vì sao đất khỏe tạo nên hạt gạo ngon?', 'vi-sao-dat-khoe-tao-nen-hat-gao-ngon', 'Canh tác vi sinh tập trung nuôi hệ đất, giúp cây lúa hấp thu ổn định hơn.', 'Nội dung chi tiết về hệ vi sinh đất, phân hữu cơ và quy trình quản lý ruộng.', 'published', '2026-05-01 08:00:00'),
('Tưới ướt khô xen kẽ giúp giảm phát thải như thế nào?', 'tuoi-uot-kho-xen-ke-giam-phat-thai', 'Kỹ thuật quản lý nước góp phần giảm methane và tiết kiệm tài nguyên.', 'Nội dung chi tiết về lịch tưới, theo dõi mực nước và số liệu mùa vụ.', 'published', '2026-05-03 08:00:00'),
('Hướng dẫn đọc mã truy xuất trên bao gạo', 'huong-dan-doc-ma-truy-xuat-tren-bao-gao', 'Khách hàng có thể kiểm tra vùng trồng, mùa vụ, ngày đóng gói và chứng nhận.', 'Nội dung hướng dẫn khách hàng tra cứu mã lô và liên hệ khi cần hỗ trợ.', 'published', '2026-05-05 08:00:00')
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), status = VALUES(status), published_at = VALUES(published_at);

INSERT INTO orders (customer_name, phone, email, address, items_summary, total_amount, status, note) VALUES
('Nguyễn Minh Anh', '0901000001', 'minhanh@example.com', 'Quận 1, TP.HCM', '2 x Gạo ST25 Vi Sinh 5kg', 370000, 'new', 'Giao giờ hành chính'),
('Công ty Xanh', '0902000002', 'sales@example.com', 'Cầu Giấy, Hà Nội', '20 x Gạo Jasmine Vi Sinh 5kg', 3360000, 'confirmed', 'Đơn đại lý')
ON DUPLICATE KEY UPDATE customer_name = VALUES(customer_name), phone = VALUES(phone), email = VALUES(email), address = VALUES(address), items_summary = VALUES(items_summary), total_amount = VALUES(total_amount), status = VALUES(status), note = VALUES(note);

INSERT INTO trace_batches (batch_code, product_id, farm_name, province, season, cultivation_log, certificate_url, packed_at) VALUES
('GVS-2026-ST25-001', (SELECT id FROM products WHERE slug = 'gao-st25-vi-sinh' LIMIT 1), 'Hợp tác xã Lúa Xanh', 'Sóc Trăng', 'Đông Xuân 2026', JSON_OBJECT('method', 'Vi sinh', 'water', 'Tưới ướt khô xen kẽ'), 'https://example.com/certificates/gvs-2026-st25-001.pdf', '2026-04-25')
ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), packed_at = VALUES(packed_at);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Gạo Vi Sinh - Gạo sạch giảm phát thải'),
('site_description', 'Website PHP & MySQL cho thương hiệu Gạo Vi Sinh: giới thiệu sản phẩm, truy xuất nguồn gốc và backend quản trị.'),
('hotline', 'Hotline: 0900 000 000'),
('email', 'hello@gaovisinh.vn')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Repair common Vietnamese text that may have been imported through a non-UTF-8 connection.
UPDATE products SET
    name = REPLACE(REPLACE(REPLACE(name, 'G?o', 'Gạo'), 'L?t', 'Lứt'), 'Th?m nh?', 'Thơm nhẹ'),
    badge = REPLACE(REPLACE(REPLACE(badge, 'Bán ch?y', 'Bán chạy'), '?n lành', 'Ăn lành'), 'Th?m nh?', 'Thơm nhẹ'),
    description = REPLACE(REPLACE(REPLACE(REPLACE(description, 'G?o', 'Gạo'), 'g?o', 'gạo'), 'truy xu?t ngu?n g?c', 'truy xuất nguồn gốc'), 'ti?t ki?m', 'tiết kiệm');

UPDATE posts SET
    title = REPLACE(REPLACE(REPLACE(title, 'Vì sao d?t kh?e', 'Vì sao đất khỏe'), 'mã truy xu?t', 'mã truy xuất'), 'bao g?o', 'bao gạo'),
    excerpt = REPLACE(REPLACE(REPLACE(excerpt, 'Canh tác vi sinh t?p trung', 'Canh tác vi sinh tập trung'), 'truy xu?t ngu?n g?c', 'truy xuất nguồn gốc'), 'Khách hàng có th?', 'Khách hàng có thể'),
    content = REPLACE(REPLACE(REPLACE(content, 'N?i dung', 'Nội dung'), 'qu?n lý', 'quản lý'), 'h? tr?', 'hỗ trợ');

UPDATE settings SET setting_value = REPLACE(REPLACE(REPLACE(setting_value, 'G?o', 'Gạo'), 'truy xu?t ngu?n g?c', 'truy xuất nguồn gốc'), 'qu?n tr?', 'quản trị');
