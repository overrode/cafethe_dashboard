<?php
declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Logger Class
 */
class Logger
{
    /**
     * @var string
     */
    private const LOG_FILE = ROOT_PATH . '/app.log';

    /**
     * Log an informational message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info(
        string $message,
        array $context = []
    ): void {
        self::write(
            'INFO',
            $message,
            $context
        );
    }

    /**
     * Log a warning.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning(
        string $message,
        array $context = []
    ): void {
        self::write(
            'WARNING',
            $message,
            $context
        );
    }

    /**
     * Log an error.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error(
        string $message,
        array $context = []
    ): void {
        self::write(
            'ERROR',
            $message,
            $context
        );
    }

    /**
     * Log an exception.
     *
     * @param Throwable $exception
     * @param array $context
     * @return void
     */
    public static function exception(
        Throwable $exception,
        array $context = []
    ): void {
        $context['exception'] = get_class($exception);
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();

        self::error(
            $exception->getMessage(),
            $context
        );
    }

    /**
     * Write one log entry.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    private static function write(
        string $level,
        string $message,
        array $context = []
    ): void {
        $line = sprintf(
            '[%s] [%s] %s',
            date('Y-m-d H:i:s'),
            $level,
            $message
        );

        if (!empty($context)) {
            $line .= ' ' . json_encode(
                $context,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );
        }

        file_put_contents(
            self::LOG_FILE,
            $line . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}