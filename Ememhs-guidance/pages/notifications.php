<?php
session_start();
require_once '../logic/db_connection.php';
require_once '../logic/sql_querries.php';
require_once '../logic/notification_logic.php';

// Check if student is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['user'];
$role = $_SESSION['role'];
$notifications = null;
$unreadCount = 0;

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add debug logging
error_log("Session data: " . print_r($_SESSION, true));
error_log("User ID: " . $id);
error_log("Role: " . $role);

try {
    if ($role === 'admin') {
        $notifications = getAdminNotifications($id);
        $unreadCount = getAdminUnreadNotificationsCount($id);
        error_log("Admin notifications fetched: " . print_r($notifications, true));
    } else {
        $notifications = getStudentNotifications($id);
        $unreadCount = getUnreadNotificationsCount($id);
        error_log("Student notifications fetched: " . print_r($notifications, true));
    }
    
    // Mark all notifications as read when viewing the page
    if ($role === 'admin') {
        markAllAdminNotificationsAsRead($id);
        error_log("Marking admin notifications as read for ID: " . $id);
    } else {
        markAllNotificationsAsRead($id);
        error_log("Marking student notifications as read for ID: " . $id);
    }
} catch (Exception $e) {
    error_log("Error in notifications page: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    $notifications = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - <?php echo ucfirst($role); ?> Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .notification-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .notification-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            border-left-color: #800000;
            background-color: #fff5f5;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
        }
        .notification-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            opacity: 0;
        }
        .notification-details.active {
            max-height: 500px;
            opacity: 1;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
        }
        .chevron-icon {
            transition: transform 0.3s ease;
        }
        .chevron-icon.active {
            transform: rotate(180deg);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-badge.pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-badge.scheduled {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-badge.resolved {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-badge.found {
            background-color: #E0E7FF;
            color: #3730A3;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .notification-count-badge {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .notification-count-badge::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php 
    if ($_SESSION['role'] === 'student'){
        include 'navigation.php'; 
    }else{
        include 'navigation-admin.php'; 
    }
    ?>

    <main class="max-w-4xl mx-auto px-4 py-8<?php echo ($role !== 'student') ? ' pl-[180px] lg:pl-[180px] md:pl-[0] sm:pl-[0]' : ''; ?>">
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#800000] mb-2">Notifications Center</h1>
                    <p class="text-gray-600">Stay updated with your complaints, lost items, and counseling schedules</p>
                </div>
                <?php if ($unreadCount > 0): ?>
                <div class="notification-count-badge">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <?php echo $unreadCount; ?> new notification<?php echo $unreadCount > 1 ? 's' : ''; ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        

        <div class="bg-white rounded-lg shadow-lg overflow-hidden animate-fade-in">
            <?php if (empty($notifications)): ?>
                <div class="p-8 text-center">
                    <div class="bg-gray-50 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500 mb-4">You'll see updates here when your complaints are scheduled or resolved.</p>
                    <div class="text-sm text-gray-400">
                        <p>Notifications will appear for:</p>
                        <ul class="list-disc list-inside mt-2">
                            <li>Counseling schedule updates</li>
                            <li>Complaint status changes</li>
                            <li>Lost item updates</li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <h2 class="text-sm font-medium text-gray-700">Recent Updates</h2>
                            <?php if ($unreadCount > 0): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    <?php echo $unreadCount; ?> new
                                </span>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-gray-500"><?php echo count($notifications); ?> notification(s)</span>
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($notifications as $notification): ?>
                        <li class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                            <div class="p-4 cursor-pointer hover:bg-gray-50" onclick="toggleDetails(this)">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 relative">
                                        <?php if ($notification['type'] === 'scheduled'): ?>
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        <?php elseif ($notification['type'] === 'resolved'): ?>
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!$notification['is_read']): ?>
                                            <span class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full border-2 border-white"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($notification['message']); ?>
                                                </p>
                                                <div class="mt-1 flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500">
                                                        <?php 
                                                            $date = new DateTime($notification['date_created'] . ' ' . $notification['time_created']);
                                                            echo $date->format('F j, Y g:i A');
                                                        ?>
                                                    </span>
                                                    <?php if (!$notification['is_read']): ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            New
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 chevron-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($notification['reference_type'] === 'complaint'): ?>
                                <div class="notification-details p-4 bg-gray-50">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Notification Type</p>
                                            <p class="text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($notification['type']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Complaint Type</p>
                                            <p class="text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($notification['reference_type_detail']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Status</p>
                                            <p class="text-sm">
                                                <span class="status-badge <?php echo strtolower($notification['reference_status']); ?>">
                                                    <?php echo htmlspecialchars($notification['reference_status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <?php if ($notification['type'] === 'new_complaint'): ?>
                                        <div class="col-span-2 mt-4">
                                            <a href="complaint-concern-admin.php" 
                                               class="inline-flex items-center px-4 py-2 bg-[#800000] text-white rounded-lg hover:bg-[#a52a2a] transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                </svg>
                                                Manage Complaint
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php elseif ($notification['reference_type'] === 'lost_item'): ?>
                                <div class="notification-details p-4 bg-gray-50">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Notification Type</p>
                                            <p class="text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($notification['type']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Item Name</p>
                                            <p class="text-sm text-gray-900"><?php echo htmlspecialchars($notification['item_name'] ?? 'Not specified'); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Item Category</p>
                                            <p class="text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($notification['reference_type_detail']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Item Status</p>
                                            <p class="text-sm">
                                                <span class="status-badge <?php echo strtolower($notification['reference_status']); ?>">
                                                    <?php echo htmlspecialchars($notification['reference_status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Description</p>
                                            <p class="text-sm text-gray-900"><?php echo htmlspecialchars($notification['description'] ?? 'No description provided'); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Location Found</p>
                                            <p class="text-sm text-gray-900"><?php echo htmlspecialchars($notification['location_found'] ?? 'Not specified'); ?></p>
                                        </div>
                                        <?php if (!empty($notification['photo']) && !empty($notification['mime_type'])): ?>
                                        <div class="col-span-2">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Item Photo</p>
                                            <img src="data:<?php echo $notification['mime_type']; ?>;base64,<?php echo base64_encode($notification['photo']); ?>" 
                                                 alt="Item Photo" 
                                                 class="w-full h-auto max-h-48 object-contain rounded-lg shadow-sm" />
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function toggleDetails(element) {
            const details = element.nextElementSibling;
            const chevron = element.querySelector('.chevron-icon');
            
            details.classList.toggle('active');
            chevron.classList.toggle('active');
        }
    </script>
</body>
</html> 