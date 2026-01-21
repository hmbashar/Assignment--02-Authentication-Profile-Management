<?php
/**
 * User Class
 * 
 * Handles user registration, retrieval, and profile management.
 * All database operations use prepared statements for security.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/../config/Database.php';

class User
{
    private $db;

    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Register a new user
     * 
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password User's password (will be hashed)
     * @return bool True on success, false on failure
     */
    public function register($name, $email, $password)
    {
        try {
            // Hash password using PHP's password_hash
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            // Log error in production
            error_log("Registration Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     * 
     * @param string $email User's email address
     * @return array|false User data or false if not found
     */
    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     * 
     * @param int $id User's ID
     * @return array|false User data or false if not found
     */
    public function getUserById($id)
    {
        try {
            $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile
     * 
     * @param int $id User's ID
     * @param string $name New name
     * @param string $email New email
     * @param string|null $password New password (optional)
     * @return bool True on success, false on failure
     */
    public function updateProfile($id, $name, $email, $password = null)
    {
        try {
            // If password is provided, update it as well
            if ($password !== null && $password !== '') {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            } else {
                // Update only name and email
                $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
                $stmt = $this->db->prepare($sql);
            }

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Profile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email already exists
     * 
     * @param string $email Email to check
     * @param int|null $excludeId User ID to exclude from check (for updates)
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email, $excludeId = null)
    {
        try {
            if ($excludeId !== null) {
                $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email AND id != :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            } else {
                $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Email Check Error: " . $e->getMessage());
            return false;
        }
    }
}
