<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Auth Class
 */
class Auth
{
    /**
     * @return array|null
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * @return bool
     */
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * @return int|null
     */
    public static function id(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    /**
     * @return string|null
     */
    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    /**
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }
}