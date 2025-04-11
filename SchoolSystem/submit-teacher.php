<?php
require 'db_config.php';
// Tejah Bean - Original code with detailed documentation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare("INSERT INTO teachers 
            (FirstName, LastName, Email, SubjectID, PhoneNumber, 
             AnnualSalary, BackgroundCheckStatus, HireDate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())");
        
        $stmt->execute([
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['email'],
            $_POST['subjectID'],
            $_POST['phone'],
            $_POST['salary'],
            $_POST['background_check']
        ]);
        
        header("Location: admin-portal.php?success=teacher_added");
        exit();
    } catch (PDOException $e) {
        die("Error adding teacher: " . $e->getMessage());
    }
}
?>