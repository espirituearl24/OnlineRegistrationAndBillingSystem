<?php
// Start the session (if not already started)
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to a different page (optional)
header("Location: index.php"); // Replace with your desired redirect
exit();
?>
