<?php

declare(strict_types=1);

/**
 * Creates PDO connections using environment-based database configuration.
 */
final class DatabaseConnection
{
    /**
     * Values loaded from `.env` when the hosting environment disables `putenv`.
     *
     * @var array<string, string>
     */
    private static array $loadedEnvironment = [];

    /**
     * Loads the optional local `.env` file into the process environment.
     *
     * @return void
     */
    public static function loadEnvironment(): void
    {
        self::loadDotEnv(dirname(__DIR__, 2) . '/.env');
    }

    /**
     * Opens the configured MySQL connection.
     *
     * @return PDO Configured PDO connection.
     *
     * @throws RuntimeException If required configuration is missing.
     * @throws PDOException If the connection cannot be opened.
     */
    public static function createFromEnvironment(): PDO
    {
        self::loadEnvironment();
        $dsn = self::requiredEnvironment('DB_DSN');
        $username = self::requiredEnvironment('DB_USER');
        $password = self::getEnvironment('DB_PASSWORD');

        if ($password === false) {
            throw new RuntimeException('Variabile d’ambiente DB_PASSWORD non configurata.');
        }

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    /**
     * Reads a value from the process environment or the local `.env` fallback.
     *
     * Process-level variables take precedence over values loaded from `.env`.
     *
     * @param string $name Environment variable name.
     *
     * @return string|false Environment value, or false when it is not configured.
     */
    public static function getEnvironment(string $name): string|false
    {
        $value = getenv($name);
        if ($value !== false) {
            return $value;
        }

        return self::$loadedEnvironment[$name] ?? false;
    }

    /**
     * Loads simple KEY=VALUE entries from a local environment file.
     *
     * Existing process environment variables take precedence over values from
     * the file, allowing deployment configuration to override local defaults.
     *
     * @param string $path Path to the optional environment file.
     *
     * @return void
     *
     * @throws RuntimeException If the file cannot be read.
     */
    private static function loadDotEnv(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException("Impossibile leggere il file environment: {$path}");
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $separator = strpos($line, '=');
            if ($separator === false) {
                continue;
            }

            $name = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));
            if ($name === '' || self::getEnvironment($name) !== false) {
                continue;
            }
            if (
                strlen($value) >= 2
                && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            if (function_exists('putenv')) {
                putenv("{$name}={$value}");
                continue;
            }

            self::$loadedEnvironment[$name] = $value;
        }
    }

    /**
     * Reads a required non-empty environment variable.
     *
     * @param string $name Environment variable name.
     *
     * @return string Environment value.
     *
     * @throws RuntimeException If the variable is missing or empty.
     */
    private static function requiredEnvironment(string $name): string
    {
        $value = self::getEnvironment($name);
        if ($value === false || trim($value) === '') {
            throw new RuntimeException("Variabile d’ambiente {$name} non configurata.");
        }

        return $value;
    }
}
