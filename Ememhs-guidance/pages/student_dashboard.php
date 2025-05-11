<?php
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])){
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}
$student_id = $_SESSION['student_id'];

// Get student details
$stmt = $pdo->prepare("SELECT first_name FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Get notification count
$unread_count = getUnreadNotificationsCount($_SESSION['user']);

// Get recent notifications
$stmt = $pdo->prepare(SQL_GET_STUDENT_NOTIFICATIONS);
$stmt->execute([$_SESSION['user']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent complaints
$stmt = $pdo->prepare("SELECT * FROM complaints_concerns WHERE student_id = ? ORDER BY date_created DESC, time_created DESC LIMIT 3");
$stmt->execute([$student_id]);
$recent_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent lost items
$stmt = $pdo->prepare("SELECT * FROM lost_items WHERE student_id = ? ORDER BY date DESC, time DESC LIMIT 3");
$stmt->execute([$student_id]);
$recent_lost_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stmt = $pdo->prepare(SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT);
$stmt->execute([$student_id]);
$total_complaints = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT_STATUS);
$stmt->execute([$student_id, 'pending']);
$pending_complaints = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT_STATUS);
$stmt->execute([$student_id, 'scheduled']);
$scheduled_complaints = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT_STATUS);
$stmt->execute([$student_id, 'resolved']);
$resolved_complaints = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_LOST_ITEMS_BY_STUDENT);
$stmt->execute([$student_id]);
$total_lost_items = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_LOST_ITEMS_BY_STUDENT_STATUS);
$stmt->execute([$student_id, 'pending']);
$pending_lost_items = $stmt->fetchColumn();

$stmt = $pdo->prepare(SQL_SUM_LIST_LOST_ITEMS_BY_STUDENT_STATUS);
$stmt->execute([$student_id, 'found']);
$found_lost_items = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EMEMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-card {
            background: white;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #800000;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #800000;
            transition: width 0.3s ease;
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
            border: 2px solid white;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-scheduled {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-resolved {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-found {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .quick-info-card {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            color: white;
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include 'navigation.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($student['first_name']) ?>!</h1>
                <p class="text-gray-600 mt-1">
                    You have <?= $pending_complaints ?> unresolved concerns and <?= $pending_lost_items ?> pending lost item reports.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="profile.php" class="text-sm text-gray-600 hover:text-[#800000] flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    View Profile
                </a>
            </div>
        </div>

        <!-- Quick Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="quick-info-card rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white/80">Total Complaints</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $total_complaints ?></h3>
                    </div>
                    <div class="p-3 bg-white/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-white/80"><?= $resolved_complaints ?> resolved</span>
                    <span class="mx-2 text-white/60">•</span>
                    <span class="text-white/80"><?= $pending_complaints ?> pending</span>
                </div>
            </div>

            <div class="quick-info-card rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white/80">Lost Items</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $total_lost_items ?></h3>
                    </div>
                    <div class="p-3 bg-white/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-white/80"><?= $found_lost_items ?> found</span>
                    <span class="mx-2 text-white/60">•</span>
                    <span class="text-white/80"><?= $pending_lost_items ?> pending</span>
                </div>
            </div>

            <div class="quick-info-card rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white/80">Scheduled Sessions</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $scheduled_complaints ?></h3>
                    </div>
                    <div class="p-3 bg-white/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-white/80">Next session: <?= $scheduled_complaints > 0 ? 'Scheduled' : 'None' ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="complaint-concern-form.php" class="dashboard-card p-6 flex items-center space-x-4">
                <div class="p-3 bg-[#800000]/10 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">New Complaint</h3>
                    <p class="text-sm text-gray-600">Submit a new complaint or concern</p>
                </div>
            </a>

            <a href="lost-item-form.php" class="dashboard-card p-6 flex items-center space-x-4">
                <div class="p-3 bg-[#800000]/10 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Report Lost Item</h3>
                    <p class="text-sm text-gray-600">Report a lost or found item</p>
                </div>
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Complaints -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Complaints Overview -->
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Complaints</h2>
                        <a href="complaint-concern.php" class="text-sm text-[#800000] hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php if (empty($recent_complaints)): ?>
                            <p class="text-gray-500 text-center py-4">No recent complaints</p>
                        <?php else: ?>
                            <?php foreach ($recent_complaints as $complaint): ?>
                                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-800"><?= htmlspecialchars($complaint['type']) ?></h3>
                                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars(substr($complaint['description'], 0, 100)) ?>...</p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Submitted on <?= date('M d, Y', strtotime($complaint['date_created'])) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge status-<?= strtolower($complaint['status']) ?>">
                                            <?= ucfirst($complaint['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Lost Items Overview -->
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Lost Items</h2>
                        <a href="lost_item.php" class="text-sm text-[#800000] hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php if (empty($recent_lost_items)): ?>
                            <p class="text-gray-500 text-center py-4">No recent lost items</p>
                        <?php else: ?>
                            <?php foreach ($recent_lost_items as $item): ?>
                                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-800"><?= htmlspecialchars($item['item_name']) ?></h3>
                                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($item['description']) ?></p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Reported on <?= date('M d, Y', strtotime($item['date'])) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge status-<?= strtolower($item['status']) ?>">
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Notifications Panel -->
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
                        <a href="notifications.php" class="text-sm text-[#800000] hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php if (empty($notifications)): ?>
                            <p class="text-gray-500 text-center py-4">No new notifications</p>
                        <?php else: ?>
                            <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <p class="text-sm text-gray-800"><?= htmlspecialchars($notification['message']) ?></p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <?= date('M d, Y', strtotime($notification['date_created'])) ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="dashboard-card p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h2>
                    <div class="space-y-3">
                        <a href="guidance-resources.php" class="flex items-center text-gray-600 hover:text-[#800000]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Guidance Resources
                        </a>
                        <a href="faq.php" class="flex items-center text-gray-600 hover:text-[#800000]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            FAQ
                        </a>
                        <a href="contact.php" class="flex items-center text-gray-600 hover:text-[#800000]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Guidance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/mobile-menu.js"></script>
</body>

</html>