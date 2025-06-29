<?php

declare(strict_types=1);

$conn = require_once 'init.php';

require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'models/user.php';
require_once 'validators.php';

$is_auth = is_user_authorized($conn);

if ($is_auth === false) {
    exit_with_message('Доступ запрещён', 403);
}

$user_name = get_user_name();
$page_title = 'Добавить новый лот';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hours_in_day = 24;

    $errors = validate(
        array_merge_recursive($_POST, $_FILES),
        [
            'lot-name' => ['required', character_limit(128)],
            'category' => ['required'],
            'message' => ['required', character_limit(512)],
            'lot-img' => ['required', mime_type_in(['image/png', 'image/jpeg', 'image/jpg'])],
            'lot-rate' => ['required', 'valid_integer', greater_than(0)],
            'lot-step' => ['required', 'valid_integer', greater_than(0)],
            'lot-date' => ['required', 'date_convertable', hours_after_now($hours_in_day)],
        ],
    );

    if (empty($errors)) {
        $lot_id = add_lot(
            $conn,
            $_POST,
            $_FILES['lot-img'],
            get_user_id(),
        );

        header("Location: lot.php?id=$lot_id");
        die();
    }
}

$categories_list = get_all_categories($conn);
$categories_header = include_template(
    'categories-header.php',
    [
        'categories_list' => $categories_list,
    ],
);

$page_content = include_template(
    'add.php',
    [
        'categories_header' => $categories_header,
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