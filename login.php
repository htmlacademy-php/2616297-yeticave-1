<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'models/user.php';
require_once 'validators.php';

[$is_auth, $conn] = require_once 'init.php';

$page_title = 'Войти';
$user_name = $_SESSION['name'] ?? null;

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

$_SESSION = array_merge($_SESSION, $result['user_data']);

header('Location: /');
die();