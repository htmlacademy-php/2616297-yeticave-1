<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'constants.php';

$is_session_started = session_start();

if (!$is_session_started) {
    exit_with_message(
        'Возникла техническая ошибка. Пожалуйста, попробуйте обновить страницу или зайти позже.',
        500
    );
}

if (!setlocale(LC_ALL, 'ru_RU.UTF-8')) {
    exit_with_message('Не удалось установить локаль', 500);
}

if (!date_default_timezone_set('UTC')) {
    exit_with_message('Не удалось установить часовой пояс', 500);
}

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