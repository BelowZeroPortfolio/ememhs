<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMEMHS Guidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fafafa;
            scroll-behavior: smooth;
            position: relative;
            overflow-x: hidden;
        }
        .accent-primary { color: #800000; }
        .bg-primary { background: #800000; }
        .bg-primary-light { background: #f8eaea; }
        .btn-primary { 
            background: #800000;
            color: #fff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover { 
            background: #a52a2a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128,0,0,0.2);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .feature-card {
            background: white;
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(128,0,0,0.1) 0%, rgba(128,0,0,0) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(128,0,0,0.1);
        }
        .feature-card:hover::before {
            opacity: 1;
        }
        .icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #f8eaea;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
        }
        .feature-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
            background: #800000;
        }
        .feature-card:hover .icon-wrapper svg {
            color: white;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        .section-subtitle {
            font-size: 1.05rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.6;
        }
        .feature-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .feature-desc {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
        }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            color: #1e293b;
            font-weight: 500;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #800000;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .hero-pattern {
            position: relative;
            overflow: hidden;
        }
        .hero-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(128, 0, 0, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(128, 0, 0, 0.05) 0%, transparent 50%);
            z-index: -1;
            animation: heroPatternMove 20s linear infinite;
        }
        .gradient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
        }
        .gradient-blob-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle at center, #800000 0%, transparent 70%);
            top: -200px;
            right: -200px;
        }
        .gradient-blob-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle at center, #a52a2a 0%, transparent 70%);
            bottom: -150px;
            left: -150px;
        }
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(128,0,0,0.1);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stats-label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }
        @media (min-width: 768px) {
            .section-title { font-size: 3rem; }
            .section-subtitle { font-size: 1.05rem; }
        }
        /* Background Elements */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.3;
            background: 
                linear-gradient(45deg, rgba(128, 0, 0, 0.02) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(128, 0, 0, 0.02) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(128, 0, 0, 0.02) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(128, 0, 0, 0.02) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            opacity: 0.4;
        }

        .shape {
            position: absolute;
            background: rgba(128, 0, 0, 0.02);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            top: 60%;
            right: 10%;
            animation-delay: -7s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            bottom: 20%;
            left: 20%;
            animation-delay: -14s;
        }

        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 0% 0%, rgba(255, 255, 255, 0.8) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(255, 255, 255, 0.8) 0%, transparent 50%);
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(30px, 30px) rotate(45deg);
            }
            50% {
                transform: translate(0, 60px) rotate(90deg);
            }
            75% {
                transform: translate(-30px, 30px) rotate(135deg);
            }
        }

        /* Enhanced Section Styles */
        .section-pattern {
            position: relative;
            overflow: hidden;
        }

        .section-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(128, 0, 0, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(128, 0, 0, 0.03) 0%, transparent 50%);
            z-index: -1;
        }

        /* Enhanced Card Styles */
        .feature-card {
            position: relative;
            overflow: hidden;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(128, 0, 0, 0.03) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover::after {
            opacity: 1;
        }

        /* Enhanced Hero Section */
        @keyframes heroPatternMove {
            0% {
                transform: scale(1) rotate(0deg);
            }
            50% {
                transform: scale(1.1) rotate(1deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden">
    <!-- Background Elements -->
    <div class="bg-pattern"></div>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="gradient-overlay"></div>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center no-underline">
                        <img src="image/ememhs-logo.png" alt="EMEMHS Logo" class="h-10 w-auto mr-2">
                        <span class="text-md font-bold text-[#800000]">Guidance System</span>
                    </a>
                </div>

                <!-- Center Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#up" class="text-sm text-gray-700 hover:text-[#800000]">Home</a>
                    <a href="#features" class="text-sm text-gray-700 hover:text-[#800000]">Features</a>
                    <a href="#about" class="text-sm text-gray-700 hover:text-[#800000]">About</a>
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-6">
                    <a href="pages/login.php" class="text-sm text-gray-700 hover:text-[#800000]">Login</a>
                    <a href="pages/register.php" class="text-sm bg-[#800000] text-white px-3 py-1.5 rounded-lg hover:bg-[#600000] transition-colors duration-300">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="w-full py-24 px-4 relative z-10 hero-pattern" id="up">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-center text-center items-center">
                <div class="container">
                    <div class="animate__animated animate__fadeIn">
                        <h1 class="section-title mb-6">
                            Boost Your School Life with <span class="accent-primary">Guidance Management</span>
                        </h1>
                        <p class="section-subtitle mb-8">
                            Take charge of your academic journey and well-being with ease. Organize counseling sessions, address concerns, and stay connected—all in one place.
                        </p>
                        <div class="flex justify-center text-center items-center  gap-4">
                            <a href="pages/login.php" class="btn-primary px-8 py-3 rounded-xl font-semibold text-base">Get Started Today</a>
                            <a href="#features" class="px-8 py-3 rounded-xl font-semibold text-base border-2 border-[#800000] text-[#800000] hover:bg-[#800000] hover:text-white transition-all duration-300">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4 bg-white relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="stats-card">
                    <div class="stats-number">500+</div>
                    <div class="stats-label">Active Students</div>
                </div>
                <div class="stats-card">
                    <div class="stats-number">98%</div>
                    <div class="stats-label">Satisfaction Rate</div>
                </div>
                <div class="stats-card">
                    <div class="stats-number">24/7</div>
                    <div class="stats-label">Support Available</div>
                </div>
                <div class="stats-card">
                    <div class="stats-number">1000+</div>
                    <div class="stats-label">Sessions Completed</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 px-4 bg-[#fafafa] relative z-10 section-pattern">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title mb-4">Core Features Designed for You</h2>
                <p class="section-subtitle max-w-2xl mx-auto">Everything you need to succeed in your academic journey</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Seamless Scheduling</h3>
                    <p class="feature-desc">Effortlessly book sessions with guidance counselors at a time that works for you.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Instant Notifications</h3>
                    <p class="feature-desc">Receive updates about your appointments and other important events directly through SMS or email.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Secure Complaints Submission</h3>
                    <p class="feature-desc">Report concerns privately and confidently, knowing they'll be addressed by the right people.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section id="about" class="py-24 px-4 bg-white relative z-10 section-pattern">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title mb-4">Why Choose EMEMHS Guidance System?</h2>
                <p class="section-subtitle max-w-2xl mx-auto">We're here to support your academic and personal growth</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z"/><polyline points="12 12 12 20"/><polyline points="12 4 12 8"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">A Platform Built for Students</h3>
                    <p class="feature-desc">We designed this system with your needs in mind, offering tools and services that simplify school life.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Support When You Need It</h3>
                    <p class="feature-desc">Whether it's academic guidance or personal challenges, we're here to help you navigate every step of the way.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="icon-wrapper mb-6">
                        <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">A Safe and Confidential Space</h3>
                    <p class="feature-desc">Your concerns and data are handled with the utmost care, ensuring a secure environment for growth and development.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 px-4 bg-[#fafafa] relative z-10 section-pattern">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title mb-4">How It Works</h2>
                <p class="section-subtitle max-w-2xl mx-auto">Simple steps to get started with our guidance system</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-[#f8eaea] flex items-center justify-center mx-auto mb-6">
                        <span class="text-[#800000] font-bold">1</span>
                    </div>
                    <h3 class="feature-title">Create an Account</h3>
                    <p class="feature-desc">Sign up using your student credentials to access the platform.</p>
                </div>
                <div class="feature-card p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-[#f8eaea] flex items-center justify-center mx-auto mb-6">
                        <span class="text-[#800000] font-bold">2</span>
                    </div>
                    <h3 class="feature-title">Submit a Request</h3>
                    <p class="feature-desc">Easily report issues or book a session using our user-friendly interface.</p>
                </div>
                <div class="feature-card p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-[#f8eaea] flex items-center justify-center mx-auto mb-6">
                        <span class="text-[#800000] font-bold">3</span>
                    </div>
                    <h3 class="feature-title">Stay Notified</h3>
                    <p class="feature-desc">Receive updates about your concerns, appointments, and other relevant activities.</p>
                </div>
                
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 px-4 bg-[#f8eaea] relative z-10 section-pattern">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="section-title mb-6">What's Next?</h2>
            <p class="section-subtitle mb-8">Take control of your school experience with EMEMHS Guidance System.</p>
            <a href="pages/login.php" class="btn-primary px-8 py-3 rounded-xl font-semibold text-base inline-block">Sign Up Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-12 border-t relative z-10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-3">
                        <a href="index.php" class="flex items-center no-underline">
                            <img src="image/ememhs-logo.png" alt="EMEMHS Logo" class="h-10 w-auto mr-2">
                            <span class="text-md font-bold text-[#800000]">Guidance System</span>
                        </a>
                    </div>
                    <p class="text-sm text-gray-600">Empowering students through <br> guidance and support</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#up" class="text-sm text-gray-600 hover:text-[#800000]">Home</a></li>
                        <li><a href="#features" class="text-sm text-gray-600 hover:text-[#800000]">Features</a></li>
                        <li><a href="#about" class="text-sm text-gray-600 hover:text-[#800000]">About</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Contact Us</h4>
                    <ul class="space-y-2">
                        <li class="text-sm text-gray-600">Email: support@ememhs.edu</li>
                        <li class="text-sm text-gray-600">Phone: (123) 456-7890</li>
                    </ul>
                </div>
            </div>
            <div class="border-t mt-12 pt-8 text-center">
                <p class="text-sm text-gray-600">© 2024 EMEMHS Guidance System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // Add animation class to the target section
                    targetElement.classList.add('animate__animated', 'animate__fadeIn');
                    
                    // Smooth scroll to the target
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Remove animation class after animation completes
                    setTimeout(() => {
                        targetElement.classList.remove('animate__animated', 'animate__fadeIn');
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>
