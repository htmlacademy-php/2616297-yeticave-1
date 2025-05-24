<?php

declare(strict_types=1);

require_once 'helpers.php';

/**
 * Форматирует цену добавляя разделители для тысяч и символ рубля
 *
 * @param int $price Не отформатированная цена
 * @return string Отформатированная цена с символом рубля
 */
function format_price(int $price): string
{
    $formatted_price = number_format($price, 0, '', ' ');
    return "$formatted_price ₽";
}

$page_title = 'Главная';
$is_auth = rand(0, 1);
$user_name = 'Артём';
$categories_list = [
    'Доски и лыжи',
    'Крепления',
    'Ботинки',
    'Одежда',
    'Инструменты',
    'Разное',
];
$lots_list = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'img' => '/img/lot-1.jpg',
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'img' => '/img/lot-2.jpg',
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'img' => '/img/lot-3.jpg',
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'img' => '/img/lot-4.jpg',
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'img' => '/img/lot-5.jpg',
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'img' => '/img/lot-6.jpg',
    ],
];

$page_content = include_template(
    'main.php',
    [
        'categories_list' => $categories_list,
        'lots_list' => $lots_list,
    ],
);

$html_result = include_template(
    'layout.php',
    [
        'page_title' => $page_title,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_content' => $page_content,
    ],
);

print($html_result);