<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';

// Check if staff is logged in
if (!$_SESSION['isLoggedIn']) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'] ?? null;

    if ($complaint_id) {
        try {
            // Update complaint status to resolved
            $stmt = $pdo->prepare("
                UPDATE " . TBL_COMPLAINTS_CONCERNS . " 
                SET status = 'resolved' 
                WHERE id = ?
            ");
            
            $stmt->execute([$complaint_id]);

            // Get student_id from the complaint
            $stmt = $pdo->prepare("SELECT student_id FROM " . TBL_COMPLAINTS_CONCERNS . " WHERE id = ?");
            $stmt->execute([$complaint_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Create notification for the student
                createResolvedNotification($result['student_id'], $complaint_id);
            }

            header("Location: complaint-concern-admin.php?success=1");
            exit();
        } catch (PDOException $e) {
            error_log("Error marking complaint as resolved: " . $e->getMessage());
            header("Location: complaint-concern-admin.php?error=1");
            exit();
        }
    }
}

header("Location: complaint-concern-admin.php");
exit();
?> 