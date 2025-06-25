<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/category.php';

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
    $lot = execute_query(
        $conn,
        <<<SQL
        SELECT l.name,
               l.description,
               l.img_url,
               l.start_price as current_price,
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

    $lot = array_merge(...array_values($lot));

    if (empty($lot)) {
        return $lot;
    }

    $current_price = execute_query(
        $conn,
        <<<SQL
        SELECT MAX(buy_price) AS current_price
        FROM buy_orders
        WHERE lot_id = ?;
        SQL,
        [$lot_id],
    )->fetch_row()[0];

    $lot_has_bid = $current_price !== null;

    if ($lot_has_bid) {
        $lot['current_price'] = $current_price;
    }

    $min_betting_step = 1;
    $min_bid_price = $lot['current_price'] + $lot['betting_step'] + $min_betting_step;

    $lot['min_bid_price'] = $min_bid_price;

    return $lot;
}

/**
 * Добавляет новый лот
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @param array $fields Текстовые данные
 * @param array $img_file Массив с данными фотографии
 * @param int $user_id Идентификатор пользователя, создателя лота
 * @return int|string Уникальный идентификатор добавленного лота
 */
function add_lot(mysqli $conn, array $fields, array $img_file, int $user_id): int|string
{
    $category_id = get_category_by_slug($conn, $fields['category']);
    $file_upload = upload_file($img_file['name'], $img_file['tmp_name'], 'img-');

    if (($file_upload['status'] ?? false) === false) {
        exit_with_message('Произошла ошибка на стороне сервера, попробуйте позже.', 500);
    }

    $lot_data = [
        $fields['lot-name'],
        $fields['message'],
        $file_upload['file_path'] ?? '',
        $fields['lot-rate'],
        $fields['lot-date'],
        $fields['lot-step'],
        1,
        $category_id['id'],
    ];

    execute_query(
        $conn,
        <<<SQL
        INSERT INTO lots (name, description, img_url, start_price, end_date, betting_step, user_id, category_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?);
        SQL,
        $lot_data,
    );

    $new_lot_id = mysqli_insert_id($conn);

    if ($new_lot_id === 0) {
        exit_with_message('Произошла ошибка на стороне сервера, попробуйте позже.', 500);
    }

    return $new_lot_id;
}

/**
 * Производит поиск лотов по запросу
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @param string $search_query Строка запроса
 * @param int $page_limit Лимит лотов для поиска
 * @param int $current_page Номер текущей страницы
 * @return array Список найденных лотов вместе с информацией о других страницах
 */
function find_lots_by(
    mysqli $conn,
    string $search_query,
    int $page_limit = 9,
    int $current_page = 1,
): array {
    $offset = get_current_page_offset($page_limit, $current_page);

    $result = execute_query(
        $conn,
        <<<SQL
        SELECT l.id,
               l.name,
               l.start_price,
               l.img_url,
               l.end_date,
               c.name           AS category_name
        FROM lots l
                 JOIN categories c on c.id = l.category_id
                 LEFT JOIN buy_orders b on l.id = b.lot_id
        WHERE MATCH (l.name, l.description) AGAINST (?)
        GROUP BY l.id, l.created_at
        LIMIT ?
        OFFSET ?
        SQL,
        [
            $search_query,
            $page_limit,
            $offset,
        ],
    )->fetch_all(MYSQLI_ASSOC);

    $total = (int)execute_query(
        $conn,
        <<<SQL
        SELECT COUNT(*) as total
        FROM lots l
                 JOIN categories c on c.id = l.category_id
                 LEFT JOIN buy_orders b on l.id = b.lot_id
        WHERE MATCH (l.name, l.description) AGAINST (?)
        SQL,
        [$search_query],
    )->fetch_row()[0];

    $links = calculate_pager_state(
        $page_limit,
        $current_page,
        $total,
    );

    return [
        'lots' => $result,
        'pager_content' => $links,
    ];
}

function add_new_bid(mysqli $conn, array $updated_lot, int $lot_id, int $bid_price, int $user_id): void
{
    execute_query(
        $conn,
        <<<SQL
        INSERT INTO buy_orders (buy_price, user_id, lot_id)
        VALUES (?, ?, ?);
        SQL,
        [
            $bid_price,
            $user_id,
            $lot_id,
        ],
    );

    $new_bid_id = mysqli_insert_id($conn);
    $is_bid_added = $new_bid_id !== 0;

    if (!$is_bid_added) {
        exit_with_message('Произошла ошибка на стороне сервера, попробуйте позже.', 500);
    }
}