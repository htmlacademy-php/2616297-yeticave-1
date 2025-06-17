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
    $result = execute_query(
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

    return array_merge(...array_values($result));
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