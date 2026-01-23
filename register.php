<?php
/**
 * ========================================
 * REGISTER PAGE
 * ========================================
 * User registration with role selection
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/security.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$success = '';
$formData = [
    'username' => '',
    'email' => '',
    'role' => 'user'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Sanitize inputs
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = sanitizeInput($_POST['role'] ?? 'user');
        
        // Store form data for repopulation
        $formData = [
            'username' => $username,
            'email' => $email,
            'role' => $role
        ];
        
        // Validation
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        } elseif (strlen($username) > 50) {
            $errors[] = 'Username must not exceed 50 characters.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Invalid email format.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (!validatePassword($password)) {
            $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, and number.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!in_array($role, ['user', 'admin'])) {
            $role = 'user'; // Default to user if invalid role
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            $conn = getDBConnection();
            
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = 'Username or email already exists.';
            }
            
            $stmt->close();
            
            // If no errors, create user
            if (empty($errors)) {
                $hashedPassword = hashPassword($password);
                $avatarUrl = generateDefaultAvatar($username);
                
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, avatar_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $avatarUrl);
                
                if ($stmt->execute()) {
                    $success = 'Registration successful! Redirecting to login...';
                    
                    // Redirect after 2 seconds
                    header("refresh:2;url=login.php");
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
                
                $stmt->close();
            }
            
            closeDBConnection($conn);
        }
    }
}

// Generate CSRF token
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Explore Kaltim</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./src/assets/icons/favicon.svg">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                        }
                    },
                    fontFamily: {
                        'display': ['Montserrat', 'sans-serif'],
                        'body': ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <style>
        body {
            background: linear-gradient(135deg, #065f46 0%, #059669 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-body antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-display font-bold text-white mb-2">Explore Kaltim</h1>
                <p class="text-primary-100">Create your account to start exploring</p>
            </div>
            
            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-6">Register</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-red-800 mb-1">Registration Error</h3>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo escapeOutput($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-700"><?php echo escapeOutput($success); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="<?php echo escapeOutput($formData['username']); ?>"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            placeholder="Enter your username"
                        >
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?php echo escapeOutput($formData['email']); ?>"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            placeholder="Enter your email"
                        >
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            placeholder="Create a strong password"
                        >
                        <p class="mt-1 text-xs text-gray-500">Min. 8 characters with uppercase, lowercase, and number</p>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            placeholder="Confirm your password"
                        >
                    </div>
                    
                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Register as</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary-500 transition <?php echo $formData['role'] === 'user' ? 'border-primary-600 bg-primary-50' : ''; ?>">
                                <input 
                                    type="radio" 
                                    name="role" 
                                    value="user" 
                                    <?php echo $formData['role'] === 'user' ? 'checked' : ''; ?>
                                    class="sr-only"
                                >
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Tourist</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary-500 transition <?php echo $formData['role'] === 'admin' ? 'border-primary-600 bg-primary-50' : ''; ?>">
                                <input 
                                    type="radio" 
                                    name="role" 
                                    value="admin" 
                                    <?php echo $formData['role'] === 'admin' ? 'checked' : ''; ?>
                                    class="sr-only"
                                >
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Admin</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-3 px-4 bg-primary-800 hover:bg-primary-700 text-white font-display font-semibold rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                    >
                        Create Account
                    </button>
                </form>
                
                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="text-primary-600 hover:text-primary-700 font-medium">Login here</a>
                    </p>
                </div>
                
                <!-- Back to Home -->
                <div class="mt-4 text-center">
                    <a href="index.html" class="text-sm text-gray-500 hover:text-gray-700">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Add visual feedback for radio button selection
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="role"]').forEach(r => {
                    const label = r.closest('label');
                    if (r.checked) {
                        label.classList.add('border-primary-600', 'bg-primary-50');
                        label.classList.remove('border-gray-300');
                    } else {
                        label.classList.remove('border-primary-600', 'bg-primary-50');
                        label.classList.add('border-gray-300');
                    }
                });
            });
        });
    </script>
</body>
</html>
