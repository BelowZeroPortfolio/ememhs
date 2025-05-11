<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    
    try {
        // Get the lost item details
        $stmt = $pdo->prepare("SELECT * FROM lost_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            throw new Exception("Lost item not found");
        }
        
        // Validate student ID
        if (!$item['student_id']) {
            throw new Exception("Student ID is missing");
        }
        
        error_log("Attempting to create notification for:");
        error_log("Student ID: " . $item['student_id']);
        error_log("Item ID: " . $item_id);
        error_log("Item Name: " . $item['item_name']);
        
        // Create notification
        $success = createFoundItemNotification($item['student_id'], $item_id, $item['item_name']);
        
        if ($success) {
            // Update item status to 'found'
            $update_stmt = $pdo->prepare("UPDATE lost_items SET status = 'found' WHERE id = ?");
            $update_stmt->execute([$item_id]);
            
            echo json_encode(['success' => true, 'message' => 'Student has been notified successfully']);
        } else {
            error_log("Failed to create notification. Check if student_id exists in users table.");
            throw new Exception("Failed to create notification - Please check if student exists in the system");
        }
        
    } catch (Exception $e) {
        error_log("Error notifying student: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Error notifying student: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 