<?php

declare(strict_types=1);

/**
 * Current application version displayed in the frontend.
 */
const APP_VERSION = '0.0.2';

require __DIR__ . '/helpers.php';
require __DIR__ . '/data-loader.php';

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
