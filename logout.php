<?php

declare(strict_types=1);

require_once 'init.php';

session_destroy();
header('Location: /');
die();