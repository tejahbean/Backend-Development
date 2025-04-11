<?php
// Tejah Bean - Original code with detailed documentation
// Start a session to track user login state across different pages
session_start();

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];  // Get the role from the dropdown
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class = $_POST['class'];
    $message = $_POST['message'];
    $assistantDetails = isset($_POST['assistant_details']) ? $_POST['assistant_details'] : ''; // TA-specific input

    // Example: Send an email to the admin for the request (to add a student, teacher, or TA)
    $adminEmail = "admin@school.com";
    $subject = "New Request to Add $role";
    $body = "A new request has been made to add a $role:\n\nName: $name\nEmail: $email\nClass: $class\nMessage: $message";
    
    if ($role == "Teaching Assistant") {
        $body .= "\nAssistant Details: $assistantDetails"; // Add TA-specific info to email body
    }
    
    $headers = "From: no-reply@school.com";

    // Send email to Admin
    mail($adminEmail, $subject, $body, $headers);

    // Send confirmation email to the user (for extra points)
    $confirmationSubject = "Request to Add $role Submitted Successfully";
    $confirmationBody = "Dear $name,\n\nYour request to add a $role has been successfully submitted. Our admin will review it soon.\n\nThank you!";
    mail($email, $confirmationSubject, $confirmationBody, $headers);

    // Set success message
    $_SESSION['success'] = "Your request has been successfully submitted!";
    header("Location: create_account.php?success=true");  // Redirect to show success modal
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - St Alphonsus Primary School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f7ff;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 5rem auto;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: 1px solid #dbe2e6;
        }

        .heading {
            text-align: center;
            color: #006400;
            margin-bottom: 2rem;
            font-weight: bold;
            font-size: 2rem;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
        }

        .btn-custom {
            width: 100%;
            background-color: #0066cc;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #004d99;
        }

        .success-message {
            color: green;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .error-message {
            color: red;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .icon {
            font-size: 40px;
            color: #006400;
            display: block;
            margin: 0 auto 2rem;
        }

        .school-motto {
            font-style: italic;
            color: #004d99;
            text-align: center;
            font-size: 1.2rem;
            margin-top: 2rem;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
        }

        /* Disable input fields after form submission */
        .disabled-field {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body>

<div class="container">
    <i class="fas fa-user-plus icon"></i>
    <h2 class="heading">Create Account Request</h2>

    <!-- Success or Error Messages -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success text-center" role="alert">
            Your request has been successfully submitted! You will receive a confirmation email shortly.
        </div>
    <?php endif; ?>

    <!-- Form to Request Admin to Add a Student, Teacher, or Teaching Assistant -->
    <form action="create_account.php" method="POST" id="createAccountForm">
        <div class="mb-4">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" required <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>>
        </div>

        <div class="mb-4">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email" required <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>>
        </div>

        <div class="mb-4">
            <label class="form-label">Role</label>
            <select class="form-control" name="role" required <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>>
                <option value="Student">Student</option>
                <option value="Teacher">Teacher</option>
                <option value="Teaching Assistant">Teaching Assistant</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Class (For Student)</label>
            <input type="text" class="form-control" name="class" <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>>
            <small class="form-text text-muted">If adding a teacher, leave blank.</small>
        </div>

        <div class="mb-4">
            <label class="form-label">Additional Information</label>
            <textarea class="form-control" name="message" rows="4" <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>></textarea>
        </div>

        <!-- Teaching Assistant-specific details (shown only when the role is Teaching Assistant) -->
        <div class="mb-4" id="assistantDetailsSection" style="display: none;">
            <label class="form-label">Teaching Assistant Specific Details</label>
            <input type="text" class="form-control" name="assistant_details" placeholder="Subject/Department" <?php echo isset($_GET['success']) ? 'class="disabled-field"' : ''; ?>>
        </div>

        <button type="submit" class="btn btn-custom" <?php echo isset($_GET['success']) ? 'disabled' : ''; ?>>Submit Request</button>
    </form>

    <p class="school-motto">"Nurturing Future Leaders, One Student at a Time"</p>

</div>

<!-- Modal for Success Notification -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Request Submitted Successfully</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Your request has been successfully submitted! You will receive a confirmation email shortly.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS for Modal functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Trigger the success modal after form submission
    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        myModal.show();
    <?php endif; ?>

    // Show TA details input only if role is Teaching Assistant
    document.querySelector('[name="role"]').addEventListener('change', function() {
        const assistantDetailsSection = document.getElementById('assistantDetailsSection');
        if (this.value === 'Teaching Assistant') {
            assistantDetailsSection.style.display = 'block';
        } else {
            assistantDetailsSection.style.display = 'none';
        }
    });
</script>

</body>
</html>


