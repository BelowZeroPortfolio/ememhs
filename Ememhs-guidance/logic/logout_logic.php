<?php
require_once 'db_connection.php';
session_start(); // Start session before destroying it
session_destroy(); // Destroy all session data
header('Location: ../pages/login.php'); // Redirect to login/home page
exit();
?>
