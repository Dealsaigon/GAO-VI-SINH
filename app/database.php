<?php

declare(strict_types=1);

function db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/config.php';
    $db = $config['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['name'],
        $db['charset']
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = sprintf('SET NAMES %s COLLATE utf8mb4_unicode_ci', $db['charset']);
    }

    $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);
    $pdo->exec(sprintf('SET NAMES %s COLLATE utf8mb4_unicode_ci', $db['charset']));
    $pdo->exec(sprintf('SET CHARACTER SET %s', $db['charset']));

    return $pdo;
}
