<?php
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])){
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}
$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare(SQL_LIST_LOST_ITEMS_BY_STUDENT);
$stmt->execute([$student_id]);
$lost_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$foundItems = [];
try {
    // Get all found items
    $stmt = $pdo->prepare("
        SELECT * from lost_items where student_id = ? AND status = 'found'
    ");
    $stmt->execute([$student_id]);
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
    <title>Lost Items</title>
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

        .status-found {
            background-color: #dcfce7;
            color: #166534;
        }

        .item-card {
            transition: transform 0.2s ease-in-out;
        }
        .item-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include 'navigation.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-[#800000]">Your Lost Items</h1>
            <a href="lost-item-form.php" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold">
                Report a Lost Item
            </a>
        </div>

        <!-- New Minimalist Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-3 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search -->
                <div class="flex-1 min-w-[180px]">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search items..." 
                               class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:border-[#800000]">
                        <svg class="absolute left-2.5 top-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button id="clearSearch" class="absolute right-2 top-2 hidden text-gray-400 hover:text-[#800000]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <input type="date" id="startDate" class="px-2 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:border-[#800000]">
                        <button id="clearStartDate" class="absolute right-1 top-2 hidden text-gray-400 hover:text-[#800000]">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <span class="text-xs text-gray-400">to</span>
                    <div class="relative">
                        <input type="date" id="endDate" class="px-2 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:border-[#800000]">
                        <button id="clearEndDate" class="absolute right-1 top-2 hidden text-gray-400 hover:text-[#800000]">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Sort Buttons -->
                <div class="flex items-center gap-1">
                    <button id="sortName" class="p-1.5 text-gray-400 hover:text-[#800000]" title="Sort by name">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    </button>
                    <button id="sortDate" class="p-1.5 text-gray-400 hover:text-[#800000]" title="Sort by date">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button id="sortStatus" class="p-1.5 text-gray-400 hover:text-[#800000]" title="Sort by status">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                    </button>
                </div>

                <!-- Clear All Filters Button -->
                <button id="clearAllFilters" class="flex items-center gap-1 px-2 py-1.5 text-xs text-gray-400 hover:text-[#800000]">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Clear all</span>
                </button>
            </div>
        </div>

        <div class="table-container relative z-10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="lostItemsTableBody">
                        <?php if (!empty($lost_items)): ?>
                            <?php foreach ($lost_items as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $item['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $item['item_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $item['category']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-<?php echo strtolower($item['status']); ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $item['date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($item['photo']) && !empty($item['mime_type'])): ?>
                                            <img src="data:<?php echo $item['mime_type']; ?>;base64,<?php echo base64_encode($item['photo']); ?>" 
                                                 alt="Item Photo" 
                                                 class="w-16 h-16 object-cover rounded-lg shadow-sm" />
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="lost-item-form.php" method="POST" class="inline">
                                            <input type="hidden" name="user" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn-secondary text-white px-4 py-2 rounded-lg text-sm">
                                                Edit
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="noResultsRow">
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No lost items found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-8 w-1 bg-[#800000] rounded-full"></div>
                <h2 class="text-xl font-semibold text-gray-800">Found Items</h2>
            </div>
            <p class="text-sm text-gray-500 mb-6">Items that have been recovered and are ready for collection</p>
        </div>

        <?php if (empty($foundItems)): ?>
            <div class="bg-white rounded-lg p-8 text-center border border-gray-100">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-50 flex items-center justify-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No Found Items</h3>
                <p class="text-xs text-gray-500">Your recovered items will appear here</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($foundItems as $item): ?>
                    <div class="bg-white rounded-lg border border-gray-100 overflow-hidden group hover:shadow-sm transition-shadow">
                        <div class="relative aspect-[4/3] bg-gray-50 cursor-pointer" onclick="openImageView(this)">
                            <?php if (!empty($item['photo']) && !empty($item['mime_type'])): ?>
                                <img src="data:<?php echo $item['mime_type']; ?>;base64,<?php echo base64_encode($item['photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <?php else: ?>
                                <img src="../image/default-image.png" 
                                     alt="Default image" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <?php endif; ?>
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo strtolower($item['status']) === 'found' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo ucfirst($item['status']); ?>
                                </span>
                            </div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m4-3H6" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        <?php echo htmlspecialchars($item['category']); ?>
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400">
                                    <?php echo date('M d, Y', strtotime($item['date'])); ?>
                                </span>
                            </div>

                            <?php if (!empty($item['description'])): ?>
                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">
                                    <?php echo htmlspecialchars($item['location'] ?? 'Location not specified'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Image View Modal -->
        <div id="imageViewModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center">
            <button onclick="closeImageView()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="max-w-7xl max-h-[90vh] p-4">
                <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-[85vh] object-contain" />
            </div>
        </div>
    </main>

    <script>
        // New filtering and sorting logic
        let currentSort = {
            column: null,
            direction: 'asc'
        };

        function updateClearButtons() {
            const searchInput = document.getElementById('searchInput');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const clearSearch = document.getElementById('clearSearch');
            const clearStartDate = document.getElementById('clearStartDate');
            const clearEndDate = document.getElementById('clearEndDate');

            // Show/hide clear buttons based on input values
            clearSearch.style.display = searchInput.value ? 'block' : 'none';
            clearStartDate.style.display = startDate.value ? 'block' : 'none';
            clearEndDate.style.display = endDate.value ? 'block' : 'none';
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            currentSort.column = null;
            currentSort.direction = 'asc';
            updateClearButtons();
            filterAndSortTable();
        }

        function filterAndSortTable() {
            const table = document.querySelector('table tbody');
            const rows = Array.from(table.getElementsByTagName('tr'));
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            // Filter rows
            rows.forEach(row => {
                if (row.children.length < 7) return; // skip 'no items' row
                
                const itemName = row.cells[1].textContent.toLowerCase();
                const date = row.cells[4].textContent;
                
                let show = true;
                
                // Search filter
                if (searchTerm && !itemName.includes(searchTerm)) {
                    show = false;
                }
                
                // Date range filter
                if (startDate && date < startDate) show = false;
                if (endDate && date > endDate) show = false;
                
                row.style.display = show ? '' : 'none';
            });

            // Sort rows if needed
            if (currentSort.column !== null) {
                const tbody = document.getElementById('lostItemsTableBody');
                const sortedRows = Array.from(tbody.getElementsByTagName('tr'))
                    .filter(row => row.children.length >= 7) // Filter out the "no results" row
                    .sort((a, b) => {
                        let aVal = a.cells[currentSort.column].textContent;
                        let bVal = b.cells[currentSort.column].textContent;
                        
                        if (currentSort.column === 4) { // Date column
                            aVal = new Date(aVal);
                            bVal = new Date(bVal);
                        }
                        
                        if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                        if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                        return 0;
                    });
                
                sortedRows.forEach(row => tbody.appendChild(row));
            }

            // Show/hide 'No results found' row
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            const noResultsRow = document.getElementById('noResultsRow');
            if (visibleRows.length === 0) {
                if (!noResultsRow) {
                    const tr = document.createElement('tr');
                    tr.id = 'noResultsRow';
                    tr.innerHTML = `<td colspan='7' class='px-6 py-4 text-center text-sm text-gray-500'>No results found</td>`;
                    table.appendChild(tr);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }

            // Update sort button states
            updateSortButtonStates();
        }

        function updateSortButtonStates() {
            const sortButtons = ['sortName', 'sortDate', 'sortStatus'];
            const columns = [1, 4, 3]; // Corresponding column indices

            sortButtons.forEach((buttonId, index) => {
                const button = document.getElementById(buttonId);
                const isActive = currentSort.column === columns[index];
                
                if (isActive) {
                    button.classList.add('text-[#800000]');
                    button.classList.remove('text-gray-400');
                } else {
                    button.classList.remove('text-[#800000]');
                    button.classList.add('text-gray-400');
                }
            });
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Search input
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', () => {
                updateClearButtons();
                filterAndSortTable();
            });
            
            // Date inputs
            document.getElementById('startDate').addEventListener('change', () => {
                updateClearButtons();
                filterAndSortTable();
            });
            document.getElementById('endDate').addEventListener('change', () => {
                updateClearButtons();
                filterAndSortTable();
            });
            
            // Clear buttons
            document.getElementById('clearSearch').addEventListener('click', () => {
                searchInput.value = '';
                updateClearButtons();
                filterAndSortTable();
            });
            
            document.getElementById('clearStartDate').addEventListener('click', () => {
                document.getElementById('startDate').value = '';
                updateClearButtons();
                filterAndSortTable();
            });
            
            document.getElementById('clearEndDate').addEventListener('click', () => {
                document.getElementById('endDate').value = '';
                updateClearButtons();
                filterAndSortTable();
            });
            
            // Clear all filters
            document.getElementById('clearAllFilters').addEventListener('click', clearAllFilters);
            
            // Sort buttons
            document.getElementById('sortName').addEventListener('click', () => {
                currentSort.column = currentSort.column === 1 ? null : 1;
                currentSort.direction = currentSort.column === 1 ? 
                    (currentSort.direction === 'asc' ? 'desc' : 'asc') : 'asc';
                filterAndSortTable();
            });
            
            document.getElementById('sortDate').addEventListener('click', () => {
                currentSort.column = currentSort.column === 4 ? null : 4;
                currentSort.direction = currentSort.column === 4 ? 
                    (currentSort.direction === 'asc' ? 'desc' : 'asc') : 'asc';
                filterAndSortTable();
            });
            
            document.getElementById('sortStatus').addEventListener('click', () => {
                currentSort.column = currentSort.column === 3 ? null : 3;
                currentSort.direction = currentSort.column === 3 ? 
                    (currentSort.direction === 'asc' ? 'desc' : 'asc') : 'asc';
                filterAndSortTable();
            });

            // Initial update of clear buttons
            updateClearButtons();
        });

        // Image View Functions
        function openImageView(element) {
            const modal = document.getElementById('imageViewModal');
            const modalImage = document.getElementById('modalImage');
            const image = element.querySelector('img');
            
            modalImage.src = image.src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeImageView() {
            const modal = document.getElementById('imageViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageView();
            }
        });

        // Close modal on click outside image
        document.getElementById('imageViewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageView();
            }
        });
    </script>
</body>

</html>