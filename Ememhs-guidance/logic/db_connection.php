<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "guidancesystem";

// mysqli connection
$con = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);
if (!$con) {
    die("Connection failed: ". mysqli_connect_error());
}else{

}
// PDO connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>