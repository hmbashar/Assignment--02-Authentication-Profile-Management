<?php
/**
 * Database Connection Class
 * 
 * Provides a singleton PDO database connection for the application.
 * Uses prepared statements to prevent SQL injection attacks.
 * 
 * @author Md Abul Bashar
 */

class Database
{
    // Database credentials
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'assignment02_db';
    private const DB_USER = 'root';
    private const DB_PASS = '123456789';

    private static $instance = null;
    private $connection;

    /**
     * Private constructor to prevent direct instantiation
     * Establishes PDO connection with error handling
     */
    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);

        } catch (PDOException $e) {
            // Log error in production, display for development
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance of Database
     * 
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection object
     * 
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
