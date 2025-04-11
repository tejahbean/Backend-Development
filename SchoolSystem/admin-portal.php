<?php 
// Tejah Bean - Original code with detailed documentation
// Start the PHP session to enable the use of session variables across pages
session_start();

// Include the database configuration file for establishing a connection
require 'db_config.php';

// ---------------------------
// Display Success Messages
// ---------------------------
if (isset($_GET['success'])) {
    // Define dynamic success messages based on the type of operation
    $message = match($_GET['success']) {
        'teacher_added' => 'Teacher added successfully!',
        'teacher_updated' => 'Teacher updated successfully!',
        'teacher_deleted' => 'Teacher deleted successfully!',
        'student_added' => 'Student successfully enrolled',
        'student_deleted' => 'Student record removed',
        default => 'Operation completed successfully'
    };

    // Output a styled Bootstrap alert box with success feedback
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert' data-auto-dismiss='5000'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}

// ---------------------------
// Display Error Messages
// ---------------------------
if (isset($_GET['error'])) {
    // Securely decode and escape the error message to prevent XSS
    $errorMessage = htmlspecialchars(urldecode($_GET['error']));
    
    // Output a styled Bootstrap alert box with the error
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' data-auto-dismiss='5000'>
            $errorMessage
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}
?>

<!-- Tejah Bean - JavaScript to handle auto-dismiss and URL cleanup -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Automatically close alert messages after a timeout (default 5 seconds)
    const alerts = document.querySelectorAll('[data-auto-dismiss]');
    alerts.forEach(alert => {
        const delay = parseInt(alert.dataset.autoDismiss) || 5000;
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close(); // Dismiss alert using Bootstrap method
        }, delay);
    });
    
    // Remove query parameters from the URL to keep it clean after alert is shown
    const cleanURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.replaceState({}, document.title, cleanURL);
});
</script>

