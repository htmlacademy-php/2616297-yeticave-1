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

$lot_id = (int)($_GET['id'] ?? 0);
$categories_list = get_all_categories($conn);
$categories_header = include_template(
    'categories-header.php',
    [
        'categories_list' => $categories_list,
    ],
);
$lot = get_lot_by_id($conn, $lot_id);

if (empty($lot)) {
    $page_title = 'Лот не найден';
    exit_with_message('Лот не найден', 404);
}

$page_title = $lot['name'] ?? '';

$is_not_expired = false;
$lot_end_date = $lot['end_date'] ?? null;

if (!is_null($lot_end_date)) {
    $dt_range = get_dt_range($lot_end_date);

    if (
        $dt_range['minutes'] !== 0
        || $dt_range['hours'] !== 0
    ) {
        $is_not_expired = true;
    }
}

$is_not_owned_by_current_user = get_user_id() !== $lot['user_id'];

$is_last_bid_made_by_another_user = true;
$current_bid_user_id = $lot['current_bid_user'] ?? null;

if (
    !is_null($current_bid_user_id)
    && get_user_id() === $current_bid_user_id
) {
    $is_last_bid_made_by_another_user = false;
}

$is_authorized_to_place_bid = false;

if (
    $is_auth
    && $is_not_expired
    && $is_not_owned_by_current_user
    && $is_last_bid_made_by_another_user
) {
    $is_authorized_to_place_bid = true;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $page_content = include_template(
        'lot.php',
        [
            'lot' => $lot,
            'categories_header' => $categories_header,
            'is_authorized_to_place_bid' => $is_authorized_to_place_bid,
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
            'categories_header' => $categories_header,
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

add_new_bid($conn, $lot_id, $new_bid_price, $user_id);

header('Location: /lot.php?id=' . $lot_id);
exit();