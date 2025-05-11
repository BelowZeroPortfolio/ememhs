<?php
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])){
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare(SQL_LIST_COMPLAINTS_CONCERNS_BY_STUDENT);
$stmt->execute([$student_id]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint/Concern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .table-container {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 55, 72, 0.2);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
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

        .status-resolved {
            background-color: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include 'navigation.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-[#800000]">Your Complaints</h1>
            <a href="complaint-concern-form.php" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold">
                Add New Complaint
            </a>
        </div>

        <div class="table-container">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidence</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($complaints)): ?>
                            <?php foreach ($complaints as $complaint): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $complaint['type']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo $complaint['description']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-<?php echo strtolower($complaint['status']); ?>">
                                            <?php echo ucfirst($complaint['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $complaint['preferred_counseling_date']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php
                                        if ($complaint['status'] == 'pending') {
                                            echo 'N/A';
                                        } else if ($complaint['status'] == 'scheduled') {
                                            echo $complaint['scheduled_date'];
                                        } else if ($complaint['status'] == 'resolved') {
                                            echo $complaint['scheduled_date'] . " (Resolved)";
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($complaint['evidence']) && !empty($complaint['mime_type'])): ?>
                                            <img src="data:<?php echo $complaint['mime_type']; ?>;base64,<?php echo base64_encode($complaint['evidence']); ?>" 
                                                 alt="Evidence" 
                                                 class="w-16 h-16 object-cover rounded-lg shadow-sm" />
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="complaint-concern-form.php" method="POST" class="inline">
                                            <input type="hidden" name="user" value="<?= $complaint['id'] ?>">
                                            <button type="submit" class="btn-secondary text-white px-4 py-2 rounded-lg text-sm">
                                                Edit
                                            </button>
                                        </form>
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
    </main>
</body>

</html>