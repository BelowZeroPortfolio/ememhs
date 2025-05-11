<?php
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])){
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare(SQL_GET_STUDENT);
$stmt->execute([$student_id]);
$studentDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    try {
        // Debug: Log the received data
        error_log("Received POST data: " . print_r($_POST, true));
        error_log("Current student_id: " . $student_id);

        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'grade_level', 'section'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            throw new Exception("Required fields are missing: " . implode(", ", $missing_fields));
        }

        // First, verify the user exists
        $check_stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $check_stmt->execute([$student_id]);
        $user_exists = $check_stmt->fetch();

        if (!$user_exists) {
            throw new Exception("User not found with ID: " . $student_id);
        }

        // Prepare the update query with correct field names
        $update_query = "UPDATE students SET 
            first_name = :first_name,
            middle_name = :middle_name,
            last_name = :last_name,
            grade_level = :grade_level,
            section = :section
            WHERE id = :student_id";

        $stmt = $pdo->prepare($update_query);
        
        // Bind parameters
        $params = [
            ':first_name' => trim($_POST['first_name']),
            ':middle_name' => trim($_POST['middle_name']),
            ':last_name' => trim($_POST['last_name']),
            ':grade_level' => trim($_POST['grade_level']),
            ':section' => trim($_POST['section']),
            ':student_id' => $student_id
        ];

        // Debug: Log the parameters
        error_log("Update parameters: " . print_r($params, true));

        // Execute the update
        $result = $stmt->execute($params);

        // Debug: Log the result
        error_log("Update result: " . ($result ? "true" : "false"));
        if (!$result) {
            error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
        }

        if ($result) {
            // Verify the update
            $verify_stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $verify_stmt->execute([$student_id]);
            $updated_data = $verify_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Log the updated data
            error_log("Updated data: " . print_r($updated_data, true));

            // Refresh the page to show updated data
            header("Location: profile.php?success=1");
            exit;
        } else {
            throw new Exception("Update failed: " . implode(", ", $stmt->errorInfo()));
        }
    } catch (Exception $e) {
        $error = "Failed to update profile: " . $e->getMessage();
        // Log the error for debugging
        error_log("Profile update error: " . $e->getMessage());
    }
}

// Debug: Log the current session data
error_log("Session data: " . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - EMEMHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .form-input {
            @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] outline-none transition-colors;
        }

        .form-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }
    </style>
</head>
<body class="bg-gray-50 font-[Inter]">
    <?php include 'navigation.php'; ?>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Student Profile</h1>
            <p class="text-gray-600 mt-2">View and manage your personal information</p>
        </div>

        <?php if (!empty($studentDetails)) {
            $student = $studentDetails[0];
        ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Profile Header -->
                <div class="bg-[#800000] px-8 py-6 text-white">
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold"><?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) ?></h2>
                            <p class="text-gray-200 mt-1"><?= htmlspecialchars($student['email']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Personal Information
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <span class="w-32 text-gray-600">First Name:</span>
                                    <span class="font-medium"><?= htmlspecialchars($student['first_name']) ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-32 text-gray-600">Middle Name:</span>
                                    <span class="font-medium"><?= htmlspecialchars($student['middle_name']) ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-32 text-gray-600">Last Name:</span>
                                    <span class="font-medium"><?= htmlspecialchars($student['last_name']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#800000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                </svg>
                                Academic Information
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <span class="w-32 text-gray-600">Grade Level:</span>
                                    <span class="font-medium"><?= htmlspecialchars($student['grade_level']) ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-32 text-gray-600">Section:</span>
                                    <span class="font-medium"><?= htmlspecialchars($student['section']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                        <button onclick="openEditModal()" class="px-4 py-2 text-[#800000] border border-[#800000] rounded-lg hover:bg-[#800000] hover:text-white transition-colors duration-300">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800">Edit Profile</h3>
                            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form action="profile.php" method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="form-label text-[12px]">First Name</label><br>
                                    <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" class="form-input" required>
                                </div>
                                
                                <div>
                                    <label class="form-label text-[12px]">Middle Name</label><br>
                                    <input type="text" name="middle_name" value="<?= htmlspecialchars($student['middle_name']) ?>" class="form-input">
                                </div>
                                
                                <div>
                                    <label class="form-label text-[12px]">Last Name</label><br>
                                    <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" class="form-input" required>
                                </div>
                                
                                <div>
                                    <label class="form-label text-[12px]">Grade Level</label><br>
                                    <input type="text" name="grade_level" value="<?= htmlspecialchars($student['grade_level']) ?>" class="form-input" required>
                                </div>
                                
                                <div>
                                    <label class="form-label text-[12px]">Section</label><br>
                                    <input type="text" name="section" value="<?= htmlspecialchars($student['section']) ?>" class="form-input" required>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-4 border-t">
                                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-[#800000] text-white rounded-lg hover:bg-[#600000] transition-colors duration-300">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Profile Found</h3>
                <p class="mt-2 text-gray-600">We couldn't find your student profile information.</p>
            </div>
        <?php } ?>
    </main>

    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('editModal').classList.contains('active')) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>