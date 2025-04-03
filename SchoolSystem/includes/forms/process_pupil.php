<?php
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $dob = $_POST['dob'];
    $classID = intval($_POST['classID']);
    $parent1ID = intval($_POST['parent1ID']);
    $parent2ID = !empty($_POST['parent2ID']) ? intval($_POST['parent2ID']) : NULL;
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO Pupils (FirstName, LastName, DateOfBirth, ClassID, Parent1ID, Parent2ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $firstName, $lastName, $dob, $classID, $parent1ID, $parent2ID);
    
    if ($stmt->execute()) {
        header("Location: ../views/view_pupils.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>