<?php
session_start();
require_once '../logic/sql_querries.php';
require_once '../logic/db_connection.php';
require_once '../logic/notification_logic.php';

// Check if admin is logged in
if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Mark all notifications as read
$success = markAllNotificationsAsRead($_SESSION['user']);

header('Content-Type: application/json');
echo json_encode(['success' => $success]); 