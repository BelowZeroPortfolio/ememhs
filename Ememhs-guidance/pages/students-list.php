<?php
session_start();
require_once '../logic/db_connection.php';
require_once '../logic/sql_querries.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

try {
    // Get all students
    $stmt = $pdo->prepare("
        SELECT s.*, u.email
        FROM " . TBL_STUDENTS . " s
        LEFT JOIN " . TBL_USERS . " u ON s.user_id = u.id
        ORDER BY s.last_name ASC, s.first_name ASC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching students: " . $e->getMessage());
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .student-card {
            transition: transform 0.2s ease-in-out;
        }
        .student-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'navigation-admin.php'; ?>
    <div class="main-content">
        <main class="max-w-7xl mx-auto px-4 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#800000] mb-2">Students List</h1>
                <p class="text-gray-600">View and manage all registered students</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Search students..." 
                                   class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 transition duration-200">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <select id="gradeFilter" class="px-4 py-2 rounded-lg border text-xs border-gray-200 focus:border-[#800000] focus:ring-2 focus:ring-[#800000]/20 transition duration-200">
                            <option value="">All Grade Levels</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                        <button id="clearFilters" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition duration-200 flex items-center text-xs gap-2">
                            <i class="fas fa-times "></i>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <?php if (empty($students)): ?>
                <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="bg-gray-50 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                    <p class="text-gray-500">There are currently no students registered in the system.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Grade Level
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Section
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact Info
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($students as $student): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($student['id']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($student['grade_level']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($student['section']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>Email: <?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></div>
                                            <div>Phone: <?php echo htmlspecialchars($student['phone_number'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Add this before the closing </body> tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const gradeFilter = document.getElementById('gradeFilter');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const table = document.querySelector('table tbody');
            const rows = table.querySelectorAll('tr');

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedGrade = gradeFilter.value.toLowerCase();
                let hasVisibleRows = false;

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let rowVisible = true;

                    // Apply search filter
                    if (searchTerm) {
                        let searchMatch = false;
                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                                searchMatch = true;
                            }
                        });
                        if (!searchMatch) rowVisible = false;
                    }

                    // Apply grade filter
                    if (selectedGrade && rowVisible) {
                        const gradeCell = cells[2].textContent.toLowerCase();
                        if (!gradeCell.includes(selectedGrade)) {
                            rowVisible = false;
                        }
                    }

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
                        cell.colSpan = 6;
                        cell.className = 'px-6 py-4 text-center text-sm text-gray-500';
                        cell.textContent = 'No matching students found';
                        newRow.appendChild(cell);
                        table.appendChild(newRow);
                    } else {
                        noResultsRow.style.display = '';
                    }
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }

            function clearFilters() {
                searchInput.value = '';
                gradeFilter.value = '';
                applyFilters();
            }

            // Add event listeners
            searchInput.addEventListener('input', applyFilters);
            gradeFilter.addEventListener('change', applyFilters);
            clearFiltersBtn.addEventListener('click', clearFilters);

            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .search-input:focus, select:focus {
                    outline: none;
                }
                tr {
                    transition: all 0.2s ease-in-out;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                tr:not([style*="display: none"]) {
                    animation: fadeIn 0.2s ease-in-out;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html> 