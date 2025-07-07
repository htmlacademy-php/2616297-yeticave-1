<?php

declare(strict_types=1);

/**
 * @var mysqli $conn Ресурс подключения к БД
 * @var array $config Массив с данными о конфигурации приложения
 */

require_once 'helpers.php';
require_once 'models/lot.php';

assign_winners($conn);
$pending_emails = find_users_with_pending_email($conn);

if (empty($pending_emails)) {
    return;
}

$mail_settings = [
    'user' => $config['smtp']['username'],
    'pass' => $config['smtp']['password'],
    'host' => $config['smtp']['host'],
    'port' => $config['smtp']['port'],
];

$stmt = db_get_prepare_stmt(
    $conn,
    <<<SQL
    UPDATE lots
    SET win_email_sent = ?
    WHERE id = ?
    SQL,
);

foreach ($pending_emails as $lot) {
    $email_content = include_template(
        'email.php',
        [
            'project_url' => $config['app']['url'],
            'user_name' => $lot['first_name'] ?? '',
            'lot_id' => $lot['id'] ?? 0,
            'lot_name' => $lot['name'] ?? '',
        ],
    );

    $email_delivery_status = send_email(
        $mail_settings,
        $lot['email'] ?? '',
        'Ваша ставка победила',
        $email_content,
        'text/html; charset=utf-8'
    );

    $id = $lot['id'] ?? 0;
    $stmt->bind_param("ii", $email_delivery_status, $id);
    $stmt->execute();
}