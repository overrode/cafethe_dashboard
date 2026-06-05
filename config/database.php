<?php

declare(strict_types=1);

/**
 * Retrieves the value of an environment variable from a `.env` file.
 *
 * @param string $key The name of the environment variable to retrieve.
 * @param string|null $default The default value to return if the variable is not found or the file does not exist.
 * @return string|null The value of the environment variable, or the default value if the variable is not found.
 */
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
    'host' => envValue('DB_HOST', ''),
    'database' => envValue('DB_NAME', ''),
    'username' => envValue('DB_USER', ''),
    'password' => envValue('DB_PASSWORD', ''),
    'charset' => envValue('DB_CHARSET', ''),
];
