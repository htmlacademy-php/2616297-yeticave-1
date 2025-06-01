<?php

declare(strict_types=1);

$config_example = require_once __DIR__ . '/config.example.php';
$config_local_path = __DIR__ . '/config.local.php';
$config_local = file_exists($config_local_path)
    ? require_once $config_local_path
    : [];

$config_result = array_replace_recursive($config_example, $config_local);

return $config_result;