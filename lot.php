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
require_once 'validators.php';

$conn = require_once 'init.php';

$validation = validate(
    $_GET,
    [
        'id' => ['required', 'valid_integer', greater_than(0)],
    ],
);

if (!empty($validation)) {
    $error_msg = '';

    foreach ($validation as $key => $value) {
        $error_msg .= "Значение $key содержит ошибки: " . implode(', ', $value) . '<br>';
    }

    exit_with_message($error_msg, 400);
}

$lot_id = (int) ($_GET['id'] ?? 0);

$lot = get_lot_by_id($conn, $lot_id);

if (empty($lot)) {
    $page_title = 'Лот не найден';
    exit_with_message('Лот не найден', 404);
}

$flatten_lot = array_merge(...array_values($lot));

$page_title = $flatten_lot['name'] ?? '';

$categories_list = get_all_categories($conn);

$page_content = include_template(
    'lot.php',
    [
        'lot' => $flatten_lot,
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