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
    $result = execute_query(
        $conn,
        <<<SQL
        SELECT name, slug
        FROM categories
        LIMIT 20
        SQL
    )->fetch_all(MYSQLI_ASSOC);

    return array_merge(...array_values($result));
}
