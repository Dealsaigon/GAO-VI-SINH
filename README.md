# Gạo Vi Sinh - Website PHP & MySQL

Dự án đã được bổ sung bộ mã nguồn PHP thuần và SQL để phát triển website giới thiệu sản phẩm gạo vi sinh, kèm backend quản trị nội dung.

## Chức năng chính

- Giao diện khách hàng tại `public/index.php`:
  - Trang hero, danh sách sản phẩm, quy trình canh tác, khu vực truy xuất mã lô, tin tức và liên hệ.
  - Dữ liệu sản phẩm, bài viết và cấu hình website đọc từ MySQL thông qua PDO.
- Backend quản trị tại `admin/`:
  - Đăng nhập quản trị bằng session và `password_verify`.
  - Dashboard thống kê sản phẩm, đơn hàng và doanh thu.
  - CRUD sản phẩm và bài viết.
  - Cập nhật trạng thái/ghi chú đơn hàng.
  - Cập nhật cấu hình website.
- Cơ sở dữ liệu tại `database/schema.sql`:
  - Bảng `admins`, `products`, `posts`, `orders`, `trace_batches`, `settings`.
  - Dữ liệu mẫu để chạy thử ngay sau khi import.

## Yêu cầu môi trường

- PHP 8.1+
- MySQL 8+ hoặc MariaDB tương thích JSON
- Extension PHP PDO MySQL

## Cài đặt nhanh

1. Tạo database và import dữ liệu mẫu:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

2. Cấu hình kết nối database bằng biến môi trường nếu khác giá trị mặc định:

   ```bash
   export DB_HOST=127.0.0.1
   export DB_PORT=3306
   export DB_NAME=gao_vi_sinh
   export DB_USER=root
   export DB_PASS=''
   export APP_URL=http://localhost:8000
   ```

3. Chạy website bằng PHP built-in server từ thư mục gốc dự án:

   ```bash
   php -S localhost:8000
   ```

4. Truy cập:

   - Website: <http://localhost:8000/public/index.php>
   - Quản trị: <http://localhost:8000/admin/login.php>

## Tài khoản quản trị mẫu

- Email: `admin@gaovisinh.vn`
- Mật khẩu: `Admin@123`

> Khuyến nghị đổi mật khẩu ngay sau khi triển khai thật.

## Ghi chú về mã nguồn React cũ

Các tệp React/Vite ban đầu vẫn được giữ lại trong `src/` để tham khảo giao diện. Phần PHP/MySQL mới nằm trong `app/`, `public/`, `admin/` và `database/`.
