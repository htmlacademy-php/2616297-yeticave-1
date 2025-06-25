<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'validators.php';

$conn = require_once 'init.php';

$is_auth = is_authorized();

$user_name = get_user_name();

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

$lot_id = (int)($_GET['id'] ?? 0);

$categories_list = get_all_categories($conn);

$errors = [];

$lot = get_lot_by_id($conn, $lot_id);

if (empty($lot)) {
    $page_title = 'Лот не найден';
    exit_with_message('Лот не найден', 404);
}

$page_title = $lot['name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $page_content = include_template(
        'lot.php',
        [
            'lot' => $lot,
            'categories_list' => $categories_list,
            'is_auth' => $is_auth,
            'errors' => $errors ?? [],
            'form_data' => $_POST,
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
    die();
}

$errors = validate(
    $_POST,
    [
        'cost' => ['required', 'valid_integer', greater_or_equal($lot['min_bid_price'])],
    ],
);

if (!empty($errors)) {
    http_response_code(400);

    $page_content = include_template(
        'lot.php',
        [
            'lot' => $lot,
            'categories_list' => $categories_list,
            'is_auth' => $is_auth,
            'errors' => $errors ?? [],
            'form_data' => $_POST,
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
    die();
}

$new_bid_price = (int)$_POST['cost'];
$user_id = get_user_id();

add_new_bid($conn, $lot, $lot_id, $new_bid_price, $user_id);

header('Location: /lot.php?id=' . $lot_id);
exit();