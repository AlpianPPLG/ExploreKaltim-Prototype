<?php
/**
 * ========================================
 * LOGIN PAGE
 * ========================================
 * User authentication with session management
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/security.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Sanitize inputs
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);
        
        // Validation
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Invalid email format.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }
        
        // Rate limiting check
        if (!checkRateLimit($email)) {
            $remainingTime = getRemainingLockoutTime($email);
            $minutes = ceil($remainingTime / 60);
            $errors[] = "Too many login attempts. Please try again in {$minutes} minute(s).";
        }
        
        // Authenticate user
        if (empty($errors)) {
            $conn = getDBConnection();
            
            $stmt = $conn->prepare("SELECT id, username, email, password, role, avatar_url FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (verifyPassword($password, $user['password'])) {
                    // Reset rate limit on successful login
                    resetRateLimit($email);
                    
                    // Set session
                    setUserSession($user);
                    
                    // Set remember me cookie if checked
                    if ($rememberMe) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true); // 30 days
                    }
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header("Location: /ExploreKaltim/admin/dashboard.php");
                    } else {
                        header("Location: /ExploreKaltim/user/dashboard.php");
                    }
                    exit();
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
            
            $stmt->close();
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
    <title>Login - Explore Kaltim</title>
    
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
                <p class="text-primary-100">Welcome back! Please login to your account</p>
            </div>
            
            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-display font-bold text-gray-900 mb-6">Login</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-red-800 mb-1">Login Error</h3>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo escapeOutput($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?php echo escapeOutput($email); ?>"
                                required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                placeholder="Enter your email"
                            >
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="remember_me" 
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            >
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            Forgot password?
                        </a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-3 px-4 bg-primary-800 hover:bg-primary-700 text-white font-display font-semibold rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                    >
                        Login
                    </button>
                </form>
                
                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="register.php" class="text-primary-600 hover:text-primary-700 font-medium">Register here</a>
                    </p>
                </div>
                
                <!-- Back to Home -->
                <div class="mt-4 text-center">
                    <a href="index.html" class="text-sm text-gray-500 hover:text-gray-700">
                        ← Back to Home
                    </a>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-lg p-4 text-white text-sm">
                <p class="font-medium mb-2">Demo Credentials:</p>
                <p>Admin: admin@explorekaltim.com / admin123</p>
                <p>User: budi@gmail.com / user123</p>
            </div>
        </div>
    </div>
</body>
</html>
