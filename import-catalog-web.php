<?php

declare(strict_types=1);

require __DIR__ . '/includes/database/DatabaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

DatabaseConnection::loadEnvironment();
$configuredToken = DatabaseConnection::getEnvironment('IMPORT_TOKEN');
$requestToken = $_SERVER['HTTP_X_IMPORT_TOKEN'] ?? '';

if (
    $configuredToken === false
    || $configuredToken === ''
    || !is_string($requestToken)
    || !hash_equals($configuredToken, $requestToken)
) {
    http_response_code(404);
    exit;
}

define('CATALOG_IMPORT_WEB', true);
require __DIR__ . '/bin/import-catalog.php';
