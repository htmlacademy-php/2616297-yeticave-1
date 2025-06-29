<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'models/user.php';
require_once 'validators.php';

$conn = require_once 'init.php';

$is_auth = is_user_authorized($conn);

if ($is_auth === true) {
    exit_with_message('Доступ запрещён', 403);
}

$user_name = get_user_name();
$page_title = 'Регистрация';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validate(
        array_merge_recursive($_POST, $_FILES),
        [
            'email' => ['required', 'valid_email', unique_email($conn)],
            'password' => ['required'],
            'name' => ['required', character_limit(129)],
            'message' => ['required', character_limit(256)],
        ],
    );

    if (empty($errors)) {
        add_user(
            $conn,
            [
                $_POST['email'],
                $_POST['name'],
                password_hash($_POST['password'], PASSWORD_BCRYPT),
                $_POST['message'],
            ]
        );

        header('Location: /login.php');
        die();
    }
}

$categories_list = get_all_categories($conn);

$page_content = include_template(
    'sign-up.php',
    [
        'categories_list' => $categories_list,
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