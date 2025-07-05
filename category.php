<?php

declare(strict_types=1);

$conn = require_once 'init.php';

require_once 'models/category.php';
require_once 'models/user.php';
require_once 'models/lot.php';
require_once 'validators.php';

$is_auth = is_user_authorized($conn);
$user_name = get_user_name();
$errors = [];

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

$category_id = (int)($_GET['id'] ?? 0);
$categories_list = get_all_categories($conn);

$categories_header = include_template(
    'categories-header.php',
    [
        'categories_list' => $categories_list,
    ],
);

$lots = find_lots_by_category($conn, $category_id);
$category_name = get_category_name_by_id($conn, $category_id);
$page_heading = is_null($category_name) ? 'Категория не найдена' : "Все лоты в категории  «{$category_name}»";

$lots_content = include_template(
    'lots.php',
    [
        'lots_list' => $lots['lots'],
    ],
);

$page_content = include_template(
    'category.php',
    [
        'heading' => $page_heading,
        'lots' => $lots_content,
        'categories_header' => $categories_header,
        'is_auth' => $is_auth,
        'pager' => $lots['pager_content'],
    ],
);

$html_result = include_template(
    'layout.php',
    [
        'categories_list' => $categories_list,
        'page_title' => $page_heading,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_content' => $page_content,
    ],
);

print_r($html_result);