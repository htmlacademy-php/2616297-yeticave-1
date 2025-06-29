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

$page_title = 'Войти';
$user_name = get_user_name();

$categories_list = get_all_categories($conn);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $page_content = include_template(
        'login.php',
        [
            'categories_list' => $categories_list,
            'errors' => $errors,
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
    array_merge_recursive($_POST, $_FILES),
    [
        'email' => ['required', 'valid_email', email_exists($conn)],
        'password' => ['required'],
    ],
);

if (!empty($errors)) {
    $page_content = include_template(
        'login.php',
        [
            'categories_list' => $categories_list,
            'errors' => $errors,
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

$result = authenticate_user($conn, $_POST['email'], $_POST['password']);
$errors = $result['errors'] ?? [];

if (!empty($errors)) {
    $page_content = include_template(
        'login.php',
        [
            'categories_list' => $categories_list,
            'errors' => $errors,
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

$_SESSION['user_data'] = $result['user_data'];

header('Location: /');
die();