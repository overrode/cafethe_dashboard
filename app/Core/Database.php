<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Represents a singleton-based database connection manager.
 * Provides a method to establish and retrieve a PDO connection
 * to interact with a database, using configuration parameters.
 */
class Database
{
    /**
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Establishes and returns a connection to the database.
     *
     * If the connection has not been initialized, it creates a new PDO instance
     * using the configuration parameters provided in the database configuration file.
     * The connection is created with standardized error handling and fetch mode settings.
     *
     * @return PDO The PDO instance representing the established database connection.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';

            /**
             * Data Source Name (DSN) string used to define the connection parameters
             * for a database in a single, unified format. The DSN typically contains
             * the type of database, host, database name, port, and other optional
             * parameters required to establish the connection.
             *
             * Commonly structured as:
             * "driver:host=hostname;dbname=database;port=port", where "driver" specifies
             * the type of database (e.g., mysql, pgsql, sqlite), and other parameters
             * are dependent on the specific database driver used.
             */
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
