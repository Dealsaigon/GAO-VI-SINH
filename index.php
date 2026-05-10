<?php

declare(strict_types=1);


$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && rtrim($requestPath, '/') === '/index.php') {
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: /' . ($queryString !== '' ? '?' . $queryString : ''), true, 301);
    exit;
}

$frontendCandidates = [
    __DIR__ . '/app/frontend.php',
    dirname(__DIR__) . '/app/frontend.php',
];

foreach ($frontendCandidates as $frontendFile) {
    if (is_file($frontendFile)) {
        require_once $frontendFile;
        render_frontend('public/assets/style.css', 'admin/login.php');
        return;
    }
}

http_response_code(500);
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thiếu mã nguồn website</title>
</head>
<body style="font-family:Arial,sans-serif;line-height:1.6;padding:40px">
    <h1>Thiếu thư mục <code>app/</code></h1>
    <p>Hosting chưa tìm thấy file <code>app/frontend.php</code>. Vui lòng upload đầy đủ thư mục <code>app/</code> vào cùng cấp với file <code>index.php</code> trong <code>public_html</code>.</p>
</body>
</html>
