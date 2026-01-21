<?php
/**
 * Index Page - Entry Point
 * 
 * Redirects to login page if not authenticated, or profile if authenticated.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/classes/Auth.php';

// Start session
session_start();

// Initialize Auth
$auth = new Auth();

// Check if user is logged in
if ($auth->isLoggedIn()) {
    // Redirect to profile if already logged in
    header("Location: profile.php");
} else {
    // Redirect to login page
    header("Location: login.php");
}
exit();
