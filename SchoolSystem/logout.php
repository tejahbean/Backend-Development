
<?php
// Tejah Bean - Original code with detailed documentation
// Start session to access session variables
session_start();

// Destroy all session data
session_destroy();

// Redirect to the login page after logout
header("Location: login.php");
exit;
?>
