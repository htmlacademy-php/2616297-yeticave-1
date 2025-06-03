<?php

declare(strict_types=1);

require_once 'helpers.php';

$config = require_once './config/autoload.php';

$conn = mysqli_connect(
    $config['db']['host'] ?? 'localhost',
    $config['db']['user'] ?? 'root',
    $config['db']['password'] ?? 'pw',
    $config['db']['name'] ?? 'database',
    $config['db']['port'] ?? 3306,
);

if ($conn === false) {
    exit_with_message('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
}

return $conn;