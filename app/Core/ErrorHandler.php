<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * ErrorHandler Class
 */
class ErrorHandler
{
    /**
     * Register global handlers.
     *
     * @return void
     */
    public static function register(): void
    {
        set_exception_handler(
            [self::class, 'handleException']
        );

        register_shutdown_function(
            [self::class, 'handleShutdown']
        );
    }


    /**
     * Handle uncaught exceptions.
     *
     * @param Throwable $exception
     * @return void
     */
    public static function handleException(
        Throwable $exception
    ): void {
        Logger::exception(
            $exception,
            [
                'route' => $_GET['route'] ?? null,
                'user_id' => $_SESSION['user']['id'] ?? null,
                'client_id' => $_SESSION['client']['id'] ?? null,
            ]
        );

        http_response_code(500);

        self::render($exception);
    }

    /**
     * Handle fatal PHP errors.
     *
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if (!$error) {
            return;
        }

        $fatalTypes = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
        ];

        if (!in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        Logger::error(
            $error['message'],
            [
                'file' => $error['file'],
                'line' => $error['line'],
                'route' => $_GET['route'] ?? null,
            ]
        );

        http_response_code(500);

        self::renderFatal($error);
    }

    /**
     * Render exception output.
     *
     * @param Throwable $exception
     * @return void
     */
    private static function render(
        Throwable $exception
    ): void {
        if (IS_DEVELOPMENT) {
            $error = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];

            require ROOT_PATH . '/app/Views/errors/500.php';

            return;
        }

        $error = null;

        require ROOT_PATH . '/app/Views/errors/500.php';
    }

    /**
     * Render fatal error output.
     *
     * @param array $fatalError
     * @return void
     */
    private static function renderFatal(
        array $fatalError
    ): void {
        if (IS_DEVELOPMENT) {
            $error = [
                'message' => $fatalError['message'],
                'file' => $fatalError['file'],
                'line' => $fatalError['line'],
                'trace' => null,
            ];

            require ROOT_PATH . '/app/Views/errors/500.php';

            return;
        }

        $error = null;

        require ROOT_PATH . '/app/Views/errors/500.php';
    }
}