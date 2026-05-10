<?php

declare(strict_types=1);


ini_set('default_charset', 'UTF-8');

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function send_utf8_header(): void
{
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
}

function app_config(?string $key = null): mixed
{
    static $config = null;
    $config ??= require __DIR__ . '/config.php';

    return $key === null ? $config : ($config[$key] ?? null);
}

function scalar_input(mixed $value, string $default = ''): string
{
    if (is_scalar($value)) {
        return trim((string) $value);
    }

    return $default;
}

function e(mixed $value): string
{
    if ($value === null) {
        return '';
    }

    if (!is_scalar($value)) {
        return '';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money_vnd(int|float $amount): string
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

function redirect(string $path): never
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

function verify_csrf(): void
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
    $text = trim(mb_strtolower($text, 'UTF-8'));
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
