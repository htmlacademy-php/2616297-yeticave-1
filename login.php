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
require_once 'models/user.php';
require_once 'validators.php';

$conn = require_once 'init.php';

$page_title = 'Войти';

$categories_list = get_all_categories($conn);

$page_content = include_template(
    'login.php',
    [
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