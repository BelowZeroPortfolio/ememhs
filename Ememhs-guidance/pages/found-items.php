<?php
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])) {
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}
$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare(SQL_LIST_LOST_ITEMS_BY_STUDENT);
$stmt->execute([$student_id]);
$lost_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user is logged in and is admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
$foundItems = [];
try {
    // Get all found items
    $stmt = $pdo->prepare("
        SELECT li.*, s.first_name, s.last_name, s.id AS student_number
        FROM lost_items li
        LEFT JOIN students s ON li.student_id = s.id
        WHERE li.status = 'found'
        ORDER BY li.date DESC, li.time DESC;
    ");
    $stmt->execute();
    $foundItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching found items: " . $e->getMessage());
    $foundItems = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items - Guidance Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .header-gradient {
            background: linear-gradient(135deg, var(--primary-color) 0%, #a52a2a 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-found {
            background-color: #dcfce7;
            color: #166534;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #a52a2a 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, #8b0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);
        }

        .image-container {
            position: relative;
            width: 100%;
            height: 200px;
            background-color: #f3f4f6;
            overflow: hidden;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            cursor: pointer;
        }

        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            cursor: pointer;
        }

        .image-modal img {
            max-width: 90%;
            max-height: 90vh;
            margin: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: contain;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 30px;
            cursor: pointer;
            z-index: 1001;
        }

        .compact-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .compact-card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .compact-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.125rem;
        }

        .info-value {
            font-size: 0.875rem;
            color: #111827;
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
    </style>
</head>

<body class="min-h-screen bg-white">
    <?php include 'navigation-admin.php'; ?>
    <div class="pt-5 main-content">
        <main class="min-h-screen">
            <!-- Welcome Section -->
            <div class="mb-4 flex justify-between items-center px-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Found Items Management</h1>
                    <p class="text-gray-600">View and manage all found items in the system</p>
                </div>
            </div>

            <div class="p-8">
                <?php if (empty($foundItems)): ?>
                    <div class="minimal-card p-8 text-center">
                        <div class="bg-gray-50 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-box-open text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No Found Items</h3>
                        <p class="text-gray-500">There are currently no found items in the system.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($foundItems as $item): ?>
                            <div class="minimal-card overflow-hidden card-hover compact-card">
                                <?php if (!empty($item['photo']) && !empty($item['mime_type'])): ?>
                                    <div class="image-container">
                                        <img src="data:<?php echo $item['mime_type']; ?>;base64,<?php echo base64_encode($item['photo']); ?>"
                                            alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                            onclick="openImageModal(this.src)" />
                                        <div class="absolute top-2 right-2">
                                            <span class="status-badge status-found">
                                                <i class="fas fa-check-circle mr-1"></i> Found
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="p-6 compact-card-content">
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-tag mr-1"></i>
                                            <?php echo htmlspecialchars($item['category']); ?>
                                        </p>
                                    </div>

                                    <div class="compact-info mb-4">
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-align-left mr-1"></i> Description
                                            </span>
                                            <span class="info-value">
                                                <?php echo htmlspecialchars($item['description'] ?? 'No description provided'); ?>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-map-marker-alt mr-1"></i> Location
                                            </span>
                                            <span class="info-value">
                                                <?php echo htmlspecialchars($item['location'] ?? 'Not specified'); ?>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-calendar-alt mr-1"></i> Date
                                            </span>
                                            <span class="info-value">
                                                <?php echo date('F j, Y', strtotime($item['date'])); ?>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-clock mr-1"></i> Time
                                            </span>
                                            <span class="info-value">
                                                <?php echo date('g:i A', strtotime($item['time'])); ?>
                                            </span>
                                        </div>

                                        <?php if (!empty($item['student_id'])): ?>
                                            <div class="info-item col-span-2">
                                                <span class="info-label">
                                                    <i class="fas fa-user mr-1"></i> Claimed By
                                                </span>
                                                <span class="info-value">
                                                    <?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?>
                                                    <span class="text-gray-500 ml-1">
                                                        (<?php echo htmlspecialchars($item['student_number']); ?>)
                                                    </span>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (empty($item['student_id'])): ?>
                                        <div class="mt-auto">
                                            <button onclick="notifyStudent(<?php echo $item['id']; ?>)"
                                                class="w-full btn-primary text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center">
                                                <i class="fas fa-bell mr-2"></i>
                                                Notify Student
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Full Screen Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="close-modal">&times;</span>
        <img id="modalImage" src="" alt="Full size image">
    </div>

    <script>
        function notifyStudent(itemId) {
            if (confirm('Are you sure you want to notify the student about this found item?')) {
                fetch('notify_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'item_id=' + itemId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Student has been notified successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while notifying the student.');
                });
            }
        }

        // Image modal functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = imageSrc;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = "none";
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeImageModal();
            }
        });
    </script>
</body>

</html>