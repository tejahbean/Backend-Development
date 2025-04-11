<?php
// Tejah Bean - Original code with detailed documentation

// Start a session to track user login state across different pages
session_start();

// Redirect to login.php if the role is selected
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];  // Get the role from the dropdown

    // Redirect to login page if "Admin" is selected
    if ($role === 'Admin') {
        header("Location: login.php");
        exit;
    } else {
        // Handle other roles (Teacher, Parent) here if needed
        $_SESSION['role'] = $role;  // Store selected role in session
        header("Location: dashboard.php"); // Example redirect
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St Alphonsus Primary School - Portal</title>
    <!-- Link to Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to FontAwesome for professional icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Basic styling for the page */
        body {
            background-color: #f0f7ff; /* Light blue background */
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .landing-container {
            width: 100%;
            max-width: 600px;
            margin: 5rem auto;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: 1px solid #dbe2e6; /* Light gray border for contrast */
        }

        .landing-container h2 {
            text-align: center;
            color: #006400; /* Dark green for heading */
            margin-bottom: 1.5rem;
            font-weight: bold;
            font-size: 2rem;
        }

        .btn-custom {
            width: 100%;
            background-color: #0066cc; /* Blue background for button */
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #004d99; /* Darker blue on hover */
        }

        .btn-create-account {
            background-color: #28a745; /* Green background for "Create Account" button */
            margin-top: 10px;
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            font-weight: bold;
        }

        .btn-create-account:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #006400; /* Green border for select */
            padding: 12px;
        }

        .school-icon {
            font-size: 80px;
            color: #006400; /* Green color for icon */
            display: block;
            margin: 0 auto 1rem;
        }

        .school-motto {
            font-style: italic;
            color: #004d99; /* Blue color for motto */
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .school-info {
            text-align: center;
            margin-top: 2rem;
        }

        .school-info p {
            font-size: 1.2rem;
            color: #0066cc; /* Blue color */
        }

        .school-info p a {
            color: #0066cc;
            text-decoration: none;
        }

        .school-info p a:hover {
            text-decoration: underline;
        }

        /* Enhancing padding and spacing */
        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
        }

        /* Additional responsive adjustments */
        @media (max-width: 768px) {
            .landing-container {
                padding: 1.5rem;
            }
            .landing-container h2 {
                font-size: 1.5rem;
            }
            .school-info p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="landing-container">
    <!-- School Icon and Motto -->
    <i class="fas fa-school school-icon"></i> <!-- FontAwesome school icon -->
    <p class="school-motto">"Nurturing Future Leaders, One Student at a Time"</p> <!-- School Motto -->

    <h2>Welcome to St Alphonsus Primary School</h2>
    <p class="text-center">Please select your role to proceed</p>

    <!-- Role Selection Form -->
    <form action="index.php" method="POST">
        <div class="mb-4">
            <label class="form-label">Select Role</label>
            <select class="form-select" name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="Admin">Admin</option>
                <option value="Teacher">Teacher</option>
                <option value="Parent">Parent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-custom">Proceed</button>
    </form>

    <!-- Create Account Button Section -->
    <div class="text-center mt-4">
        <i class="fas fa-user-plus" style="font-size: 30px; color: #28a745;"></i> <!-- Create Account Icon -->
        <p>Create a new account to access the portal</p>
        <a href="create_account.php" class="btn btn-create-account">Create Account</a>
    </div>

    <!-- Optional: Info about the school -->
    <div class="school-info">
        <p>If you are an Admin, please select admin then proceed <a href="login.php">Login</a></p>
        <p>For more information about St Alphonsus, visit our website: <a href="#">www.StAlphonsus.com</a></p>
    </div>
</div>


<!-- Bootstrap JS for any dynamic functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

