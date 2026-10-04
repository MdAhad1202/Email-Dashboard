<?php
// Session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = 'Ahad1234@';       // ⚠️ এখানে তোমার actual password বসাও
$db   = 'student_dashboard';           // ⚠️ এখানে তোমার database name বসাও

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Auto-login check (login.php ছাড়া বাকি সব page এ login লাগবে)
$currentFile = basename($_SERVER['PHP_SELF']);
$publicFiles = ['login.php'];
if (!in_array($currentFile, $publicFiles) && !isset($_SESSION['admin_id'])) {
    header("Location: auth/login.php");
    exit;
}
?>