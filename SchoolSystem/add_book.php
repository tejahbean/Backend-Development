
<?php
// Tejah Bean - Original code with detailed documentation

// Start the PHP session to enable the use of session variables across pages
session_start(); 

// Include database configuration file to establish a connection
require 'db_config.php';

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  // Retrieve the title and author values from the submitted form data
  $title = $_POST['title'];
  $author = $_POST['author'];

  // Prepare an SQL statement to insert the new book into the library database
  $stmt = $pdo->prepare("INSERT INTO librarybooks (Title, Author, Status) 
                        VALUES (?, ?, 'Available')");

  // Execute the prepared statement with the provided title and author values
  $stmt->execute([$title, $author]);
  
  // Redirect to the admin portal page with a success message parameter
  header("Location: admin-portal.php?success=book_added");

  // Terminate the script to prevent further code execution after redirect
  exit();
}
