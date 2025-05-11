<?php
require_once '../logic/db_connection.php';
require_once '../logic/sql_querries.php';
session_start();
if (!isset($_SESSION['isLoggedIn'])){
    echo "<script>alert('You are not logged in!!'); window.location.href = 'index.php';</script>";
}
$id = "0";
if (isset($_POST['user'])) {
    $id = $_POST['user'];
    $stmt = $pdo->prepare(SQL_GET_LOST_ITEMS_BY_ID);
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Lost Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .form-container {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
        }

        .form-input {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .form-input:focus {
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);
        }

        .preview-image {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-container input[type="checkbox"] {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.25rem;
            border: 2px solid #800000;
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include 'navigation.php'; ?>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="form-container p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-[#800000] mb-8">Report a Lost Item</h1>

            <form action="../logic/submit_lost_item_logic.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="space-y-2">
                    <label for="item_name" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="item_name" id="item_name" required
                           placeholder="e.g., Wallet, Phone, ID, Umbrella"
                           value="<?= htmlspecialchars($item['item_name'] ?? '') ?>"
                           class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                </div>

                <div class="space-y-2">
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" required
                            class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                        <option value="">Select a category</option>
                        <?php
                        $categories = ["bag", "clothing", "electronic", "id_card", "stationery", "umbrella", "money", "others"];
                        $currentCategory = $item['category'] ?? '';

                        foreach ($categories as $cat) {
                            $selected = ($currentCategory === $cat) ? 'selected' : '';
                            echo "<option value=\"$cat\" $selected>" . ucfirst(str_replace('_', ' ', $cat)) . "</option>";
                        }

                        if (!in_array($currentCategory, $categories) && !empty($currentCategory)) {
                            echo "<option value=\"$currentCategory\" selected>" . ucfirst($currentCategory) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="space-y-2" id="other_category_group" style="display: none;">
                    <label for="other_category" class="block text-sm font-medium text-gray-700">Specify Other Category</label>
                    <input type="text" name="other_category" id="other_category"
                           value="<?= htmlspecialchars($item['other_category'] ?? '') ?>"
                           class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="date_lost" class="block text-sm font-medium text-gray-700">Date Lost</label>
                        <input type="date" name="date_lost" id="date_lost" required
                               value="<?= htmlspecialchars($item['date_lost'] ?? '') ?>"
                               class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label for="time_lost" class="block text-sm font-medium text-gray-700">Time Lost (optional)</label>
                        <input type="time" name="time_lost" id="time_lost"
                               value="<?= htmlspecialchars($item['time_lost'] ?? '') ?>"
                               class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="location" class="block text-sm font-medium text-gray-700">Last Seen Location</label>
                    <select name="location" id="location" required
                            class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                        <option value="">Select a location</option>
                        <?php
                        $locations = ["classroom", "library", "hallway", "canteen", "gymnasium", "comfort_room", "others"];
                        $currentLocation = $item['location'] ?? '';

                        foreach ($locations as $loc) {
                            $selected = ($currentLocation === $loc) ? 'selected' : '';
                            echo "<option value=\"$loc\" $selected>" . ucfirst(str_replace('_', ' ', $loc)) . "</option>";
                        }

                        if (!in_array($currentLocation, $locations) && !empty($currentLocation)) {
                            echo "<option value=\"$currentLocation\" selected>" . ucfirst($currentLocation) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="space-y-2" id="classroom_details" style="display: none;">
                    <label for="building_room" class="block text-sm font-medium text-gray-700">Building/Room Number</label>
                    <input type="text" name="building_room" id="building_room"
                           value="<?= htmlspecialchars($item['building_room'] ?? '') ?>"
                           class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                </div>

                <div class="space-y-2" id="other_location_group" style="display: none;">
                    <label for="other_location" class="block text-sm font-medium text-gray-700">Specify Other Location</label>
                    <input type="text" name="other_location" id="other_location"
                           value="<?= htmlspecialchars($item['other_location'] ?? '') ?>"
                           class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description of Item</label>
                    <textarea name="description" id="description" required
                              class="form-input w-full px-4 py-2 rounded-lg focus:outline-none"
                              rows="4"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                </div>

                <div class="space-y-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700">Upload Photo (if available)</label>
                    
                    <?php if (!empty($item['photo'])): ?>
                        <div class="mb-4">
                            <img src="data:<?php echo $item['mime_type']; ?>;base64,<?php echo base64_encode($item['photo']); ?>"
                                alt="Uploaded Photo"
                                class="preview-image max-w-xs" />
                        </div>
                        <input type="hidden" name="existing_photo"
                            value="<?php echo base64_encode($item['photo']); ?>" />
                        <input type="hidden" name="existing_photo_mime_type"
                            value="<?php echo htmlspecialchars($item['mime_type']); ?>" />
                    <?php endif; ?>

                    <div class="flex items-center space-x-4">
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png"
                               class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Accepted formats: JPG, PNG only</p>
                </div>

                <div class="space-y-4">
                    <div class="checkbox-container">
                        <input type="checkbox" name="receive_sms" id="receive_sms"
                               <?= isset($item['receive_sms']) && $item['receive_sms'] ? 'checked' : '' ?>>
                        <label for="receive_sms" class="text-sm font-medium text-gray-700">
                            I want to receive SMS updates
                        </label>
                    </div>

                    <div class="space-y-2" id="phone_number_group" style="display: none;">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number (optional)</label>
                        <input type="text" name="phone_number" id="phone_number"
                               value="<?= htmlspecialchars($item['phone_number'] ?? '') ?>"
                               class="form-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    </div>
                </div>

                <input type="hidden" name="isUpdate" value="<?= $id ?>">

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold">
                        Submit Lost Item Report
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Show/hide other category field
        document.getElementById('category').addEventListener('change', function() {
            document.getElementById('other_category_group').style.display =
                this.value === 'others' ? 'block' : 'none';
        });

        // Show/hide classroom details and other location fields
        document.getElementById('location').addEventListener('change', function() {
            const location = this.value;
            document.getElementById('classroom_details').style.display =
                location === 'classroom' ? 'block' : 'none';
            document.getElementById('other_location_group').style.display =
                location === 'others' ? 'block' : 'none';
        });

        // Show/hide phone number field
        document.getElementById('receive_sms').addEventListener('change', function() {
            document.getElementById('phone_number_group').style.display =
                this.checked ? 'block' : 'none';
        });
    </script>
</body>

</html>