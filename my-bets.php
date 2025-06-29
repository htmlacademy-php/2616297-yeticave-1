<?php

declare(strict_types=1);

$conn = require_once 'init.php';

require_once 'models/category.php';
require_once 'models/user.php';
require_once 'models/lot.php';

$is_auth = is_user_authorized($conn);
$user_name = get_user_name();
$page_title = 'Мои ставки';

if ($is_auth === false) {
    exit_with_message('Доступ запрещён', 403);
}

$bids = get_user_bids($conn, get_user_id());
$categories_list = get_all_categories($conn);
$categories_header = include_template(
    'categories-header.php',
    [
        'categories_list' => $categories_list,
    ],
);

$page_content = include_template(
    'my-bets.php',
    [
        'categories_header' => $categories_header,
        'bids' => $bids,
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