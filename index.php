<?php

/**
 * @var string[] $categories_list Список категорий
 * @var array<int,array{name: string, category: string, price: int, img: string} $lots_list Список лотов
 * @var bool $is_auth Флаг авторизации
 * @var string $user_name Имя пользователя
 */

declare(strict_types=1);

require_once 'helpers.php';
require_once 'data.php';
$config = require_once './config/autoload.php';

$page_title = 'Главная';

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

$lots_list = execute_query(
    $conn,
    <<<SQL
    SELECT l.id,
           l.name,
           l.start_price,
           MAX(b.buy_price) AS current_price,
           l.img_url,
           l.end_date,
           l.user_id,
           c.name           AS category_name,
           l.created_at
    FROM lots l
             JOIN categories c on c.id = l.category_id
             LEFT JOIN buy_orders b on l.id = b.lot_id
    WHERE l.winner_id IS NULL AND l.end_date > NOW()
    GROUP BY l.id, l.created_at
    ORDER BY l.created_at DESC
    LIMIT 9
    SQL
);

$categories_list = execute_query(
    $conn,
    <<<SQL
    SELECT name, slug
    FROM categories
    LIMIT 20
    SQL
);

$page_content = include_template(
    'main.php',
    [
        'categories_list' => $categories_list,
        'lots_list' => $lots_list,
    ],
);

$html_result = include_template(
    'layout.php',
    [
        'categories_list' => $categories_list,
        'page_title' => $page_title,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_content' => $page_content,
    ],
);

print($html_result);