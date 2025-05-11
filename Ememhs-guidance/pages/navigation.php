<?php
// Check if user is logged in
require_once '../logic/notification_logic.php';
$isLoggedIn = isset($_SESSION['isLoggedIn']);

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="bg-white shadow-lg sticky top-0 z-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo Section -->
            <div class="flex items-center">
                <a href="student_dashboard.php" class="flex items-center no-underline">
                    <img src="../image/ememhs-logo.png" alt="EMEMHS Logo" class="h-10 w-auto mr-2">
                    <span class="text-md font-bold text-[#800000]">Guidance System</span>
                </a>
            </div>

            <!-- Center Navigation Links -->
            <div class="desktop-nav flex items-center space-x-8">
                <?php if ($isLoggedIn): ?>
                    <a href="student_dashboard.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] <?php echo ($current_page == 'student_dashboard.php') ? 'active' : ''; ?>">Home</a>
                    <a href="complaint-concern.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] <?php echo ($current_page == 'complaint-concern.php') ? 'active' : ''; ?>">Complaint/Concern</a>
                    <a href="lost_item.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] <?php echo ($current_page == 'lost_item.php') ? 'active' : ''; ?>">Lost Item</a>
                <?php endif; ?>
            </div>

            <!-- Right Section -->
            <div class="flex items-center space-x-6 hidden md:flex">
                <?php if ($isLoggedIn): ?>
                    <a href="notifications.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] relative <?php echo ($current_page == 'notifications.php') ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <?php 
                        $unread_count = getUnreadNotificationsCount($_SESSION['user']);
                        if ($unread_count > 0): 
                        ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="profile.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                    <a href="../logic/logout_logic.php" class="text-sm bg-[#800000] text-white px-3 py-1.5 rounded-lg hover:bg-[#600000] transition-colors duration-300">Logout</a>
                <?php else: ?>
                    <a href="index.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Login</a>
                    <a href="register.php" class="text-sm bg-[#800000] text-white px-3 py-1.5 rounded-lg hover:bg-[#600000] transition-colors duration-300">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Button -->
<button class="mobile-menu-button fixed top-4 right-4 z-50 p-2 rounded-lg bg-[#800000] text-white md:hidden">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay"></div>

<!-- Mobile Menu -->
<div class="mobile-menu">
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center">
            <img src="../images/ememhs-logo.png" alt="EMEMHS Logo" class="h-8 w-auto mr-2">
            <span class="text-lg font-bold text-[#800000]">EMEMHS</span>
        </div>
        <button class="close-menu p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="flex flex-col space-y-3">
        <?php if ($isLoggedIn): ?>
            <a href="student_dashboard.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Home</a>
            <a href="complaint-concern.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Complaint/Concern</a>
            <a href="lost_item.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Lost Item</a>
            <a href="notifications.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Notifications
                <?php if ($unread_count > 0): ?>
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
            </a>
            <a href="../logic/logout_logic.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Logout</a>
        <?php else: ?>
            <a href="index.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Login</a>
            <a href="register.php" class="nav-link text-sm text-gray-700 hover:text-[#800000] py-2 px-4 rounded-lg hover:bg-gray-50">Register</a>
        <?php endif; ?>
    </div>
</div>

<style>
    .nav-link {
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        color: #800000;
        transform: translateY(-2px);
    }

    .nav-link.active {
        color: #800000;
        font-weight: 600;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #800000;
        transform: scaleX(1);
        transition: transform 0.3s ease;
    }

    .nav-link:not(.active)::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #800000;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .nav-link:hover::after {
        transform: scaleX(1);
    }

    /* Mobile menu styles */
    .mobile-menu {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        height: 100vh;
        background: white;
        z-index: 50;
        padding: 1rem;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    }

    .mobile-menu.active {
        transform: translateX(0);
        display: block;
    }

    .mobile-menu-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 40;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .mobile-menu-overlay.active {
        display: block;
        opacity: 1;
    }

    @media (max-width: 768px) {
        .desktop-nav {
            display: none;
        }
        .mobile-menu-button {
            display: block;
        }
    }

    @media (min-width: 769px) {
        .mobile-menu {
            display: none !important;
        }
        .mobile-menu-button {
            display: none;
        }
        .mobile-menu-overlay {
            display: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const closeMenuButton = document.querySelector('.close-menu');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const body = document.body;

    // Function to open menu
    function openMenu() {
        mobileMenu.classList.add('active');
        mobileMenuOverlay.classList.add('active');
        body.style.overflow = 'hidden'; // Prevent scrolling when menu is open
    }

    // Function to close menu
    function closeMenu() {
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        body.style.overflow = ''; // Restore scrolling
    }

    // Event listeners
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', openMenu);
    }

    if (closeMenuButton) {
        closeMenuButton.addEventListener('click', closeMenu);
    }

    // Close menu when clicking on overlay
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMenu);
    }

    // Close menu when clicking on a menu link
    const menuLinks = mobileMenu.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // Close menu when pressing Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMenu();
        }
    });
});
</script>