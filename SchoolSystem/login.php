<?php
session_start();
include('db_config.php');

// Tejah Bean - Original code with detailed documentation

// Database connection
$servername = "localhost";
$dbUsername = "root"; 
$dbPassword = "";
$dbname = "school_system";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs safely
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Debug: Print the submitted username and password to check if the form is being submitted
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br>";

    // Sanitize user input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Validate inputs
    if (!empty($username) && !empty($password)) {
        // Query the database to check if the user exists
        $query = "SELECT * FROM admin_users WHERE Username = '$username'";
        $result = mysqli_query($conn, $query);

        // Check if user exists
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verify the password using password_verify() for hashed passwords
            if (password_verify($password, $user['PasswordHash'])) {
                // Successful login
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                header("Location: admin-portal.php"); // redirect to admin portal
                exit;
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f7fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-container {
      width: 100%;
      max-width: 400px;
      margin: 5rem auto;
      padding: 2rem;
      background-color: #fff;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    .login-container h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 1.5rem;
    }
    .btn-custom {
      width: 100%;
    }
    .form-control {
      border-radius: 8px;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2><i class="bi bi-shield-lock-fill me-2"></i>Admin Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" name="username" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-custom"><i class="bi bi-lock-fill me-1"></i>Login</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


