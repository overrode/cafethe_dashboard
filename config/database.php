<?php

declare(strict_types=1);

function envValue(string $key, ?string $default = null): ?string
{
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        return $default;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$name, $value] = array_pad(explode('=', $line, 2), 2, null);

        if (trim($name) === $key) {
            return trim((string) $value);
        }
    }

    return $default;
}

return [
    'host' => envValue('DB_HOST', 'db'),
    'database' => envValue('DB_NAME', 'cafethe'),
    'username' => envValue('DB_USER', 'cafethe_user'),
    'password' => envValue('DB_PASSWORD', ''),
    'charset' => envValue('DB_CHARSET', 'utf8mb4'),
];
