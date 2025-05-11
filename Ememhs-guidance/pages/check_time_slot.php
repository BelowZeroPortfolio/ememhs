<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';

// Check if staff is logged in
if (!$_SESSION['isLoggedIn']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';

if (empty($date) || empty($time)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

try {
    // Convert military time to standard time format for comparison
    $standardTime = date('g:i A', strtotime($time));
    
    // Check if the time slot is already taken
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM complaints_concerns 
        WHERE scheduled_date = :date 
        AND scheduled_time = :time 
        AND status IN ('scheduled', 'resolved')
    ");
    
    $stmt->execute([
        'date' => $date,
        'time' => $standardTime
    ]);
    
    $count = $stmt->fetchColumn();
    
    header('Content-Type: application/json');
    echo json_encode(['available' => $count === 0]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
}
?> 