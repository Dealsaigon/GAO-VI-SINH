<?php

declare(strict_types=1);


ini_set('default_charset', 'UTF-8');

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function send_utf8_header()
{
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
}

function app_config($key = null)
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    return $key === null ? $config : ($config[$key] ?? null);
}


function repair_vietnamese_text(string $text): string
{
    $replacements = [
        'G?o' => 'Gạo',
        'g?o' => 'gạo',
        'L?t' => 'Lứt',
        'l?t' => 'lứt',
        '?n lành' => 'Ăn lành',
        '?n s?ch' => 'Ăn sạch',
        'Bán ch?y' => 'Bán chạy',
        'Th?m nh?' => 'Thơm nhẹ',
        'S?n ph?m' => 'Sản phẩm',
        's?n ph?m' => 'sản phẩm',
        'Qu?n tr?' => 'Quản trị',
        'qu?n tr?' => 'quản trị',
        'T?ng quan' => 'Tổng quan',
        'C?u hình' => 'Cấu hình',
        'Bài vi?t' => 'Bài viết',
        'Ðon hàng' => 'Đơn hàng',
        'Đon hàng' => 'Đơn hàng',
        '?ơn hàng' => 'Đơn hàng',
        'D? li?u' => 'Dữ liệu',
        'd? li?u' => 'dữ liệu',
        'truy xu?t' => 'truy xuất',
        'Truy xu?t' => 'Truy xuất',
        'ngu?n g?c' => 'nguồn gốc',
        'Ngu?n g?c' => 'Nguồn gốc',
        'Liên h?' => 'Liên hệ',
        'liên h?' => 'liên hệ',
        'M?t kh?u' => 'Mật khẩu',
        'm?t kh?u' => 'mật khẩu',
        'Không dúng' => 'Không đúng',
        'không dúng' => 'không đúng',
        'Sóc Tr?ng' => 'Sóc Trăng',
        'S?c Tr?ng' => 'Sóc Trăng',
        'Ðông Xuân' => 'Đông Xuân',
        'Đông Xuân' => 'Đông Xuân',
        'H?p tác xã' => 'Hợp tác xã',
        'h?p tác xã' => 'hợp tác xã',
    ];

    return strtr($text, $replacements);
}

function scalar_input($value, string $default = ''): string
{
    if (is_scalar($value)) {
        return repair_vietnamese_text(trim((string) $value));
    }

    return $default;
}

function e($value): string
{
    if ($value === null) {
        return '';
    }

    if (!is_scalar($value)) {
        return '';
    }

    $text = repair_vietnamese_text((string) $value);

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function money_vnd($amount): string
{
    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function url(string $path = ''): string
{
    $base = rtrim((string) app_config('base_url'), '/');
    $path = ltrim($path, '/');

    if ($base === '') {
        return '/' . $path;
    }

    return $base . '/' . $path;
}

function redirect(string $path)
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function verify_csrf()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Phiên bảo mật đã hết hạn. Vui lòng tải lại trang.');
    }
}

function slugify(string $text): string
{
    $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    $text = trim($text);
    $map = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d',
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?: '';

    return trim($text, '-') ?: 'bai-viet';
}
