<?php
/**
 * Auth Class
 * 
 * Handles user authentication, session management, and access control.
 * Provides methods for login, logout, and authentication checks.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/../classes/User.php';

class Auth
{
    private $user;

    /**
     * Constructor - Initialize User class and start session
     */
    public function __construct()
    {
        $this->user = new User();

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Login user with email and password
     * 
     * @param string $email User's email
     * @param string $password User's password
     * @return bool True on success, false on failure
     */
    public function login($email, $password)
    {
        // Get user by email
        $userData = $this->user->getUserByEmail($email);

        if ($userData === false) {
            return false;
        }

        // Verify password using password_verify
        if (password_verify($password, $userData['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['name'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['logged_in'] = true;

            return true;
        }

        return false;
    }

    /**
     * Check if user is logged in
     * 
     * @return bool True if logged in, false otherwise
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require authentication - redirect to login if not authenticated
     * 
     * @param string $redirectTo URL to redirect to if not authenticated
     */
    public function requireAuth($redirectTo = 'login.php')
    {
        if (!$this->isLoggedIn()) {
            header("Location: $redirectTo");
            exit();
        }
    }

    /**
     * Get current logged-in user data
     * 
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
        ];
    }

    /**
     * Logout user - destroy session and redirect
     * 
     * @param string $redirectTo URL to redirect after logout
     */
    public function logout($redirectTo = 'login.php')
    {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: $redirectTo");
        exit();
    }

    /**
     * Redirect if already logged in
     * 
     * @param string $redirectTo URL to redirect to if already logged in
     */
    public function redirectIfAuthenticated($redirectTo = 'profile.php')
    {
        if ($this->isLoggedIn()) {
            header("Location: $redirectTo");
            exit();
        }
    }
}
