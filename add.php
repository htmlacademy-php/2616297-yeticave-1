<?php

/**
 * @var bool $is_auth Флаг авторизации
 * @var string $user_name Имя пользователя
 */

declare(strict_types=1);

require_once 'helpers.php';
require_once 'data.php';
require_once 'models/category.php';
require_once 'models/lot.php';
require_once 'validators.php';

$conn = require_once 'init.php';