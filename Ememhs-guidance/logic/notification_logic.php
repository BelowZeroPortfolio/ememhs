<?php
require_once 'db_connection.php';
require_once 'sql_querries.php';

function createNotification($student_id, $reference_id, $reference_type, $type, $message) {
    global $pdo;

    error_log("[DEBUG] createNotification() called with parameters:");
    error_log("Student ID: " . $student_id);
    error_log("Reference ID: " . $reference_id);
    error_log("Reference Type: " . $reference_type);
    error_log("Type: " . $type);
    error_log("Message: " . $message);

    try {
        // Validate input parameters
        if (!$student_id || !$reference_id || !$reference_type || !$type || !$message) {
            error_log("[ERROR] Missing required parameters in createNotification");
            return false;
        }

        $stmt = $pdo->prepare(SQL_INSERT_NOTIFICATION);
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');

        error_log("Prepared SQL: " . SQL_INSERT_NOTIFICATION);
        error_log("Parameters to execute:");
        error_log("1. Student ID: " . $student_id);
        error_log("2. Reference ID: " . $reference_id);
        error_log("3. Reference Type: " . $reference_type);
        error_log("4. Type: " . $type);
        error_log("5. Message: " . $message);
        error_log("6. Date: " . $current_date);
        error_log("7. Time: " . $current_time);

        $result = $stmt->execute([
            $student_id,
            $reference_id,
            $reference_type,
            $type,
            $message,
            $current_date,
            $current_time
        ]);

        if (!$result) {
            error_log("[ERROR] Failed to execute notification insert");
            error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
            return false;
        }

        error_log("Notification created successfully with ID: " . $pdo->lastInsertId());
        return true;
    } catch (PDOException $e) {
        error_log("[ERROR] Exception in createNotification: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

function getStudentNotifications($student_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(SQL_GET_STUDENT_NOTIFICATIONS);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}


function getUnreadNotificationsCount($student_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(SQL_GET_UNREAD_NOTIFICATIONS_COUNT);
        $stmt->execute([$student_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['unread_count'];
    } catch (PDOException $e) {
        error_log("Error getting unread count: " . $e->getMessage());
        return 0;
    }
}

function markNotificationAsRead($notification_id, $student_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(SQL_MARK_NOTIFICATION_AS_READ);
        return $stmt->execute([$notification_id, $student_id]);
    } catch (PDOException $e) {
        error_log("Error marking notification as read: " . $e->getMessage());
        return false;
    }
}

function markAllNotificationsAsRead($student_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(SQL_MARK_ALL_NOTIFICATIONS_AS_READ);
        return $stmt->execute([$student_id]);
    } catch (PDOException $e) {
        error_log("Error marking all notifications as read: " . $e->getMessage());
        return false;
    }
}

// Function to create notification when complaint is scheduled
function createScheduledNotification($student_id, $complaint_id, $scheduled_date, $scheduled_time) {
    error_log("Creating scheduled notification with parameters:");
    error_log("Student ID: " . $student_id);
    error_log("Complaint ID: " . $complaint_id);
    error_log("Scheduled Date: " . $scheduled_date);
    error_log("Scheduled Time: " . $scheduled_time);

    $message = "Your complaint has been scheduled for " . date('F j, Y', strtotime($scheduled_date)) . 
               " at " . date('g:i A', strtotime($scheduled_time));
    
    error_log("Generated message: " . $message);
    
    $result = createNotification($student_id, $complaint_id, 'complaint', 'scheduled', $message);
    
    error_log("Notification creation result: " . ($result ? "success" : "failed"));
    
    return $result;
}

// Function to create notification when complaint is resolved
function createResolvedNotification($student_id, $complaint_id) {
    $message = "Your complaint has been marked as resolved.";
    return createNotification($student_id, $complaint_id, 'complaint', 'resolved', $message);
}

// Function to create notification when lost item is found
function createFoundItemNotification($student_id, $item_id, $item_name) {
    global $pdo;
    
    error_log("[DEBUG] createFoundItemNotification() called with parameters:");
    error_log("Student ID: " . $student_id);
    error_log("Item ID: " . $item_id);
    error_log("Item Name: " . $item_name);

    if (!$student_id || !$item_id || !$item_name) {
        error_log("[ERROR] Missing required parameters");
        return false;
    }

    try {
        // Get the user_id from students table
        $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student || !$student['user_id']) {
            error_log("[ERROR] Student not found or has no user_id");
            return false;
        }

        $message = "A matching item has been found: " . $item_name;
        $result = createNotification($student['user_id'], $item_id, 'lost_item', 'found_item', $message);
        
        error_log("Notification creation result: " . ($result ? "success" : "failed"));
        
        return $result;
    } catch (PDOException $e) {
        error_log("[ERROR] Database error in createFoundItemNotification: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

function createAdminNotif($complaint_id, $student_name, $complaint_type) {
    global $pdo;

    echo "[DEBUG] createAdminNotif() called.<br>";

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            echo "[DEBUG] No admin found.<br>";
            return false;
        }

        $adminId = $admin['id'];
        $message = "New complaint from $student_name: $complaint_type";

        echo "[DEBUG] Admin ID: $adminId<br>";
        echo "[DEBUG] Message: $message<br>";

        $success = createNotification($adminId, $complaint_id, 'complaint', 'new_complaint', $message);

        if (!$success) {
            echo "[DEBUG] createNotification failed.<br>";
        }

        return $success;
    } catch (PDOException $e) {
        echo "[DEBUG] Exception in createAdminNotif: " . $e->getMessage() . "<br>";
        return false;
    }
}

function notifyAdminNewComplaint($complaint_id, $student_name, $complaint_type) {
    global $pdo;
    
    try {
        // Get all admin users
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create notification for each admin
        foreach ($admins as $admin) {
            $stmt = $pdo->prepare(SQL_INSERT_NOTIFICATION);
            $stmt->execute([
                $admin['id'],  // admin_id as the recipient
                $complaint_id,
                'complaint',
                'new_complaint',
                "New complaint from $student_name: $complaint_type",
                date('Y-m-d'),
                date('H:i:s')
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error creating admin notification: " . $e->getMessage());
        return false;
    }
}

function getAdminNotifications($admin_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT n.*, 
                CASE 
                    WHEN n.reference_type = 'complaint' THEN cc.type
                    WHEN n.reference_type = 'lost_item' THEN li.category
                END as reference_type_detail,
                CASE 
                    WHEN n.reference_type = 'complaint' THEN cc.status
                    WHEN n.reference_type = 'lost_item' THEN li.status
                END as reference_status,
                CASE 
                    WHEN n.reference_type = 'complaint' THEN cc.description
                    WHEN n.reference_type = 'lost_item' THEN li.description
                END as description,
                CASE 
                    WHEN n.reference_type = 'lost_item' THEN li.item_name
                    ELSE NULL
                END as item_name,
                CASE 
                    WHEN n.reference_type = 'lost_item' THEN li.location
                    ELSE NULL
                END as location_found,
                CASE 
                    WHEN n.reference_type = 'complaint' THEN cc.evidence
                    WHEN n.reference_type = 'lost_item' THEN li.photo
                    ELSE NULL
                END as photo,
                CASE 
                    WHEN n.reference_type = 'complaint' THEN cc.mime_type
                    WHEN n.reference_type = 'lost_item' THEN li.mime_type
                    ELSE NULL
                END as mime_type,
                s.first_name,
                s.last_name,
                s.grade_level,
                s.section
            FROM " . TBL_NOTIFICATIONS . " n
            LEFT JOIN " . TBL_COMPLAINTS_CONCERNS . " cc ON n.reference_id = cc.id AND n.reference_type = 'complaint'
            LEFT JOIN " . TBL_LOST_ITEMS . " li ON n.reference_id = li.id AND n.reference_type = 'lost_item'
            LEFT JOIN " . TBL_STUDENTS . " s ON cc.student_id = s.id OR li.student_id = s.id
            WHERE n.user_id = ?
            ORDER BY n.date_created DESC, n.time_created DESC
        ");
        $stmt->execute([$admin_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting admin notifications: " . $e->getMessage());
        return [];
    }
}

function getAdminUnreadNotificationsCount($admin_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as unread_count
            FROM " . TBL_NOTIFICATIONS . "
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$admin_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['unread_count'];
    } catch (PDOException $e) {
        error_log("Error getting admin unread count: " . $e->getMessage());
        return 0;
    }
}

function markAllAdminNotificationsAsRead($admin_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE " . TBL_NOTIFICATIONS . "
            SET is_read = 1
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$admin_id]);
    } catch (PDOException $e) {
        error_log("Error marking all admin notifications as read: " . $e->getMessage());
        return false;
    }
}