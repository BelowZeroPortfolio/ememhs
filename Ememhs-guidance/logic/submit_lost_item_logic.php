<?php
require_once 'sql_querries.php';
require_once 'db_connection.php';
session_start();

$date = date('Y-m-d');
$time = date('H:i:s');

try {
    $transacType = "insert";
    $queryUse = SQL_INSERT_LOST_ITEMS;
    if ($_POST['isUpdate'] != "0") {
        $queryUse = SQL_UPDATE_LOST_ITEMS;
        $transacType = "update";
        // echo "<script>alert('aw ah ahahhaha')</script>";
    }

    // Get form data
    $rowId = $_POST['isUpdate'];
    $itemName = $_POST['item_name'];
    $category = $_POST['category'];
    $otherCategory = isset($_POST['other_category']) ? $_POST['other_category'] : null;
    $dateLost = $_POST['date_lost'];
    $timeLost = isset($_POST['time_lost']) ? $_POST['time_lost'] : null;
    $location = $_POST['location'];
    $buildingRoom = isset($_POST['building_room']) ? $_POST['building_room'] : null;
    $otherLocation = isset($_POST['other_location']) ? $_POST['other_location'] : null;
    $description = $_POST['description'];
    $receiveSMS = isset($_POST['receive_sms']) ? 1 : 0;
    $phoneNumber = isset($_POST['phone_number']) ? $_POST['phone_number'] : null;

    // Handle category
    $finalCategory = $category;
    if ($category === 'others' && $otherCategory) {
        $finalCategory = $otherCategory;
    }

    // Handle location
    $finalLocation = $location;
    if ($location === 'classroom' && $buildingRoom) {
        $finalLocation = "Classroom - " . $buildingRoom;
    } elseif ($location === 'others' && $otherLocation) {
        $finalLocation = $otherLocation;
    }

    // Handle photo upload
    $photo = null;
    $photoType = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmpPath = $_FILES['photo']['tmp_name'];

        if (!empty($photoTmpPath) && file_exists($photoTmpPath)) {
            $photo = file_get_contents($photoTmpPath);
            $photoType = mime_content_type($photoTmpPath);
        }
    } elseif (!empty($_POST['existing_photo']) && !empty($_POST['existing_photo_mime_type'])) {
        // Use previously uploaded evidence
        $photo = base64_decode($_POST['existing_photo']);
        $photoType = $_POST['existing_photo_mime_type'];
    } else {
        // No evidence provided
        $evidence = null;
        $mime_type = null;
    }

    $status = 'pending';
    $student_id = $_SESSION['student_id'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare($queryUse);

    if ($transacType == "update") {
        $stmt->execute([
            $student_id,
            $itemName,
            $finalCategory,
            $dateLost,
            $timeLost,
            $finalLocation,
            $description,
            $photo,
            $photoType,
            $receiveSMS,
            $phoneNumber,
            $status,
            $date,
            $time,
            $rowId
        ]);
    } else if ($transacType == "insert") {
        $stmt->execute([
            $student_id,
            $itemName,
            $finalCategory,
            $dateLost,
            $timeLost,
            $finalLocation,
            $description,
            $photo,
            $photoType,
            $receiveSMS,
            $phoneNumber,
            $status,
            $date,
            $time
        ]);
    }

    $pdo->commit();

    echo "<script>
        alert('Your lost item report has been submitted successfully.');
        window.location.href = '../pages/student_dashboard.php';
    </script>";
    exit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    echo json_encode($response);
}
