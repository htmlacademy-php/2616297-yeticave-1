<?php

declare(strict_types=1);

$config = require_once './config/autoload.php';

$conn = mysqli_connect(
    $config['db']['host'] ?? 'localhost',
    $config['db']['user'] ?? 'root',
    $config['db']['password'] ?? 'pw',
    $config['db']['name'] ?? 'database',
    $config['db']['port'] ?? 3306,
);

if ($conn === false) {
    http_response_code(500);
    die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
}

return $conn;