<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';

// Check if staff is logged in
if (!$_SESSION['isLoggedIn']) {
    header("Location: index.php");
    exit();
}

// Get total number of complaints
$stmt = $pdo->prepare(SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STATUS);
$stmt->execute(['pending']);
$pending_complaints = $stmt->fetchColumn();

// Get monthly complaints data for the chart
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(date_created, '%Y-%m') as month,
        COUNT(*) as count
    FROM " . TBL_COMPLAINTS_CONCERNS . "
    WHERE date_created >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date_created, '%Y-%m')
    ORDER BY month ASC
");
$stmt->execute();
$monthly_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get lost items distribution
$stmt = $pdo->prepare("
    SELECT 
        category,
        COUNT(*) as count
    FROM " . TBL_LOST_ITEMS . "
    GROUP BY category
    ORDER BY count DESC
    LIMIT 4
");
$stmt->execute();
$lost_items_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of lost items
$stmt = $pdo->prepare("SELECT COUNT(*) FROM " . TBL_LOST_ITEMS . " WHERE status = 'found'");
$stmt->execute();
$found_items  = $stmt->fetchColumn();

// Get total number of students
$stmt = $pdo->prepare("SELECT COUNT(*) FROM " . TBL_STUDENTS);
$stmt->execute();
$total_students = $stmt->fetchColumn();

// Get recent complaints
$stmt = $pdo->prepare("
    SELECT c.*, s.first_name, s.last_name 
    FROM " . TBL_COMPLAINTS_CONCERNS . " c 
    JOIN " . TBL_STUDENTS . " s ON c.student_id = s.id 
    ORDER BY c.date_created DESC 
    LIMIT 5
");
$stmt->execute();
$recent_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent lost items
$stmt = $pdo->prepare("
    SELECT l.*, s.first_name, s.last_name 
    FROM " . TBL_LOST_ITEMS . " l 
    JOIN " . TBL_STUDENTS . " s ON l.student_id = s.id 
    ORDER BY l.date DESC 
    LIMIT 5
");
$stmt->execute();
$recent_lost_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent notifications
$stmt = $pdo->prepare("
    SELECT n.*, s.first_name, s.last_name 
    FROM " . TBL_NOTIFICATIONS . " n 
    JOIN " . TBL_STUDENTS . " s ON n.user_id = s.id 
    ORDER BY n.date_created DESC 
    LIMIT 5
");
$stmt->execute();
$recent_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unread notifications count
$unread_count = getUnreadNotificationsCount($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Guidance Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #800000;
            --primary-hover: #600000;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #ffffff;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .minimal-card {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .minimal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .minimal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .minimal-card:hover::before {
            opacity: 1;
        }

        .minimal-btn {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .minimal-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .minimal-btn i {
            transition: transform 0.2s ease;
        }

        .minimal-btn:hover i {
            transform: translateX(4px);
        }

        .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            transition: width 0.5s ease;
            position: relative;
        }

        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(128,0,0,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::after {
            opacity: 1;
        }

        .chart-container {
            position: relative;
            padding: 1rem;
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            border-radius: 0.5rem;
        }

        .chart-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
        }

        .activity-item {
            position: relative;
            padding-left: 1.5rem;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-color);
            opacity: 0.2;
        }

        .activity-item:last-child::before {
            height: 50%;
        }

        .activity-item::after {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-color);
            opacity: 0.2;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .sidebar {
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .sidebar-item {
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
        }

        .sidebar-item:hover {
            background-color: #f1f5f9;
            transform: translateX(4px);
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            animation: pulse 2s infinite;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification-icon {
            position: relative;
            transition: all 0.3s ease;
        }

        .notification-icon:hover {
            transform: scale(1.1);
        }

        .notification-icon.has-notifications {
            color: var(--primary-color);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-scheduled {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .table-container {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-row {
            transition: all 0.2s ease;
        }

        .table-row:hover {
            background-color: #f8fafc;
        }

        .gradient-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }

        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen bg-white">

<?php include 'navigation-admin.php'?>
<div class="main-content">
    <main class="pt-16 min-h-screen">
        <!-- Welcome Section with Notification Bell -->
        <div class="mb-8 flex justify-between items-center px-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, <?php echo $_SESSION['staff_name'] ?? 'Staff'; ?></h1>
                <p class="text-gray-600">Here's what's happening today</p>
            </div>
            <a href="notifications.php" class="notification-icon <?php echo $unread_count > 0 ? 'has-notifications' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600 hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <?php if ($unread_count > 0): ?>
                    <span class="notification-badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="p-8">
            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="minimal-card p-6 stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Pending Complaints</p>
                            <h3 class="text-3xl font-bold text-[#800000]"><?=$pending_complaints?></h3>
                            <div class="progress-bar mt-2">
                                <div class="progress-bar-fill bg-[#800000]" style="width: <?= min(($pending_complaints / max($total_students, 1)) * 100, 100) ?>%"></div>
                            </div>
                        </div>
                        <div class="text-[#800000] transform hover:scale-110 transition-transform">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="minimal-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Found Items</p>
                            <h3 class="text-3xl font-bold text-[#800000]"><?=$found_items?></h3>
                            <div class="progress-bar mt-2">
                                <div class="progress-bar-fill bg-[#800000]" style="width: <?= min(($found_items / max($total_students, 1)) * 100, 100) ?>%"></div>
                            </div>
                        </div>
                        <div class="text-[#800000]">
                            <i class="fas fa-box text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="minimal-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Students</p>
                            <h3 class="text-3xl font-bold text-[#800000]"><?=$total_students?></h3>
                            <div class="progress-bar mt-2">
                                <div class="progress-bar-fill bg-[#800000]" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="text-[#800000]">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="minimal-card chart-container">
                    <h2 class="section-title text-sm font-medium text-[#800000]">Lost Items Distribution</h2>
                    <div class="h-48">
                        <canvas id="lostItemsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="minimal-card p-6 mb-8">
                <h2 class="section-title text-sm font-medium text-[#800000]">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="complaint-concern-admin.php" class="minimal-btn px-4 py-2 rounded-lg flex items-center hover:bg-[#800000] hover:text-white transition-all duration-300">
                        <i class="fas fa-clipboard-list mr-2"></i>View Complaints
                    </a>
                    <a href="found-items.php" class="minimal-btn px-4 py-2 rounded-lg flex items-center hover:bg-[#800000] hover:text-white transition-all duration-300">
                        <i class="fas fa-box mr-2"></i>View Found Items
                    </a>
                    <a href="students-list.php" class="minimal-btn px-4 py-2 rounded-lg flex items-center hover:bg-[#800000] hover:text-white transition-all duration-300">
                        <i class="fas fa-users mr-2"></i>View Students
                    </a>
                </div>
            </div>

            <!-- Recent Activities and Lost Items -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="minimal-card p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="section-title text-sm font-medium text-[#800000]">Recent Concerns</h2>
                            <a href="complaint-concern-admin.php" class="text-xs text-[#800000] hover:text-[#800000]/80 flex items-center">
                                View All <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ($recent_complaints as $complaint): ?>
                                <div class="minimal-card p-3 border border-gray-100 activity-item">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <?php
                                            $icon_class = '';
                                            $text_class = 'text-[#800000]';
                                            switch($complaint['type']) {
                                                case 'family_problems':
                                                    $icon_class = 'fas fa-home';
                                                    break;
                                                case 'academic_stress':
                                                    $icon_class = 'fas fa-book';
                                                    break;
                                                case 'peer_relationship':
                                                    $icon_class = 'fas fa-users';
                                                    break;
                                                default:
                                                    $icon_class = 'fas fa-comment';
                                            }
                                            ?>
                                            <div class="<?= $text_class ?>">
                                                <i class="<?= $icon_class ?> text-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                                    <?php 
                                                    $type_display = $complaint['type'];
                                                    if ($type_display === 'peer_relationship') {
                                                        $type_display = 'Peer Pressure';
                                                    } else {
                                                        $type_display = ucwords(str_replace('_', ' ', $type_display));
                                                    }
                                                    echo $type_display;
                                                    ?>
                                                </h3>
                                                <span class="text-xs px-2 py-1 rounded-full <?= 
                                                    $complaint['status'] === 'pending' ? 'bg-[#800000]/10 text-[#800000]' : 
                                                    ($complaint['status'] === 'scheduled' ? 'bg-[#800000]/10 text-[#800000]' : 'bg-[#800000]/10 text-[#800000]')
                                                ?>">
                                                    <?= ucfirst($complaint['status']) ?>
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-2 line-clamp-2">
                                                <?= htmlspecialchars($complaint['description'] ?? 'No description provided') ?>
                                            </p>
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <div class="flex items-center space-x-3">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-user-circle mr-1"></i>
                                                        <?= htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']) ?>
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        <?= date('M d, Y', strtotime($complaint['date_created'])) ?>
                                                    </span>
                                                </div>
                                                <a href="complaint-concern-admin.php?id=<?= $complaint['id'] ?>" 
                                                   class="text-[#800000] hover:text-[#800000]/80">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="minimal-card p-4">
                        <h2 class="section-title text-sm font-medium text-[#800000] mb-4">Recent Lost Items</h2>
                        <div class="space-y-3">
                            <?php foreach ($recent_lost_items as $item): ?>
                                <div class="minimal-card p-3 border border-gray-100 activity-item">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($item['photo']) && !empty($item['mime_type'])): ?>
                                                <img src="data:<?= $item['mime_type'] ?>;base64,<?= base64_encode($item['photo']) ?>" 
                                                     alt="Item Photo" 
                                                     class="w-12 h-12 object-cover rounded">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-50 rounded flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                                    <?= htmlspecialchars($item['item_name']) ?>
                                                </h3>
                                                <span class="text-xs px-2 py-1 rounded-full <?= 
                                                    $item['status'] === 'pending' ? 'bg-[#800000]/10 text-[#800000]' : 'bg-[#800000]/10 text-[#800000]'
                                                ?>">
                                                    <?= ucfirst($item['status']) ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    <?= date('M d, Y', strtotime($item['date'])) ?>
                                                </span>
                                                <button onclick="notifyStudent(<?= $item['id'] ?>)" 
                                                        class="text-[#800000] hover:text-[#800000]/80">
                                                    <i class="fas fa-bell mr-1"></i> Notify
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div class="fixed right-0 top-16 bottom-0 w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out" id="notificationsPanel">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">System Notifications</h2>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto max-h-[calc(100vh-8rem)]">
                    <?php foreach ($recent_notifications as $notification): ?>
                    <div class="card p-4 hover:shadow-md transition-shadow">
                        <div class="text-sm text-gray-500 mb-2">
                            <?= date('h:i A', strtotime($notification['time_created'])) ?>
                        </div>
                        <p class="text-gray-800">
                            <?= htmlspecialchars($notification['message']) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Profile dropdown toggle
            const profileButton = document.querySelector('.relative button');
            const profileDropdown = document.querySelector('.relative .absolute');
            
            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }

            // Toggle notifications panel
            const notificationsButton = document.querySelector('.fa-bell').parentElement;
            const notificationsPanel = document.getElementById('notificationsPanel');
            
            notificationsButton.addEventListener('click', function() {
                notificationsPanel.classList.toggle('translate-x-full');
            });

            // Close notifications panel when clicking outside
            document.addEventListener('click', function(event) {
                if (!notificationsButton.contains(event.target) && !notificationsPanel.contains(event.target)) {
                    notificationsPanel.classList.add('translate-x-full');
                }
            });

            // Add click event listeners to all notify buttons
            const notifyButtons = document.querySelectorAll('.notify-btn');
            notifyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    if (itemId) {
                        notifyStudent(itemId);
                    }
                });
            });

            // Prepare chart data from PHP variables
            const monthlyData = <?= json_encode($monthly_complaints) ?>;
            const lostItemsData = <?= json_encode($lost_items_distribution) ?>;

            // Lost Items Chart with enhanced styling
            const lostItemsCtx = document.getElementById('lostItemsChart').getContext('2d');
            new Chart(lostItemsCtx, {
                type: 'doughnut',
                data: {
                    labels: lostItemsData.map(item => item.category),
                    datasets: [{
                        data: lostItemsData.map(item => item.count),
                        backgroundColor: [
                            '#800000',
                            '#80000080',
                            '#80000060',
                            '#80000040'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 10,
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    },
                    cutout: '75%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });

        // Function to notify student about a found item
        function notifyStudent(itemId) {
            if (!confirm('Are you sure you want to notify the student about this found item?')) {
                return;
            }

            const formData = new FormData();
            formData.append('item_id', itemId);

            fetch('notify_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Refresh the page or update the UI as needed
                    location.reload();
                } else {
                    alert(data.message || 'Failed to notify student');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while notifying the student');
            });
        }
    </script>
</body>
</html> 