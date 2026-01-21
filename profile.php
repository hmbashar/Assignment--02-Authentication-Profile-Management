<?php
/**
 * User Profile Page
 * 
 * Displays user profile and allows profile updates.
 * Requires authentication.
 * 
 * @author Md Abul Bashar
 */

require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/User.php';

// Start session
session_start();

// Initialize Auth and require authentication
$auth = new Auth();
$auth->requireAuth('login.php');

// Get current user data
$currentUser = $auth->getCurrentUser();
$user = new User();
$userData = $user->getUserById($currentUser['id']);

// Initialize variables
$errors = [];
$success = '';
$isEditing = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Process form submission (profile update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
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

    // Check if email already exists (excluding current user)
    if (empty($errors) && $user->emailExists($email, $currentUser['id'])) {
        $errors[] = "Email address already in use by another account.";
    }

    // If changing password, validate it
    $updatePassword = null;
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = "New passwords do not match.";
        }

        // Verify current password before allowing password change
        if (empty($errors)) {
            $currentUserData = $user->getUserByEmail($userData['email']);
            if (!password_verify($currentPassword, $currentUserData['password'])) {
                $errors[] = "Current password is incorrect.";
            } else {
                $updatePassword = $newPassword;
            }
        }
    }

    // If no errors, update profile
    if (empty($errors)) {
        if ($user->updateProfile($currentUser['id'], $name, $email, $updatePassword)) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            // Refresh user data
            $userData = $user->getUserById($currentUser['id']);
            $currentUser = $auth->getCurrentUser();

            $success = "Profile updated successfully!";
            $isEditing = false;
        } else {
            $errors[] = "Profile update failed. Please try again.";
        }
    }
}

// Get user initials for avatar
$initials = '';
if ($userData && !empty($userData['name'])) {
    $nameParts = explode(' ', $userData['name']);
    $initials = strtoupper(substr($nameParts[0], 0, 1));
    if (isset($nameParts[1])) {
        $initials .= strtoupper(substr($nameParts[1], 0, 1));
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile | Interactive Cares</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: "#eef2ff",
                            100: "#e0e7ff",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                        },
                        purple: {
                            50: "#faf5ff",
                            500: "#a855f7",
                            600: "#9333ea",
                            700: "#7e22ce",
                        },
                    },
                    animation: {
                        fadeIn: "fadeIn 0.5s ease-in forwards",
                        slideIn: "slideIn 0.3s ease-out forwards",
                    },
                    keyframes: {
                        fadeIn: {
                            from: { opacity: 0, transform: "translateY(10px)" },
                            to: { opacity: 1, transform: "translateY(0)" },
                        },
                        slideIn: {
                            from: { opacity: 0, transform: "translateX(-10px)" },
                            to: { opacity: 1, transform: "translateX(0)" },
                        },
                    },
                },
            },
        };
    </script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

        body {
            font-family: "Inter", sans-serif;
        }

        .glass {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #f3f4f6;
            border-left: 4px solid #4f46e5;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Dashboard Container -->
    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="lg:w-64 bg-white shadow-lg z-10 lg:h-screen lg:sticky lg:top-0">
            <div class="p-6 border-b">
                <div class="flex items-center space-x-3">
                    <div
                        class="bg-gradient-to-r from-indigo-500 to-purple-600 w-10 h-10 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5 text-white">
                            <path fill-rule="evenodd"
                                d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="font-bold text-lg bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Interactive Cares
                        </h1>
                        <p class="text-xs text-gray-500">Dashboard</p>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <nav class="space-y-1">
                    <a href="profile.php" class="flex items-center space-x-3 p-3 rounded-lg sidebar-link active">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-indigo-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="font-medium">My Profile</span>
                    </a>
                    <a href="logout.php" class="flex items-center space-x-3 p-3 rounded-lg sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        <span class="font-medium">Logout</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">My Profile</h2>
                    <p class="text-gray-600">Welcome back,
                        <?php echo htmlspecialchars($userData['name']); ?>!
                    </p>
                </div>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <?php if (!$isEditing): ?>
                        <a href="?edit=true"
                            class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            <span>Edit Profile</span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php"
                        class="flex items-center space-x-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:text-indigo-600 hover:border-indigo-600 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="max-w-4xl">
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
                                <h3 class="text-sm font-medium text-red-800">Update Error</h3>
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

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2"></div>
                    <div class="p-6">
                        <?php if (!$isEditing): ?>
                            <!-- View Mode -->
                            <div class="flex flex-col md:flex-row md:items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-24 h-24 rounded-full bg-gradient-to-r from-indigo-100 to-purple-100 flex items-center justify-center text-3xl font-bold text-indigo-600">
                                        <?php echo htmlspecialchars($initials); ?>
                                    </div>
                                </div>
                                <div class="mt-4 md:mt-0 md:ml-6 flex-1">
                                    <h3 class="text-xl font-bold text-gray-800">
                                        <?php echo htmlspecialchars($userData['name']); ?>
                                    </h3>
                                    <p class="text-gray-500">
                                        <?php echo htmlspecialchars($userData['email']); ?>
                                    </p>
                                    <p class="text-sm text-gray-400 mt-2">Member since
                                        <?php echo date('F Y', strtotime($userData['created_at'])); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 border rounded-lg">
                                    <p class="text-gray-500 text-sm">Full Name</p>
                                    <p class="font-medium">
                                        <?php echo htmlspecialchars($userData['name']); ?>
                                    </p>
                                </div>
                                <div class="p-4 border rounded-lg">
                                    <p class="text-gray-500 text-sm">Email Address</p>
                                    <p class="font-medium">
                                        <?php echo htmlspecialchars($userData['email']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Edit Mode -->
                            <form method="POST" action="" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                        <input type="text" name="name"
                                            value="<?php echo htmlspecialchars($userData['name']); ?>"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                                            required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                        <input type="email" name="email"
                                            value="<?php echo htmlspecialchars($userData['email']); ?>"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                                            required />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Change Password (Optional)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Current
                                                Password</label>
                                            <input type="password" name="current_password"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                                                placeholder="••••••••" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">New
                                                Password</label>
                                            <input type="password" name="new_password"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                                                placeholder="••••••••" />
                                            <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New
                                                Password</label>
                                            <input type="password" name="confirm_password"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
                                                placeholder="••••••••" />
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Leave password fields empty if you don't want to
                                        change your password.</p>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <button type="submit"
                                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg">
                                        Save Changes
                                    </button>
                                    <a href="profile.php"
                                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-300">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>