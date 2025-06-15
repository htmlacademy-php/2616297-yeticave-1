<?php

declare(strict_types=1);

require_once 'helpers.php';

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