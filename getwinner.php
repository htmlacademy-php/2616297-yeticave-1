<?php

declare(strict_types=1);

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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

$user = urlencode($config['smtp']['username']);
$pass = urlencode($config['smtp']['password']);
$host = $config['smtp']['host'];
$port = $config['smtp']['port'];
$dsn_string = sprintf('smtp://%s:%s@%s:%s', $user, $pass, $host, $port);
$transport = Transport::fromDsn($dsn_string);
$mailer = new Mailer($transport);
$site_url = 'http://' . $_SERVER['SERVER_NAME'];

$stmt = db_get_prepare_stmt(
    $conn,
    <<<SQL
    UPDATE lots
    SET win_email_sent = ?
    WHERE id = ?
    SQL,
    [EMAIL_DELIVERY_SUCCESS, 0],
);

foreach ($pending_emails as $lot) {
    $email_content = include_template(
        'email.php',
        [
            'project_url' => $site_url,
            'user_name' => $lot['first_name'] ?? '',
            'lot_id' => $lot['id'] ?? 0,
            'lot_name' => $lot['name'] ?? '',
        ],
    );

    $is_email_sent = EMAIL_DELIVERY_SUCCESS;

    $email = (new Email())
        ->from($user)
        ->to($lot['email'] ?? '')
        ->subject('Ваша ставка победила')
        ->html($email_content);

    $email->getHeaders()->addTextHeader('Content-Type', 'text/html; charset=utf-8');

    try {
        $mailer->send($email);
    } catch (TransportExceptionInterface $e) {
        $is_email_sent = EMAIL_DELIVERY_FAILURE;
    }

    if ($is_email_sent === EMAIL_DELIVERY_SUCCESS) {
        $id = $lot['id'] ?? 0;
        $stmt->bind_param("ii", $is_email_sent, $id);
        $stmt->execute();
    }
}