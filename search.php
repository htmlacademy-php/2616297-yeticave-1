<?php

declare(strict_types=1);

$conn = require_once 'init.php';

require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'models/user.php';
require_once 'validators.php';

$is_auth = is_user_authorized($conn);
$user_name = get_user_name();
$lots = [];
$search_query = '';

$validation = validate(
    $_GET,
    [
        'search' => ['required'],
        'page' => ['valid_integer'],
    ],
);

$categories_list = get_all_categories($conn);
$categories_header = include_template(
    'categories-header.php',
    [
        'categories_list' => $categories_list,
    ],
);

if (!empty($validation)) {
    http_response_code(400);

    $page_content = include_template(
        'search.php',
        [
            'search_query' => $search_query,
            'lots_list' => $lots,
            'categories_header' => $categories_header,
            'is_auth' => $is_auth,
        ],
    );

    $html_result = include_template(
        'layout.php',
        [
            'categories_list' => $categories_list,
            'page_title' => 'Ничего не найдено по вашему запросу',
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'page_content' => $page_content,
        ],
    );

    print($html_result);
    die();
}

$search_query = htmlspecialchars(trim($_GET['search']));
$page_title = "Результаты поиска по запросу «{$search_query}»";
$page_limit = 9;
$current_page = (int)($_GET['page'] ?? 1);
$page_data = find_lots_by($conn, $search_query, $page_limit, $current_page);
$lots = $page_data['lots'];
$pager_content = $page_data['pager_content'];

if (empty($lots)) {
    http_response_code(404);
}

$lots_content = include_template(
    'lots.php',
    [
        'lots_list' => $lots,
    ],
);

$page_content = include_template(
    'search.php',
    [
        'search_query' => $search_query,
        'lots' => $lots_content,
        'categories_header' => $categories_header,
        'is_auth' => $is_auth,
        'pager' => $pager_content,
    ],
);

$html_result = include_template(
    'layout.php',
    [
        'search_query' => $search_query,
        'categories_list' => $categories_list,
        'page_title' => $page_title,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_content' => $page_content,
    ],
);

print($html_result);