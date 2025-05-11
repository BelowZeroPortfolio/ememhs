<?php
// Any PHP logic can go here
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System's Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-focus {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(128, 0, 0, 0.1);
            background: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(128, 0, 0, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary:hover::after {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
            }
            
            .form-container > div {
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center p-4 sm:p-6 md:p-8">
    
    <div class="container mx-auto max-w-6xl">
    <div>
        <a href="../index.php" class="inline-flex items-center text-gray-600 hover:text-[#800000] transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>
        <div class="form-container rounded-3xl overflow-hidden flex flex-col md:flex-row">
            <!-- Left Column - Login Form -->
            <div class="w-full md:w-1/2 p-6 sm:p-8 md:p-12">
                <div class="text-center mb-8">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#800000] mb-2">Welcome Back</h1>
                    <p class="text-gray-600 text-sm sm:text-base">Please login to your account</p>
                </div>

                <form action="../logic/login_logic.php" method="POST" class="space-y-6">
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
                            <input type="text" id="username" name="username" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember"
                                    class="h-4 w-4 text-[#800000] focus:ring-[#800000] border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>
                            <a href="#" class="text-sm text-[#800000] hover:text-[#a52a2a] transition-colors duration-200">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn-primary w-full px-4 py-3 text-white font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000]/20 focus:ring-offset-2">
                        Log In
                    </button>

                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="register.php" class="text-[#800000] hover:text-[#a52a2a] font-medium transition-colors duration-200">Register here</a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Column - Image -->
            <div class="w-full md:w-1/2 bg-[#800000] relative">
                <div class="absolute inset-0 bg-gradient-to-br from-[#800000]/90 to-[#a52a2a]/90"></div>
                <img src="../image/login-logo.png" alt="Login" class="w-full h-full object-cover relative z-10">
            </div>
        </div>
    </div>

    <script src="../js/index.js"></script>
</body>

</html>