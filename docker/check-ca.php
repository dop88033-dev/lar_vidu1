<?php

/**
 * Validates the MySQL SSL CA certificate environment setup.
 */

$caPath = getenv('MYSQL_ATTR_SSL_CA');

if (empty($caPath)) {
    echo "MYSQL_ATTR_SSL_CA is not set. Skipping SSL CA check.\n";
    exit(0);
}

if (!file_exists($caPath)) {
    fwrite(STDERR, "Error: SSL CA file does not exist at '{$caPath}'.\n");
    exit(1);
}

if (!is_readable($caPath)) {
    fwrite(STDERR, "Error: SSL CA file at '{$caPath}' is not readable.\n");
    exit(1);
}

echo "MySQL SSL CA certificate path verified: {$caPath}\n";
exit(0);
