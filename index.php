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

$page_title = 'Главная';

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
        'page_title' => $page_title,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_content' => $page_content,
    ],
);

print($html_result);