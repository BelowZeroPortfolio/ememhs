<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if staff is logged in
if (!$_SESSION['isLoggedIn']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get data from either JSON or form data
$data = [];
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$complaint_id = $data['complaint_id'] ?? '';
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';

// Log received data
error_log("Received data: " . print_r($data, true));

if (empty($complaint_id) || empty($date) || empty($time)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required parameters', 'data' => $data]);
    exit();
}

try {
    // First, check if the complaint exists
    $checkStmt = $pdo->prepare("SELECT id FROM complaints_concerns WHERE id = :complaint_id");
    $checkStmt->execute(['complaint_id' => $complaint_id]);
    $exists = $checkStmt->fetch();

    if (!$exists) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Complaint not found']);
        exit();
    }

    // Convert military time to standard time format
    $standardTime = date('g:i A', strtotime($time));
    error_log("Standard time format: " . $standardTime);

    // Update the complaint with the scheduled date and time
    $sql = "
        UPDATE complaints_concerns 
        SET scheduled_date = :date,
            scheduled_time = :time,
            status = 'scheduled',
            updated_at = NOW()
        WHERE id = :complaint_id
    ";
    
    error_log("Executing SQL: " . $sql);
    error_log("With parameters: " . print_r([
        'date' => $date,
        'time' => $standardTime,
        'complaint_id' => $complaint_id
    ], true));

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'date' => $date,
        'time' => $standardTime,
        'complaint_id' => $complaint_id
    ]);
    
    error_log("Update result: " . ($result ? "success" : "failed"));
    error_log("Rows affected: " . $stmt->rowCount());

    if ($stmt->rowCount() > 0) {
        // Verify the update
        $verifyStmt = $pdo->prepare("SELECT scheduled_date, scheduled_time, status FROM complaints_concerns WHERE id = :complaint_id");
        $verifyStmt->execute(['complaint_id' => $complaint_id]);
        $updatedData = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Updated data: " . print_r($updatedData, true));
        
        // Get student_id and user_id from the complaint
        $stmt = $pdo->prepare("
            SELECT cc.student_id, s.user_id 
            FROM complaints_concerns cc
            JOIN students s ON cc.student_id = s.id
            WHERE cc.id = ?
        ");
        $stmt->execute([$complaint_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            error_log("Student data found: " . print_r($result, true));
            // Create notification for the student using user_id
            createScheduledNotification($result['user_id'], $complaint_id, $date, $time);
        } else {
            error_log("No student data found for complaint ID: " . $complaint_id);
        }

        // Send success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'updated_data' => $updatedData
        ]);
    } else {
        // Send error response if no records were updated
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'No records updated',
            'sql' => $sql,
            'params' => [
                'date' => $date,
                'time' => $standardTime,
                'complaint_id' => $complaint_id
            ]
        ]);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>
