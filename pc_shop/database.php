<?php
// =============================================
// Database Connection File
// =============================================

$server = "localhost";
$user = "root";
$password = "";
$database = "pc_shop_db";

// Create connection
$conn = mysqli_connect($server, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");
?>