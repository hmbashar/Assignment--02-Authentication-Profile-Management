<?php
/**
 * Logout Page
 * 
 * Destroys user session and redirects to login page.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/classes/Auth.php';

// Start session
session_start();

// Initialize Auth and logout
$auth = new Auth();
$auth->logout('login.php');
