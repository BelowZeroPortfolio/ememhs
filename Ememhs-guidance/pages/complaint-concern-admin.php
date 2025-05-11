<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';

// Check if staff is logged in
if (!$_SESSION['isLoggedIn']) {
    header("Location: index.php");
    exit();
}
$stmt = $pdo->prepare(SQL_LIST_COMPLAINTS_CONCERNS);
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count pending complaints for the tab badge
$pending_complaints = 0;
foreach ($complaints as $complaint) {
    if (isset($complaint['status']) && $complaint['status'] === 'pending') {
        $pending_complaints++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Guidance Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #800000;
            --primary-hover: #600000;
            --secondary-color: #64748b;
            --background-color: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
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

        .table-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            border-bottom: 2px solid #e2e8f0;
        }

        .table-row {
            transition: all 0.2s ease;
        }

        .table-row:hover {
            background-color: rgba(128, 0, 0, 0.05);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .status-pending {
            background-color: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
        }

        .status-scheduled {
            background-color: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
        }

        .status-resolved {
            background-color: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
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

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn i {
            transition: transform 0.2s ease;
        }

        .action-btn:hover i {
            transform: translateX(2px);
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

        .search-input {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            background-color: white;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem;
        }

        .time-slot-btn {
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            text-align: center;
        }

        .time-slot-btn.available {
            background-color: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
        }

        .time-slot-btn.available:hover {
            background-color: rgba(128, 0, 0, 0.2);
        }

        .time-slot-btn.selected {
            background-color: var(--primary-color);
            color: white;
        }

        .time-slot-btn.unavailable {
            background-color: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .tabs-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }

        .tabs-header {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1rem;
        }

        .tab-button {
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: var(--secondary-color);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-button:hover {
            color: var(--primary-color);
        }

        .tab-button.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-button i {
            font-size: 1.1rem;
        }

        .tab-content {
            display: none;
            padding: 1.5rem;
        }

        .tab-content.active {
            display: block;
        }

        .tab-badge {
            background-color: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body class="min-h-screen">
<?php include 'navigation-admin.php'?>
<div class="main-content">
    <main class=" min-h-screen">
        <div class="p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Welcome, <?php echo $_SESSION['staff_name'] ?? 'Staff'; ?></h1>
            
            <!-- Tabs Container -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" data-tab="pending">
                        <i class="fas fa-clock"></i>
                        Pending Complaints
                        <span class="tab-badge"><?= $pending_complaints ?></span>
                    </button>
                    <button class="tab-button" data-tab="all">
                        <i class="fas fa-list"></i>
                        All Complaints
                    </button>
                    <button class="tab-button" data-tab="scheduled">
                        <i class="fas fa-calendar-check"></i>
                        Scheduled Complaints
                    </button>
                </div>

                <!-- Pending Complaints Tab -->
                <div id="pending" class="tab-content active">
                    <div class="flex items-center mb-6">
                        <div class="bg-[#800000]/10 text-[#800000] rounded-full p-3 mr-4">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <h2 class="section-title text-xl font-semibold text-[#800000]">Pending Complaints</h2>
                        <div class="ml-auto">
                            <input type="text" placeholder="Search complaints..." class="search-input" />
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade & Section</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint Type</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidence</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php 
                                    $pending_found = false;
                                    if (!empty($complaints)):
                                        foreach ($complaints as $complaint):
                                            if ($complaint['status'] == 'pending'):
                                                $pending_found = true;
                                    ?>
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['first_name']." ".$complaint['last_name']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['grade_level']." ".$complaint['section']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['type']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['preferred_counseling_date']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if (!empty($complaint['evidence']) && !empty($complaint['mime_type'])): ?>
                                                    <img src="data:<?php echo $complaint['mime_type']; ?>;base64,<?php echo base64_encode($complaint['evidence']); ?>" 
                                                         alt="Evidence" 
                                                         class="w-16 h-16 object-cover rounded-lg shadow-sm hover:scale-110 transition-transform" />
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-500">No Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <button class="action-btn btn-primary set-schedule-btn" title="Set Schedule" data-complaint-id="<?= $complaint['id'] ?>">
                                                    <i class="fas fa-calendar-plus"></i>
                                                    Schedule
                                                </button>
                                                <button class="action-btn btn-secondary view-complaint ml-2" title="View Details"
                                                        data-complaint='<?php 
                                                            $complaintData = $complaint;
                                                            unset($complaintData['evidence']);
                                                            unset($complaintData['mime_type']);
                                                            echo json_encode($complaintData); 
                                                        ?>'
                                                        data-evidence='<?php echo !empty($complaint['evidence']) ? base64_encode($complaint['evidence']) : ''; ?>'
                                                        data-mime-type='<?php echo !empty($complaint['mime_type']) ? htmlspecialchars($complaint['mime_type']) : ''; ?>'>
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                            endif;
                                        endforeach;
                                    endif;
                                    if (!$pending_found):
                                    ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No pending complaints found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- All Complaints Tab -->
                <div id="all" class="tab-content">
                    <div class="flex items-center mb-6">
                        <div class="bg-[#800000]/10 text-[#800000] rounded-full p-3 mr-4">
                            <i class="fas fa-list text-xl"></i>
                        </div>
                        <h2 class="section-title text-xl font-semibold text-[#800000]">All Complaints</h2>
                        <div class="ml-auto">
                            <input type="text" placeholder="Search all complaints..." class="search-input" />
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade&&Section</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint Type</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidence</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($complaints)): ?>
                                        <?php foreach ($complaints as $complaint): ?>
                                            <tr class="table-row">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['first_name']." ".$complaint['last_name']; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['grade_level']." ".$complaint['section']; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['type']; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="status-badge status-<?php echo strtolower($complaint['status']); ?> flex items-center justify-center gap-1">
                                                        <?php if ($complaint['status'] === 'pending'): ?>
                                                            <i class="fas fa-clock"></i>
                                                        <?php elseif ($complaint['status'] === 'scheduled'): ?>
                                                            <i class="fas fa-calendar-check"></i>
                                                        <?php elseif ($complaint['status'] === 'resolved'): ?>
                                                            <i class="fas fa-check-circle"></i>
                                                        <?php endif; ?>
                                                        <?php echo ucfirst($complaint['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?php echo $complaint['preferred_counseling_date']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?php
                                                    if ($complaint['status'] == 'pending') {
                                                        echo '<span class="text-gray-500">Not scheduled yet</span>';
                                                    } else if ($complaint['status'] == 'scheduled') {
                                                        echo $complaint['scheduled_date'] . ' ' . $complaint['scheduled_time'];
                                                    } else if ($complaint['status'] == 'resolved') {
                                                        echo $complaint['scheduled_date'] . ' ' . $complaint['scheduled_time'] . " (Resolved)";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if (!empty($complaint['evidence']) && !empty($complaint['mime_type'])): ?>
                                                        <img src="data:<?php echo $complaint['mime_type']; ?>;base64,<?php echo base64_encode($complaint['evidence']); ?>" 
                                                             alt="Evidence" 
                                                             class="w-16 h-16 object-cover rounded-lg shadow-sm hover:scale-110 transition-transform" />
                                                    <?php else: ?>
                                                        <span class="text-sm text-gray-500">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <div class="flex items-center gap-2">
                                                        <?php if ($complaint['status'] === 'pending'): ?>
                                                            <button class="action-btn btn-primary set-schedule-btn" title="Set Schedule" data-complaint-id="<?= $complaint['id'] ?>">
                                                                <i class="fas fa-calendar-plus"></i>
                                                                Schedule
                                                            </button>
                                                        <?php endif; ?>
                                                        <button class="action-btn btn-secondary view-complaint" title="View Details"
                                                                data-complaint='<?php 
                                                                    $complaintData = $complaint;
                                                                    unset($complaintData['evidence']);
                                                                    unset($complaintData['mime_type']);
                                                                    echo json_encode($complaintData); 
                                                                ?>'
                                                                data-evidence='<?php echo !empty($complaint['evidence']) ? base64_encode($complaint['evidence']) : ''; ?>'
                                                                data-mime-type='<?php echo !empty($complaint['mime_type']) ? htmlspecialchars($complaint['mime_type']) : ''; ?>'>
                                                            <i class="fas fa-eye"></i>
                                                            View
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No complaints found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Complaints Tab -->
                <div id="scheduled" class="tab-content">
                    <div class="flex items-center mb-6">
                        <div class="bg-[#800000]/10 text-[#800000] rounded-full p-3 mr-4">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <h2 class="section-title text-xl font-semibold text-[#800000]">Scheduled Complaints</h2>
                        <div class="ml-auto">
                            <input type="text" placeholder="Search scheduled complaints..." class="search-input" />
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade&&Section</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint Type</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidence</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php 
                                    $scheduled_found = false;
                                    if (!empty($complaints)):
                                        foreach ($complaints as $complaint):
                                            if ($complaint['status'] == 'scheduled'):
                                                $scheduled_found = true;
                                    ?>
                                        <tr class="table-row">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['first_name']." ".$complaint['last_name']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['grade_level']." ".$complaint['section']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['type']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['scheduled_date']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['scheduled_time']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if (!empty($complaint['evidence']) && !empty($complaint['mime_type'])): ?>
                                                    <img src="data:<?php echo $complaint['mime_type']; ?>;base64,<?php echo base64_encode($complaint['evidence']); ?>" 
                                                         alt="Evidence" 
                                                         class="w-16 h-16 object-cover rounded-lg shadow-sm hover:scale-110 transition-transform" />
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-500">No Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form action="mark_resolved.php" method="POST" class="inline">
                                                    <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                                        Mark as Resolved
                                                    </button>
                                                </form>
                                                <button class="view-complaint bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm ml-2"
                                                        data-complaint='<?php 
                                                            $complaintData = $complaint;
                                                            // Remove binary data from the JSON
                                                            unset($complaintData['evidence']);
                                                            unset($complaintData['mime_type']);
                                                            echo json_encode($complaintData); 
                                                        ?>'
                                                        data-evidence='<?php echo !empty($complaint['evidence']) ? base64_encode($complaint['evidence']) : ''; ?>'
                                                        data-mime-type='<?php echo !empty($complaint['mime_type']) ? htmlspecialchars($complaint['mime_type']) : ''; ?>'>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                            endif;
                                        endforeach;
                                    endif;
                                    if (!$scheduled_found):
                                    ?>
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No scheduled complaints found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

    <!-- View Details Modal -->
    <div id="viewDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-4xl modal-content">
            <div class="modal-header flex justify-between items-center">
                <h3 class="text-xl font-semibold text-[#800000]">Complaint Details</h3>
                <button id="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Student Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-[#800000] mb-4 flex items-center">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Student Information
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">First Name</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewFirstName"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Last Name</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewLastName"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Grade Level</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewGradeLevel"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Section</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewSection"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Complaint Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-[#800000] mb-4 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Complaint Information
                            </h4>
                            <div class="space-y-4">
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Type</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewType"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Description</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewDescription"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewStatus"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Schedule Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-[#800000] mb-4 flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Schedule Information
                            </h4>
                            <div class="space-y-4">
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Preferred Date</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewPreferredDate"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Scheduled Date</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewScheduledDate"></p>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-sm text-gray-500">Scheduled Time</p>
                                    <p class="text-sm font-medium text-gray-900" id="viewScheduledTime"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Evidence -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-[#800000] mb-4 flex items-center">
                                <i class="fas fa-image mr-2"></i>
                                Evidence
                            </h4>
                            <div id="viewEvidence" class="bg-white p-3 rounded-md shadow-sm">
                                <!-- Evidence will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduling Modal -->
    <div id="schedulingModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm overflow-y-auto hidden">
        <div class="relative top-20 mx-auto p-6 w-[28rem] bg-white rounded-2xl shadow-xl transform transition-all">
            <div class="pb-4 border-b border-gray-100">
                <h3 class="text-xl font-semibold text-[#800000] flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule Counseling Session
                </h3>
            </div>
            <div class="py-6 space-y-6">
                <div>
                    <label for="scheduleDate" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" id="scheduleDate" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 transition duration-200 ease-in-out">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Time Slots</label>
                    <div id="timeSlots" class="grid grid-cols-3 gap-3">
                        <!-- Time slots will be populated here -->
                    </div>
                </div>
                <input type="hidden" id="selectedComplaintId">
            </div>
            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button id="cancelSchedule" class="px-4 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button id="confirmSchedule" class="px-4 py-2.5 rounded-xl bg-[#800000] text-white hover:bg-[#900000] transition duration-200 flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    Confirm Schedule
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    button.classList.add('active');
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            const viewModal = document.getElementById('viewDetailsModal');
            const closeViewModal = document.getElementById('closeViewModal');

            // Function to show complaint details
            function showComplaintDetails(complaint, evidence, mimeType) {
                // Set student information
                document.getElementById('viewFirstName').textContent = complaint.first_name;
                document.getElementById('viewLastName').textContent = complaint.last_name;
                document.getElementById('viewGradeLevel').textContent = complaint.grade_level;
                document.getElementById('viewSection').textContent = complaint.section;

                // Set complaint information
                document.getElementById('viewType').textContent = complaint.type;
                document.getElementById('viewDescription').textContent = complaint.description || 'No description provided';
                document.getElementById('viewStatus').textContent = complaint.status;
                document.getElementById('viewPreferredDate').textContent = complaint.preferred_counseling_date || 'N/A';
                document.getElementById('viewScheduledDate').textContent = complaint.scheduled_date || 'N/A';
                document.getElementById('viewScheduledTime').textContent = complaint.scheduled_time || 'N/A';

                // Set evidence
                const evidenceContainer = document.getElementById('viewEvidence');
                if (evidence && mimeType) {
                    evidenceContainer.innerHTML = `
                        <div class="relative group">
                            <img src="data:${mimeType};base64,${evidence}" 
                                 alt="Evidence" 
                                 class="w-full h-auto object-cover rounded-lg shadow-sm transition-transform duration-300 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300 rounded-lg"></div>
                        </div>
                    `;
                } else {
                    evidenceContainer.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No evidence provided</p>
                        </div>
                    `;
                }

                // Show modal
                viewModal.classList.remove('hidden');
            }

            // Add click handlers for view buttons
            document.querySelectorAll('.view-complaint').forEach(button => {
                button.addEventListener('click', function() {
                    const complaintData = JSON.parse(this.dataset.complaint);
                    const evidence = this.dataset.evidence;
                    const mimeType = this.dataset.mimeType;
                    showComplaintDetails(complaintData, evidence, mimeType);
                });
            });

            // Close modal handler
            closeViewModal.addEventListener('click', function() {
                viewModal.classList.add('hidden');
            });

            // Close modal when clicking outside
            viewModal.addEventListener('click', function(e) {
                if (e.target === viewModal) {
                    viewModal.classList.add('hidden');
                }
            });

            // Scheduling Modal functionality
            const schedulingModal = document.getElementById('schedulingModal');
            const scheduleDate = document.getElementById('scheduleDate');
            const timeSlots = document.getElementById('timeSlots');
            const selectedComplaintId = document.getElementById('selectedComplaintId');
            const cancelSchedule = document.getElementById('cancelSchedule');
            const confirmSchedule = document.getElementById('confirmSchedule');
            let currentComplaintId = null;

            // Set minimum date to today
            scheduleDate.min = new Date().toISOString().split('T')[0];

            // Generate time slots
            function generateTimeSlots() {
                const slots = [];
                const startHour = 8; // 8 AM
                const endHour = 17; // 5 PM
                
                for (let hour = startHour; hour <= endHour; hour++) {
                    const time = `${hour}:00`;
                    slots.push(time);
                }
                
                return slots;
            }

            // Check time slot availability
            async function checkTimeSlotAvailability(date, time) {
                try {
                    const response = await fetch('check_time_slot.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ date, time })
                    });
                    const data = await response.json();
                    return data.available;
                } catch (error) {
                    console.error('Error checking time slot:', error);
                    return false;
                }
            }

            // Update time slots display
            async function updateTimeSlots(date) {
                timeSlots.innerHTML = '';
                const slots = generateTimeSlots();
                
                for (const time of slots) {
                    const isAvailable = await checkTimeSlotAvailability(date, time);
                    const button = document.createElement('button');
                    button.className = `px-3 py-2 rounded-md text-sm ${
                        isAvailable 
                            ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                            : 'bg-red-100 text-red-800 cursor-not-allowed'
                    }`;
                    button.textContent = time;
                    button.disabled = !isAvailable;
                    button.onclick = () => {
                        document.querySelectorAll('#timeSlots button').forEach(btn => {
                            btn.classList.remove('bg-indigo-100', 'text-indigo-800');
                        });
                        if (isAvailable) {
                            button.classList.add('bg-indigo-100', 'text-indigo-800');
                        }
                    };
                    timeSlots.appendChild(button);
                }
            }

            // Event listeners
            scheduleDate.addEventListener('change', () => {
                updateTimeSlots(scheduleDate.value);
            });

            cancelSchedule.addEventListener('click', () => {
                schedulingModal.classList.add('hidden');
            });

            confirmSchedule.addEventListener('click', async () => {
                const selectedTime = document.querySelector('#timeSlots button.bg-indigo-100');
                if (!selectedTime) {
                    alert('Please select a time slot');
                    return;
                }

                try {
                    const requestData = {
                        complaint_id: selectedComplaintId.value,
                        date: scheduleDate.value,
                        time: selectedTime.textContent
                    };
                    
                    console.log('Sending request:', requestData);
                    
                    const response = await fetch('set-schedule.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    // Get the raw response text
                    const responseText = await response.text();
                    console.log('Raw response:', responseText);
                    
                    // Extract just the JSON part (everything before the first [DEBUG])
                    const jsonPart = responseText.split('[DEBUG]')[0];
                    console.log('JSON part:', jsonPart);
                    
                    // Try to parse as JSON
                    let data;
                    try {
                        data = JSON.parse(jsonPart);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('JSON part:', jsonPart);
                        alert('Error parsing server response. Check console for details.');
                        return;
                    }

                    if (data.success) {
                        alert('Schedule set successfully');
                        window.location.reload();
                    } else {
                        alert('Error setting schedule: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error setting schedule:', error);
                    console.error('Full error details:', {
                        message: error.message,
                        stack: error.stack
                    });
                    alert('Error setting schedule: ' + error.message);
                }
            });

            // Update Set Schedule button click handler
            document.querySelectorAll('.set-schedule-btn').forEach(button => {
                button.addEventListener('click', () => {
                    currentComplaintId = button.dataset.complaintId;
                    selectedComplaintId.value = currentComplaintId;
                    schedulingModal.classList.remove('hidden');
                });
            });

            // Search functionality
            const searchInputs = document.querySelectorAll('.search-input');
            
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const tabId = this.closest('.tab-content').id;
                    const table = document.querySelector(`#${tabId} table tbody`);
                    const rows = table.querySelectorAll('tr');
                    let hasVisibleRows = false;

                    rows.forEach(row => {
                        // Skip the "no results" row if it exists
                        if (row.querySelector('td[colspan]')) {
                            row.style.display = 'none';
                            return;
                        }

                        const cells = row.querySelectorAll('td');
                        let rowVisible = false;

                        cells.forEach(cell => {
                            const cellText = cell.textContent.toLowerCase().trim();
                            if (cellText.includes(searchTerm)) {
                                rowVisible = true;
                            }
                        });

                        row.style.display = rowVisible ? '' : 'none';
                        if (rowVisible) {
                            hasVisibleRows = true;
                        }
                    });

                    // Handle "no results" message
                    const noResultsRow = table.querySelector('tr td[colspan]');
                    if (!hasVisibleRows) {
                        if (!noResultsRow) {
                            const newRow = document.createElement('tr');
                            const cell = document.createElement('td');
                            cell.colSpan = tabId === 'scheduled' ? 7 : 8;
                            cell.className = 'px-6 py-4 text-center text-sm text-gray-500';
                            cell.textContent = 'No matching complaints found';
                            newRow.appendChild(cell);
                            table.appendChild(newRow);
                        } else {
                            noResultsRow.style.display = '';
                        }
                    } else if (noResultsRow) {
                        noResultsRow.style.display = 'none';
                    }
                });
            });

            // Add search input styling
            const style = document.createElement('style');
            style.textContent += `
                .search-input {
                    transition: all 0.2s ease-in-out;
                }
                .search-input:focus {
                    box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
                }
                .search-input::placeholder {
                    color: #94a3b8;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                tr {
                    animation: fadeIn 0.2s ease-in-out;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html> 