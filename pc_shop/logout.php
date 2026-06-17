<?php
// =============================================
// Logout - Destroy Session and Redirect
// =============================================

session_start();
session_destroy();
header("Location: signin.php");
exit();
?>