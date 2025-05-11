<?php
require_once '../logic/notification_logic.php';
// Check if user is logged in
$isLoggedIn = isset($_SESSION['isLoggedIn']);

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 bg-white shadow-lg transform transition-all duration-300 ease-in-out z-50" id="sidebar">
    <!-- Logo Section -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 sidebar-header">
        <div class="flex items-center space-x-2 sidebar-logo">
            <img src="../image/ememhs-logo.png" alt="EMEMHS Logo" class="sidebar-logo-img h-8 w-auto max-w-[40px] mx-auto">
            <span class="text-xl font-bold text-[#800000] whitespace-nowrap sidebar-logo-text">Guidance System</span>
        </div>
        <div class="flex items-center space-x-2 sidebar-toggle">
            <button class="hidden lg:flex text-gray-500 hover:text-gray-700 focus:outline-none" id="toggleSidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
            <button class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none" id="closeSidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="px-4 py-6 space-y-2 overflow-y-auto h-[calc(100vh-4rem)]">
        <?php if ($isLoggedIn): ?>
            <a href="staff-dashboard.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'staff-dashboard.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : 'text-gray-700 hover:text-[#800000] hover:bg-gray-50'; ?>" tabindex="0" data-tooltip="Dashboard">
                <span class="icon-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                </span>
                <span class="truncate">Dashboard</span>
                <span class="sidebar-tooltip">Dashboard</span>
            </a>

            <a href="complaint-concern-admin.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'complaint-concern-admin.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : 'text-gray-700 hover:text-[#800000] hover:bg-gray-50'; ?>" tabindex="0" data-tooltip="Manage Complaints">
                <span class="icon-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                </span>
                <span class="truncate">Manage Complaints</span>
                <span class="sidebar-tooltip">Manage Complaints</span>
            </a>

            <a href="found-items.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'found-items.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : 'text-gray-700 hover:text-[#800000] hover:bg-gray-50'; ?>" tabindex="0" data-tooltip="Found Items">
                <span class="icon-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                </span>
                <span class="truncate">Found Items</span>
                <span class="sidebar-tooltip">Found Items</span>
            </a>

            <a href="students-list.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'students-list.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : 'text-gray-700 hover:text-[#800000] hover:bg-gray-50'; ?>" tabindex="0" data-tooltip="Students">
                <span class="icon-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                </span>
                <span class="truncate">Students</span>
                <span class="sidebar-tooltip">Students</span>
            </a>

            <div class="border-t border-gray-200 my-4"></div>

            <a href="notifications.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'notifications.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : 'text-gray-700 hover:text-[#800000] hover:bg-gray-50'; ?>" tabindex="0" data-tooltip="Notifications">
                <span class="icon-center relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <?php 
                $unread_count = getAdminUnreadNotificationsCount($_SESSION['user']);
                if ($unread_count > 0): 
                ?>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"><?php echo $unread_count; ?></span>
                <?php endif; ?>
                </span>
                <span class="truncate">Notifications</span>
                <span class="sidebar-tooltip">Notifications</span>
            </a>


            <div class="border-t border-gray-200 my-4"></div>

            <a href="../logic/logout_logic.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 <?php echo ($current_page == 'logout_logic.php') ? 'bg-red-100 font-medium' : ''; ?>" tabindex="0" data-tooltip="Logout">
                <span class="icon-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                </span>
                <span class="truncate">Logout</span>
                <span class="sidebar-tooltip">Logout</span>
            </a>
        <?php else: ?>
            <a href="index.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:text-[#800000] hover:bg-gray-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'index.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : ''; ?>" tabindex="0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Login</span>
            </a>
            <a href="register.php" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:text-[#800000] hover:bg-gray-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#800000] <?php echo ($current_page == 'register.php') ? 'bg-[#800000]/10 text-[#800000] font-medium' : ''; ?>" tabindex="0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Register</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Menu Button -->
<button class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-[#800000] text-white shadow-lg focus:outline-none" id="openSidebar">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300" id="overlay"></div>

<style>
    /* Sidebar styles */
    #sidebar {
        width: 280px;
        transition: all 0.3s ease-in-out;
    }
    #sidebar.collapsed {
        width: 80px;
    }
    #sidebar.collapsed .truncate {
        display: none;
    }
    #sidebar.collapsed .space-x-3 {
        margin-left: 0;
        margin-right: 0;
    }
    #sidebar.collapsed .px-4 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    #sidebar.collapsed .justify-between {
        justify-content: center;
    }
    #sidebar.collapsed #toggleSidebar svg {
        transform: rotate(180deg);
    }
    #sidebar.collapsed .whitespace-nowrap {
        display: none;
    }

    /* Mobile sidebar styles */
    @media (max-width: 1024px) {
        #sidebar {
            transform: translateX(-100%);
        }
        #sidebar.active {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
    @media (min-width: 1025px) {
        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease-in-out;
        }
        .main-content.expanded {
            margin-left: 80px;
            width: calc(100% - 80px);
        }
        #openSidebar {
            display: none;
        }
    }
    @media (max-width: 640px) {
        #sidebar {
            width: 100%;
        }
    }

    /* Scrollbar styles */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #F7FAFC;
    }
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #F7FAFC;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: #CBD5E0;
        border-radius: 3px;
    }

    #sidebar.collapsed .sidebar-header {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        height: 80px;
    }
    #sidebar.collapsed .sidebar-logo {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    #sidebar.collapsed .sidebar-logo-img {
        margin: 0 auto;
        display: block;
        height: 40px;
        width: 40px;
        max-width: 40px;
    }
    #sidebar.collapsed .sidebar-logo-text {
        display: none;
    }
    #sidebar.collapsed .sidebar-toggle {
        justify-content: center;
        width: 100%;
        margin-top: 0.5rem;
    }

    /* Center icons in collapsed mode */
    #sidebar.collapsed .icon-center {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    #sidebar .icon-center {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 24px;
    }
    /* Hide text in collapsed mode */
    #sidebar.collapsed .truncate {
        display: none !important;
    }
    /* Tooltip styles */
    .sidebar-tooltip {
        display: block;
        visibility: hidden;
        position: fixed;
        left: 80px;
        top: 50%;
        transform: translateY(-50%);
        background: #222;
        color: #fff;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        white-space: nowrap;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    #sidebar.collapsed .nav-link {
        position: relative;
        justify-content: center;
    }
    #sidebar.collapsed .nav-link:hover .sidebar-tooltip {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
        top: 50%;
    }
    #sidebar.collapsed .sidebar-tooltip {
        left: 80px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const mainContent = document.querySelector('.main-content');

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        if (mainContent && window.innerWidth >= 1025) {
            mainContent.classList.toggle('expanded');
        }
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    // Event listeners
    if (openSidebarBtn) {
        openSidebarBtn.addEventListener('click', openSidebar);
    }
    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', closeSidebar);
    }
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', toggleSidebar);
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });

    // Close sidebar on link click (mobile)
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                closeSidebar();
            }
        });
    });

    // Handle window resize
    function handleResize() {
        if (window.innerWidth >= 1025) {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
            // Restore expanded/collapsed state
            if (sidebar.classList.contains('collapsed')) {
                mainContent.classList.add('expanded');
            } else {
                mainContent.classList.remove('expanded');
            }
        } else {
            // On mobile, always remove expanded
            if (mainContent) mainContent.classList.remove('expanded');
        }
    }

    // Restore sidebar state from localStorage
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) {
            mainContent.classList.add('expanded');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check
});
</script>