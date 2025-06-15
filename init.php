<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'constants.php';

session_start();

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

$is_authorized = isset($_SESSION['user_id']);

$requested_page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (
    !$is_authorized
    && in_array($requested_page, PROTECTED_PAGES)
) {
    exit_with_message('Доступ запрещён', 403);
}

if (
    $is_authorized
    && in_array($requested_page, GUEST_ONLY_PAGES)
) {
    exit_with_message('Доступ запрещён', 403);
}

return [$is_authorized, $conn];