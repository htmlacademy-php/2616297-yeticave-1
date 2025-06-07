<?php

declare(strict_types=1);

require_once 'helpers.php';

/**
 * Возвращает активных и открытых лотов
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @return array Список лотов в формате ассоциативного массива
 */
function get_open_lots(mysqli $conn): array
{
    return execute_query(
        $conn,
        <<<SQL
        SELECT l.id,
               l.name,
               l.start_price,
               MAX(b.buy_price) AS current_price,
               l.img_url,
               l.end_date,
               l.user_id,
               c.name           AS category_name,
               l.created_at
        FROM lots l
                 JOIN categories c on c.id = l.category_id
                 LEFT JOIN buy_orders b on l.id = b.lot_id
        WHERE l.winner_id IS NULL AND l.end_date > NOW()
        GROUP BY l.id, l.created_at
        ORDER BY l.created_at DESC
        LIMIT 9
        SQL
    )->fetch_all(MYSQLI_ASSOC);
}

/**
 * @param mysqli $conn Ресурс подключения в БД
 * @param int $lot_id Уникальный идентификатор лота
 * @return array Массив с информацией о конкретном лоте
 */
function get_lot_by_id(mysqli $conn, int $lot_id): array
{
    return execute_query(
        $conn,
        <<<SQL
        SELECT l.name,
               l.description,
               l.img_url,
               l.start_price,
               l.end_date,
               l.betting_step,
               l.user_id,
               l.winner_id,
               c.name AS category_name
        FROM lots l
                 JOIN categories c on c.id = l.category_id
        WHERE l.id = ?;
        SQL,
        [$lot_id],
    )->fetch_all(MYSQLI_ASSOC);
}

function add_lot(mysqli $conn, array $lot_data): int|string
{
    execute_query(
        $conn,
        <<<SQL
        INSERT INTO lots (name, description, img_url, start_price, end_date, betting_step, user_id, category_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?);
        SQL,
        $lot_data,
    );

    return mysqli_insert_id($conn);
}

function get_category_by_slug(mysqli $conn, string $slug): array
{
    return execute_query(
        $conn,
        <<<SQL
        SELECT id
        FROM categories
        WHERE slug = ?
        SQL,
        [$slug],
    )->fetch_all(MYSQLI_ASSOC);
}