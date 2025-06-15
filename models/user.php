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

function find_user_by_email(mysqli $conn, string $email): array
{
    $result = execute_query(
        $conn,
        <<<SQL
        SELECT id, email, first_name, password_hash
        FROM users
        WHERE email = ?;
        SQL,
        [$email],
    )->fetch_all(MYSQLI_ASSOC);

    return array_merge(...array_values($result));
}

function authenticate_user(mysqli $conn, string $email, string $password): array
{
    $user = find_user_by_email($conn, $email);

    if (!password_verify($password, $user['password_hash'])) {
        return [
            'errors' => [
                'password' => [
                    'Неверный пароль',
                ],
            ],
        ];
    }

    return [
        'errors' => [],
        'user_data' => [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['first_name'],
        ]
    ];
}