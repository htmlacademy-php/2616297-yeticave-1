<?php

declare(strict_types=1);

require_once 'helpers.php';

/**
 * Добавляет нового пользователя в БД
 *
 * @param mysqli $conn Ресурс подключения к БД
 * @param array<int, string> $user Массив с данными пользователя
 * @return bool false при успешном добавлении
 */
function add_user(mysqli $conn, array $user): bool
{
    return execute_query(
        $conn,
        <<<SQL
        INSERT INTO users (email, first_name, password_hash, contact_info)
        VALUES (?, ?, ?, ?);
        SQL,
        $user,
    );
}

/**
 * Проверяет существование email в таблице users
 *
 * @param mysqli $conn Ресурс подключения к БД
 * @param string $email Email адрес для проверки
 * @return bool Возвращает true если email существует в базе, false если не существует
 */
function is_email_exists(mysqli $conn, string $email): bool
{
    $result = execute_query(
        $conn,
        <<<SQL
        SELECT 1
        FROM users
        WHERE email = ?;
        SQL,
        [$email],
    )->fetch_all(MYSQLI_ASSOC);

    return !empty($result);
}