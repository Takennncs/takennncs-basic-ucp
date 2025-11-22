<?php
$steamauth['apikey'] = "";
$steamauth['domainname'] = "";
$steamauth['loginpage'] = "dashboard.php";
$steamauth['logoutpage'] = "index.php";

$host = "";
$dbname = ""; 
$username = "";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database viga: " . $e->getMessage());
}
?>
