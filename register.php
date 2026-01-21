<?php
/**
 * User Registration Page
 * 
 * Handles user registration with validation and security measures.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Auth.php';

// Start session
session_start();

// Initialize Auth and redirect if already logged in
$auth = new Auth();
$auth->redirectIfAuthenticated('profile.php');

// Initialize variables
$errors = [];
$success = '';
$name = '';
$email = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name)) {
        $errors[] = "Full name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $user = new User();
        if ($user->emailExists($email)) {
            $errors[] = "Email address already registered.";
        }
    }

    // If no errors, register user
    if (empty($errors)) {
        if ($user->register($name, $email, $password)) {
            $success = "Registration successful! Redirecting to login...";
            // Clear form data
            $name = '';
            $email = '';
            // Redirect after 2 seconds
            header("refresh:2;url=login.php");
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up | Interactive Cares</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        float: "float 3s ease-in-out infinite",
                        fadeIn: "fadeIn 0.5s ease-in forwards",
                    },
                    keyframes: {
                        float: {
                            "0%, 100%": { transform: "translateY(0px)" },
                            "50%": { transform: "translateY(-10px)" },
                        },
                        fadeIn: {
                            from: { opacity: 0, transform: "translateY(10px)" },
                            to: { opacity: 1, transform: "translateY(0)" },
                        },
                    },
                },
            },
        };
    </script>
    <style>
        body {
            overflow-x: hidden;
        }

        .glass {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-50 via-white to-cyan-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center animate-float">
            <div
                class="mx-auto bg-gradient-to-r from-indigo-500 to-purple-600 w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="w-6 h-6 text-white">
                    <path
                        d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Create Account
            </h1>
            <p class="mt-2 text-gray-600">Join us today to get started</p>
        </div>

        <div class="bg-white glass rounded-2xl shadow-xl p-8 transition-all duration-300 hover:shadow-2xl">
            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Registration Error</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                <?php foreach ($errors as $error): ?>
                                    <li>
                                        <?php echo htmlspecialchars($error); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                <?php echo htmlspecialchars($success); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6 animate-fadeIn">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"
                                class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all duration-300"
                                placeholder="John Doe" required />
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                            class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all duration-300"
                            placeholder="you@example.com" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input type="password" name="password"
                            class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all duration-300"
                            placeholder="••••••••" required />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input type="password" name="confirm_password"
                            class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all duration-300"
                            placeholder="••••••••" required />
                    </div>
                </div>

                <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5 text-indigo-300 group-hover:text-white transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 15.75h3m0 0h3m-3 0V12m0 3.75V18" />
                        </svg>
                    </span>
                    Create Account
                </button>
            </form>
        </div>

        <div class="text-center text-sm text-gray-600">
            <p>
                Already have an account?
                <a href="login.php" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Sign
                    in</a>
            </p>
        </div>
    </div>
</body>

</html>