<?php
// Any PHP logic can go here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
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
            background: white;
            box-shadow: 0 4px 6px rgba(128, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #a52a2a 0%, #800000 100%);
        }
        .form-section {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(128, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .section-title {
            position: relative;
            padding-left: 1.5rem;
            color: #800000;
            font-weight: 600;
        }
        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0.75rem;
            height: 0.75rem;
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            border-radius: 50%;
        }
        .back-link {
            transition: color 0.3s ease;
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
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 md:p-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex justify-start my-4">
                <a href="login.php" class="back-link inline-flex items-center text-[#800000] hover:text-[#a52a2a] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Login
                </a>
            </div>
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#800000] mb-2">Student Registration</h1>
                <p class="text-gray-600 text-sm">Please fill in your details to create an account</p>
            </div>

            <div class="form-container rounded-3xl p-6 sm:p-8">
                <form action="../logic/register_logic.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h2 class="section-title text-lg font-semibold mb-4">Personal Information</h2>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                            <input type="text" id="first_name" name="first_name" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        </div>

                                        <div>
                                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                            <input type="text" id="last_name" name="last_name" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                        <input type="text" id="middle_name" name="middle_name"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                                            <select id="grade_level" name="grade_level" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                                <option value="">Select Grade Level</option>
                                                <option value="7">Grade 7</option>
                                                <option value="8">Grade 8</option>
                                                <option value="9">Grade 9</option>
                                                <option value="10">Grade 10</option>
                                                <option value="11">Grade 11</option>
                                                <option value="12">Grade 12</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                            <input type="text" id="section" name="section" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="form-section">
                                <h2 class="section-title text-lg font-semibold mb-4">Contact Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <div class="flex gap-2">
                                            <input type="email" id="email" name="email" oninput="validateEmail()" required
                                                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                            <button type="button" id="getCode" onclick="executeSendCode()"
                                                class="btn-primary px-4 py-3 text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000]/20 focus:ring-offset-2 text-sm">
                                                Send Code
                                            </button>
                                        </div>
                                        <span id="email_status" class="text-xs text-red-600 mt-1 block"></span>
                                    </div>

                                    <div>
                                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Email Verification Code</label>
                                        <input type="text" id="code" name="code" oninput="validateCode()" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        <span id="code_status" class="text-xs text-red-600 mt-1 block"></span>
                                    </div>

                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" pattern="[0-9]*" maxlength="11" minlength="11" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Parent/Guardian Information Section -->
                            <div class="form-section">
                                <h2 class="section-title text-lg font-semibold mb-4">Parent/Guardian Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Name</label>
                                        <input type="text" id="parent_name" name="parent_name" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                    </div>

                                    <div>
                                        <label for="parent_contact" class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Contact Number</label>
                                        <input type="tel" id="parent_contact" name="parent_contact" pattern="[0-9]*" maxlength="11" minlength="11" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Account Security Section -->
                            <div class="form-section">
                                <h2 class="section-title text-lg font-semibold mb-4">Account Security</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input type="password" id="password" name="password" oninput="validatePassword()" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        <div class="mt-2 space-y-1">
                                            <p class="text-xs text-gray-600">Password must contain:</p>
                                            <ul class="text-xs space-y-1">
                                                <li id="length-check" class="flex items-center text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    At least 6 characters
                                                </li>
                                                <li id="special-check" class="flex items-center text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    At least one special character (!@#$%^&*)
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" oninput="validatePassword()" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 outline-none">
                                        <span id="password_match_status" class="text-xs text-red-600 mt-1 block"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4 border-t border-gray-200 mt-6">
                        <button type="submit" id="register"
                            class="btn-primary px-8 py-3 text-white font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000]/20 focus:ring-offset-2">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.emailjs.com/dist/email.min.js"></script>
    <script src="../js/index.js"></script>
    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const lengthCheck = document.getElementById('length-check');
            const specialCheck = document.getElementById('special-check');
            const matchStatus = document.getElementById('password_match_status');
            const registerButton = document.getElementById('register');
            
            // Check password length
            const hasMinLength = password.length >= 6;
            lengthCheck.className = `flex items-center text-xs ${hasMinLength ? 'text-green-600' : 'text-gray-500'}`;
            lengthCheck.querySelector('svg').innerHTML = hasMinLength ? 
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' :
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            
            // Check for special character
            const hasSpecialChar = /[!@#$%^&*]/.test(password);
            specialCheck.className = `flex items-center text-xs ${hasSpecialChar ? 'text-green-600' : 'text-gray-500'}`;
            specialCheck.querySelector('svg').innerHTML = hasSpecialChar ?
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' :
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            
            // Check password match
            if (confirmPassword) {
                if (password === confirmPassword) {
                    matchStatus.textContent = 'Passwords match';
                    matchStatus.className = 'text-xs text-green-600 mt-1 block';
                } else {
                    matchStatus.textContent = 'Passwords do not match';
                    matchStatus.className = 'text-xs text-red-600 mt-1 block';
                }
            } else {
                matchStatus.textContent = '';
            }
            
            // Enable/disable register button based on all validations
            const isPasswordValid = hasMinLength && hasSpecialChar && password === confirmPassword;
            registerButton.disabled = !isPasswordValid;
            registerButton.className = `btn-primary px-8 py-3 text-white font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000]/20 focus:ring-offset-2 ${!isPasswordValid ? 'opacity-50 cursor-not-allowed' : ''}`;
        }

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 6 || !/[!@#$%^&*]/.test(password) || password !== confirmPassword) {
                e.preventDefault();
                alert('Please ensure your password meets all requirements and matches the confirmation.');
            }
        });
    </script>
</body>
</html>
