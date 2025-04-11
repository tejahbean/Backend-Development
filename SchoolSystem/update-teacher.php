
<?php
require 'db_config.php';
// Tejah Bean - Original code with detailed documentation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare("
            UPDATE teachers 
            SET 
                FirstName = ?, 
                LastName = ?, 
                Email = ?, 
                SubjectID = ?,
                PhoneNumber = ?,
                AnnualSalary = ?,
                BackgroundCheckStatus = ?
            WHERE TeacherID = ?
        ");
        
        $stmt->execute([
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['email'],
            $_POST['subjectID'],
            $_POST['phone'],
            $_POST['salary'],
            $_POST['background_check'],
            $_POST['teacherID']
        ]);
        
        header("Location: admin-portal.php?success=teacher_updated");
        exit();
    } catch (PDOException $e) {
        die("Error updating teacher: " . $e->getMessage());
    }
}
?>