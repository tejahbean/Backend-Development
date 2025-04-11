
<?php
// Tejah Bean - Original code with detailed documentation
// Start session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Retrieve session variables for the logged-in user
$username = $_SESSION['username'];
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-container {
            width: 100%;
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .dashboard-container h2 {
            text-align: center;
            color: #003366;
        }
        .btn-logout {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Your role: <?php echo htmlspecialchars($role); ?></p>
    
    <!-- You can add more content or features specific to the user role here -->
    
    <!-- Logout Button -->
    <a href="logout.php" class="btn btn-danger btn-logout">Logout</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
