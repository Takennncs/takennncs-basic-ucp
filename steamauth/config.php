<?php
$steamauth['apikey'] = "058CE9F7A684D4D4B1780A16B68D78FF";
$steamauth['domainname'] = "http://localhost";
$steamauth['loginpage'] = "dashboard.php";
$steamauth['logoutpage'] = "index.php";

$host = "localhost";
$dbname = "qbcore_21c90a"; 
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database viga: " . $e->getMessage());
}
?>