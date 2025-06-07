<?php

declare(strict_types=1);

require_once 'helpers.php';

/**
 * Возвращает список категорий
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @return array Список категорий в формате ассоциативного массива
 */
function get_all_categories(mysqli $conn): array
{
    return execute_query(
        $conn,
        <<<SQL
        SELECT name, slug
        FROM categories
        LIMIT 20
        SQL
    )->fetch_all(MYSQLI_ASSOC);
}

/**
 * Возвращает уникальный идентификатор категории по её символьному коду
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @param string $slug Символьной код
 * @return array Массив с идентификатором категории
 */
function get_category_by_slug(mysqli $conn, string $slug): array
{
    $result = execute_query(
        $conn,
        <<<SQL
        SELECT id
        FROM categories
        WHERE slug = ?
        SQL,
        [$slug],
    )->fetch_all(MYSQLI_ASSOC);

    return array_merge(...array_values($result));
}