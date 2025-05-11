<?php
require_once 'sql_querries.php';
require_once 'db_connection.php';
session_start();

try {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $pdo->beginTransaction();

    // First get the user with their hashed password
    $stmt = $pdo->prepare("SELECT u.id as user_id, u.email, u.password, u.role, s.id as student_id, s.first_name, s.last_name 
                          FROM " . TBL_USERS . " u
                          LEFT JOIN " . TBL_STUDENTS . " s ON u.id = s.user_id
                          WHERE u.email = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['student_id'] = $user['student_id'];
        $pdo->commit();

        if ($user['role'] === "admin") {
            header("Location: ../pages/staff-dashboard.php");
            exit;
        } elseif ($user['role'] === "student") {
            header("Location: ../pages/student_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Unknown user role.'); window.location.href = '../pages/index.php';</script>";
        }

    } else {
        echo "<script>alert('Invalid username or password'); window.location.href = '../pages/login.php';</script>";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
