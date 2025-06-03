<?php

/**
 * @var bool $is_auth Флаг авторизации
 * @var string $user_name Имя пользователя
 */

declare(strict_types=1);

require_once 'helpers.php';
require_once 'data.php';
require_once 'models/category.php';
require_once 'models/lot.php';

$conn = require_once 'init.php';

$lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($lot_id === null) {
    $page_title = 'Ошибка';
    exit_with_message('Отсутствует параметр id', 400);
}

if (
    $lot_id === false
    || $lot_id < 1
) {
    $page_title = 'Ошибка';
    exit_with_message('Некорректный тип параметра запроса', 400);
}

$lot = get_lot_by_id($conn, $lot_id);

if (empty($lot)) {
    $page_title = 'Лот не найден';
    exit_with_message('Лот не найден', 404);
}

$page_title = $lot['name'];

$categories_list = get_all_categories($conn);

$page_content = include_template(
    'lot.php',
    [
        'lot' => $lot,
        'categories_list' => $categories_list,
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