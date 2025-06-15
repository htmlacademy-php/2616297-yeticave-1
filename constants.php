<?php

declare(strict_types=1);

/**
 * Список часто используемых mime типов и соответствующих им расширений файлов
 */
const MIME_MAP = [
    'image/jpeg' => 'jpeg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/svg+xml' => 'svg',
    'image/webp' => 'webp',
    'image/bmp' => 'bmp',
    'image/x-icon' => 'ico',
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/vnd.ms-excel' => 'xls',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    'application/vnd.ms-powerpoint' => 'ppt',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
    'text/plain' => 'txt',
    'text/csv' => 'csv',
    'application/json' => 'json',
    'application/xml' => 'xml',
    'audio/mpeg' => 'mp3',
    'audio/ogg' => 'ogg',
    'audio/wav' => 'wav',
    'audio/webm' => 'weba',
    'video/mp4' => 'mp4',
    'video/ogg' => 'ogv',
    'video/webm' => 'webm',
    'video/x-msvideo' => 'avi',
    'video/quicktime' => 'mov',
    'application/zip' => 'zip',
    'application/x-rar-compressed' => 'rar',
    'application/x-7z-compressed' => '7z',
    'application/x-tar' => 'tar',
    'application/gzip' => 'gz',
    'text/html' => 'html',
    'text/css' => 'css',
    'application/javascript' => 'js',
    'application/x-httpd-php' => 'php',
];

/**
 * Список страниц где для доступа необходима авторизация
 */
const PROTECTED_PAGES = [
    '/add.php',
];

/**
 * Список страниц с доступом для не авторизированных пользователй
 */
const GUEST_ONLY_PAGES = [
    '/sign-up.php',
];