<!-- Tejah Bean - Frontend HTML head setup with Bootstrap and custom styling -->
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Set encoding and responsive layout -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Admin Portal - St Alphonsus Primary</title>

  <!-- Bootstrap CSS v5.3 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Tejah Bean - Custom styling for admin UI -->
  <style>
    body {
      background-color: #fff;
      color: #333;
      font-family: 'Segoe UI', sans-serif;
    }

    /* Navbar design */
    .navbar {
      background-color: #003366;
      padding: 1rem;
    }
    .navbar-brand {
      font-weight: 600;
      color: #fff;
    }

    /* Header titles */
    h2 {
      color: #003366;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    /* Tab container styling */
    .tab-content {
      padding: 2rem;
      background-color: #f9f9f9;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    /* Tab navigation links */
    .nav-tabs .nav-link {
      font-weight: 500;
      color: #003366;
    }
    .nav-tabs .nav-link.active {
      background-color: #003366;
      color: #fff;
      border-color: #003366 #003366 #fff;
    }

    .nav-tabs {
      margin-bottom: 1.5rem;
    }

    /* Form element styling */
    .form-control, .form-select {
      border-radius: 8px;
    }

    /* Submit button styling */
    button[type="submit"] {
      min-width: 140px;
      background-color: #80c442;
      color: #fff;
    }
    button[type="submit"]:hover {
      background-color: #5c8c2f;
    }

    /* Footer design */
    footer {
      margin-top: 40px;
      text-align: center;
      color: #ccc;
    }

    label.form-label {
      font-weight: 500;
    }

    /* Search bar */
    .search-bar {
      width: 100%;
      max-width: 300px;
      margin-bottom: 1rem;
    }

    /* Table cell alignment */
    .table td, .table th {
      vertical-align: middle;
    }

    /* Cards for form sections */
    .card {
      margin-bottom: 1.5rem;
    }

    .card-header {
      font-weight: 600;
      color: #fff;
      background-color: #003366;
    }

    /* Button rounding */
    .btn {
      border-radius: 8px;
    }

    /* Icon spacing in tabs */
    .nav-item .nav-link i {
      margin-right: 8px;
    }
  </style>
</head>

<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <!-- School Admin Brand Logo with Icon -->
    <a class="navbar-brand" href="#"><i class="bi bi-shield-lock-fill me-2"></i>St Alphonsus Admin</a>
  </div>
</nav>

<!-- Main Container for Admin Dashboard -->
<div class="container mt-4">

  <!-- Navigation Tabs for Admin Interface -->
  <ul class="nav nav-tabs" id="adminTabs" role="tablist">

    <!-- Dashboard Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab">
        <i class="bi bi-house-door-fill"></i> Dashboard
      </a>
    </li>

    <!-- Library Management Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="library-tab" data-bs-toggle="tab" href="#library" role="tab">
        <i class="bi bi-book"></i> Library
      </a>
    </li>

    <!-- Add New Teacher Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="add-teacher-tab" data-bs-toggle="tab" href="#add-teacher" role="tab">
        <i class="bi bi-person-plus-fill"></i> Add Teacher
      </a>
    </li>

    <!-- View Existing Teachers Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="view-teachers-tab" data-bs-toggle="tab" href="#view-teachers" role="tab">
        <i class="bi bi-eye-fill"></i> View Teachers
      </a>
    </li>

    <!-- Attendance Recording and Review Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab">
        <i class="bi bi-journal-text"></i> Attendance
      </a>
    </li>

    <!-- Add New Student Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="add-student-tab" data-bs-toggle="tab" href="#add-student" role="tab">
        <i class="bi bi-person-plus-fill"></i> Add Student
      </a>
    </li>

    <!-- View Existing Students Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="view-students-tab" data-bs-toggle="tab" href="#view-students" role="tab">
        <i class="bi bi-people-fill"></i> View Students
      </a>
    </li>

    <!-- Book Loans Tracking Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="book-loans-tab" data-bs-toggle="tab" href="#book-loans" role="tab">
        <i class="bi bi-bookmark-check-fill"></i> Book Loans
      </a>
    </li>

    <!-- Performance Tracking and Analysis Tab -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="performance-tab" data-bs-toggle="tab" href="#performance" role="tab">
        <i class="bi bi-bar-chart-fill"></i> Performance
      </a>
    </li>

    <!-- Database Editing Tab for Admins -->
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="edit-database-tab" data-bs-toggle="tab" href="#edit-database" role="tab">
        <i class="bi bi-pencil-fill"></i> Edit Database
      </a>
    </li>

  </ul>

  <!-- Tab Content Area -->
  <div class="tab-content" id="adminTabsContent">

    <!-- Dashboard Content Panel -->
    <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
      <h2>Welcome, Admin!</h2>
      <p>This is your admin dashboard. Use the tabs above to manage teachers, pupils, attendance, and more.</p>
    </div>

    <!-- Add Teacher Content Panel -->
    <div class="tab-pane fade" id="add-teacher" role="tabpanel" aria-labelledby="add-teacher-tab">
      <h2>Add New Teacher</h2>

      <!-- Teacher Submission Form -->
      <form action="submit-teacher.php" method="POST">

        <!-- First Name Input -->
        <div class="mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="firstName" required>
        </div>

        <!-- Last Name Input -->
        <div class="mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="lastName" required>
        </div>

        <!-- Email Address Input -->
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" name="email" required>
        </div>

        <!-- Subject Dropdown (Dynamically Populated from DB) -->
        <div class="mb-3">
          <label class="form-label">Subject</label>
          <select class="form-select" name="subjectID" required>
            <?php
            // Retrieve and display all available subjects from the database
            $stmt = $pdo->query("SELECT * FROM subjects ORDER BY SubjectName");
            while ($subject = $stmt->fetch()) {
                echo "<option value='{$subject['SubjectID']}'>{$subject['SubjectName']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Phone Number with UK Pattern Validation -->
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="tel" class="form-control" name="phone" pattern="[0-9]{10,11}" 
                 title="UK phone number (10 or 11 digits)" required>
        </div>

        <!-- Annual Salary Input (Min/Max Constraint) -->
        <div class="mb-3">
          <label class="form-label">Annual Salary (£)</label>
          <input type="number" class="form-control" name="salary" 
                 step="0.01" min="25000" max="100000" required>
        </div>

        <!-- Background Check Status Dropdown -->
        <div class="mb-3">
          <label class="form-label">Background Check Status</label>
          <select class="form-select" name="background_check" required>
            <option value="Completed">Completed</option>
            <option value="Pending">Pending</option>
            <option value="Failed">Failed</option>
          </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>

<!-- =======================
     VIEW TEACHERS TAB
     ======================= -->
     <div class="tab-pane fade" id="view-teachers" role="tabpanel">
  <h2>View Teachers</h2>

  <!-- Search input for filtering teachers -->
  <div class="search-bar mb-3">
    <input type="text" id="teacherSearch" class="form-control" placeholder="Search teachers...">
  </div>

  <!-- Teachers table displaying essential details -->
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Name</th>
        <th>Subject</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Salary</th>
        <th>Background Check</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="teacherTableBody">
      <?php
      // Query to fetch teachers and their associated subjects
      $stmt = $pdo->query("
          SELECT t.*, s.SubjectName 
          FROM teachers t
          LEFT JOIN subjects s ON t.SubjectID = s.SubjectID
          ORDER BY t.LastName
      ");

      // Loop through each teacher and render a row
      while ($teacher = $stmt->fetch()) {
          echo "<tr data-teacher-id='{$teacher['TeacherID']}'>
                  <td>{$teacher['FirstName']} {$teacher['LastName']}</td>
                  <td>{$teacher['SubjectName']}</td>
                  <td>{$teacher['Email']}</td>
                  <td>{$teacher['PhoneNumber']}</td>
                  <td>£" . number_format($teacher['AnnualSalary'], 2) . "</td>
                  
                  <!-- Badge indicates background check status -->
                  <td>
                      <span class='badge bg-" . 
                      ($teacher['BackgroundCheckStatus'] == 'Completed' ? 'success' : 
                      ($teacher['BackgroundCheckStatus'] == 'Pending' ? 'warning' : 'danger')) . "'>
                      {$teacher['BackgroundCheckStatus']}
                      </span>
                  </td>
                  
                  <!-- Action buttons for editing or deleting teacher -->
                  <td>
                      <button class='btn btn-warning btn-sm edit-teacher' 
                              data-bs-toggle='modal' 
                              data-bs-target='#editTeacherModal'>
                          <i class='bi bi-pencil-square'></i> Edit
                      </button>
                      <button class='btn btn-danger btn-sm delete-teacher' 
                              data-teacher-id='{$teacher['TeacherID']}'>
                          <i class='bi bi-trash'></i> Delete
                      </button>
                  </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- =======================
     ADD STUDENT TAB
     ======================= -->
<div class="tab-pane fade" id="add-student" role="tabpanel">
  <h2>Add New Student</h2>
  
  <!-- Student registration form -->
  <form action="submit-student.php" method="POST" id="studentForm">
    
    <!-- Age restriction using max DOB -->
    <div class="mb-3">
      <label class="form-label">Date of Birth (Must be under 18)</label>
      <input type="date" class="form-control" name="dob" 
             max="<?= date('Y-m-d', strtotime('-18 years')) ?>" 
             required>
    </div>

    <!-- Parent/Guardian section: dynamic repeatable block -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        Parent/Guardian Information
      </div>
      <div class="card-body" id="parentFields">
        <div class="parent-group">
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="parents[0][name]" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Relationship</label>
              <select class="form-select" name="parents[0][relationship]" required>
                <option value="Mother">Mother</option>
                <option value="Father">Father</option>
                <option value="Guardian">Guardian</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="parents[0][email]" required>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Phone</label>
              <input type="tel" class="form-control" name="parents[0][phone]" required>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <!-- Button to dynamically add another parent entry -->
        <button type="button" class="btn btn-sm btn-success" id="addParent">
          <i class="bi bi-plus-circle"></i> Add Another Parent
        </button>
      </div>
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn btn-primary">Add Student</button>
  </form>
</div>

<!-- =======================
     VIEW STUDENTS TAB
     ======================= -->
<div class="tab-pane fade" id="view-students" role="tabpanel">
  <h2>Student Directory</h2>

  <!-- Table listing students with age, class, and parents -->
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Class</th>
        <th>Parents</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Query to get students, their class, and associated parents
      $stmt = $pdo->query("
          SELECT p.*, c.ClassName,
                 GROUP_CONCAT(CONCAT(pr.FullName, ' (', pr.Relationship, ')') SEPARATOR ', ') AS Parents
          FROM pupils p
          LEFT JOIN classes c ON p.ClassID = c.ClassID
          LEFT JOIN pupil_parent pp ON p.PupilID = pp.PupilID
          LEFT JOIN parents pr ON pp.ParentID = pr.ParentID
          GROUP BY p.PupilID
          ORDER BY p.LastName
      ");

      // Display student records
      while ($student = $stmt->fetch()) {
          $age = date_diff(date_create($student['DateOfBirth']), date_create('today'))->y;
          $parents = $student['Parents'] ?? 'No parents registered';

          echo "<tr>
                  <td>{$student['FirstName']} {$student['LastName']}</td>
                  <td>{$age}</td>
                  <td>{$student['ClassName']}</td>
                  <td>{$parents}</td>
                  <td>
                    <button class='btn btn-warning btn-sm edit-student'>
                      <i class='bi bi-pencil'></i>
                    </button>
                    <button class='btn btn-danger btn-sm delete-student' 
                            data-student-id='{$student['PupilID']}'>
                      <i class='bi bi-trash'></i>
                    </button>
                  </td>
                </tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- =======================
     ATTENDANCE TAB
     ======================= -->
<div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
  <h2>Manage Attendance</h2>
  <form>
    <!-- Select date for attendance entry -->
    <div class="mb-3">
      <label for="attendanceDate" class="form-label">Date</label>
      <input type="date" class="form-control" name="attendanceDate" required>
    </div>

    <!-- Select attendance status -->
    <div class="mb-3">
      <label for="attendanceStatus" class="form-label">Status</label>
      <select class="form-select" name="attendanceStatus" required>
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
      </select>
    </div>

    <!-- Submit attendance -->
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>

<!-- =======================
     LIBRARY TAB
     ======================= -->
<div class="tab-pane fade" id="library" role="tabpanel" aria-labelledby="library-tab">
  <h2>Library Management</h2>

  <!-- Book search filter -->
  <div class="mb-3">
    <input type="text" class="form-control" id="bookSearch" placeholder="Search books...">
  </div>

  <!-- Book listing table -->
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Fetch all books from the library
      $books = $pdo->query("SELECT * FROM librarybooks");
      foreach ($books as $book) {
          echo "<tr>
                  <td>{$book['Title']}</td>
                  <td>{$book['Author']}</td>
                  <td><span class='badge bg-".($book['Status'] == 'Available' ? 'success' : 'warning')."'>
                      {$book['Status']}</span></td>
                  <td>
                    <!-- Delete book button -->
                    <button class='btn btn-sm btn-danger' 
                            onclick='deleteBook({$book['BookID']})'>
                      <i class='bi bi-trash'></i>
                    </button>
                  </td>
                </tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Form to add a new book -->
  <h3>Add New Book</h3>
  <form action="add_book.php" method="POST">
    <div class="mb-3">
      <input type="text" class="form-control" name="title" placeholder="Book Title" required>
    </div>
    <div class="mb-3">
      <input type="text" class="form-control" name="author" placeholder="Author">
    </div>
    <button type="submit" class="btn btn-success">Add Book</button>
  </form>
</div>

<!-- Footer Section -->
<footer>
  <p>&copy; 2025 St Alphonsus Primary. All rights reserved.</p>
</footer>

<!-- Bootstrap JavaScript Bundle (includes Popper.js for tooltips & dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============================ -->
<!-- Edit Teacher Modal Component -->
<!-- ============================ -->
<!-- Modal triggered when editing teacher details -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal header with title and close button -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal form that submits to update-teacher.php -->
      <form id="editTeacherForm" action="update-teacher.php" method="POST">
        <div class="modal-body">
          <!-- Hidden input to hold Teacher ID for editing -->
          <input type="hidden" name="teacherID" id="editTeacherID">
          
          <!-- Teacher First and Last Name Inputs -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="firstName" id="editFirstName" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="lastName" id="editLastName" required>
            </div>
          </div>

          <!-- Email and Phone Number Inputs -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" id="editEmail" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" name="phone" id="editPhone" 
                     pattern="[0-9]{10,11}" title="UK format: 10 or 11 digits" required>
            </div>
          </div>

          <!-- Subject Dropdown and Salary Input -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Subject</label>
              <select class="form-select" name="subjectID" id="editSubjectID" required>
                <?php
                // Dynamically populate dropdown with subjects from database
                $stmt = $pdo->query("SELECT * FROM subjects ORDER BY SubjectName");
                while ($subject = $stmt->fetch()) {
                    echo "<option value='{$subject['SubjectID']}'>{$subject['SubjectName']}</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Annual Salary (£)</label>
              <div class="input-group">
                <span class="input-group-text">£</span>
                <input type="number" class="form-control" name="salary" id="editSalary" 
                       step="0.01" min="25000" max="100000" required>
              </div>
            </div>
          </div>

          <!-- Background Check Dropdown -->
          <div class="mb-3">
            <label class="form-label">Background Check Status</label>
            <select class="form-select" name="background_check" id="editBackgroundCheck" required>
              <option value="Completed">Completed</option>
              <option value="Pending">Pending</option>
              <option value="Failed">Failed</option>
            </select>
          </div>
        </div>

        <!-- Modal Footer Buttons -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================== -->
<!-- JavaScript: Edit Teacher -->
<!-- ========================== -->
<script>
// Wait for DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Attach click event listener to all 'edit-teacher' buttons
    document.querySelectorAll('.edit-teacher').forEach(button => {
        button.addEventListener('click', function() {
            // Get the teacher ID from the row's data attribute
            const teacherID = this.closest('tr').dataset.teacherId;

            // Fetch teacher details from the server
            fetch(`get-teacher.php?id=${teacherID}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(teacher => {
                    // Populate the modal form fields with fetched data
                    document.getElementById('editTeacherID').value = teacher.TeacherID;
                    document.getElementById('editFirstName').value = teacher.FirstName;
                    document.getElementById('editLastName').value = teacher.LastName;
                    document.getElementById('editEmail').value = teacher.Email;
                    document.getElementById('editPhone').value = teacher.PhoneNumber;
                    document.getElementById('editSubjectID').value = teacher.SubjectID;
                    document.getElementById('editSalary').value = teacher.AnnualSalary;
                    document.getElementById('editBackgroundCheck').value = teacher.BackgroundCheckStatus;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading teacher data');
                });
        });
    });

    // Client-side form validation on submit
    document.getElementById('editTeacherForm').addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated'); // Bootstrap visual feedback
        }
    });
});
</script>

<!-- ============================= -->
<!-- JavaScript: Delete Teacher -->
<!-- ============================= -->
<script>
document.querySelectorAll('.delete-teacher').forEach(button => {
    button.addEventListener('click', function() {
        const teacherID = this.dataset.teacherId;
        const teacherName = this.closest('tr').querySelector('td:first-child').textContent;
        
        // Confirm before deletion
        if (confirm(`Are you sure you want to delete ${teacherName}? This action cannot be undone!`)) {
            // Send delete request
            fetch(`delete-teacher.php?id=${teacherID}`)
                .then(response => {
                    if (response.ok) {
                        window.location.reload(); // Reload to reflect changes
                    } else {
                        alert('Error deleting teacher');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error - please try again');
                });
        }
    });
});
</script>

<!-- ============================== -->
<!-- JavaScript: Student Search -->
<!-- ============================== -->
<script>
document.getElementById('studentSearch').addEventListener('input', function() {
  const searchValue = this.value.toLowerCase();
  const rows = document.querySelectorAll('#studentTableBody tr');

  // Show/hide rows based on match
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchValue) ? '' : 'none';
  });
});
</script>

<!-- ============================== -->
<!-- JavaScript: Teacher Search -->
<!-- ============================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('teacherSearch');
    const tableBody = document.getElementById('teacherTableBody');

    // Live filtering of teacher table rows
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = tableBody.getElementsByTagName('tr');

        Array.from(rows).forEach(row => {
            const cells = row.getElementsByTagName('td');
            let matchFound = false;

            // Match against first four cells (Name, Subject, Email, Phone)
            for (let i = 0; i < 4; i++) {
                const cellText = cells[i].textContent.toLowerCase();
                if (cellText.includes(searchTerm)) {
                    matchFound = true;
                    break;
                }
            }

            row.style.display = matchFound ? '' : 'none';
        });
    });
});
</script>

<!-- =================================== -->
<!-- JavaScript: Dynamic Parent Fields -->
<!-- =================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let parentCount = 1; // Initial parent count
    const parentFields = document.getElementById('parentFields');

    // Add new parent fields when button clicked
    document.getElementById('addParent').addEventListener('click', function() {
        const newIndex = parentCount++;
        const parentGroup = document.createElement('div');
        parentGroup.className = 'parent-group mt-3';
        parentGroup.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" 
                           name="parents[${newIndex}][name]" 
                           placeholder="Full Name" required>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-select" name="parents[${newIndex}][relationship]" required>
                        <option value="Mother">Mother</option>
                        <option value="Father">Father</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input type="email" class="form-control" 
                           name="parents[${newIndex}][email]" 
                           placeholder="Email" required>
                </div>
                <div class="col-md-2 mb-3">
                    <input type="tel" class="form-control" 
                           name="parents[${newIndex}][phone]" 
                           placeholder="Phone" required>
                </div>
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-parent">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>`;

        // Enable remove button on each dynamic parent section
        parentGroup.querySelector('.remove-parent').addEventListener('click', function() {
            parentGroup.remove();
            parentCount--; // Adjust counter
        });

        parentFields.appendChild(parentGroup);
    });
});
</script>

</body>
</html>


