<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/category.php';
require_once 'models/user.php';
require_once 'models/lot.php';

$conn = require_once 'init.php';

$is_auth = is_user_authorized($conn);

$user_name = get_user_name();
$page_title = 'Главная';

$lots_list = get_open_lots($conn);
$categories_list = get_all_categories($conn);

$lots_content = include_template(
    'lots.php',
    [
        'lots_list' => $lots_list,
    ],
);

$page_content = include_template(
    'main.php',
    [
        'categories_list' => $categories_list,
        'lots' => $lots_content,
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