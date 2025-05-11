<?php
require_once '../sql_querries.php';
require_once '../db_connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $stmt = $pdo->prepare(SQL_CHECK_EMAIL_EXISTS);
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo "0";
        }else{
            echo "1";
        }
        
    } else {
        echo "No email received.";
    }
} else {
    echo "Invalid request.";
}
?>
