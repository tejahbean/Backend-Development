<?php
// Tejah Bean - Original code with detailed documentation
require 'db_config.php';

if (isset($_GET['id'])) {
    $teacherID = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("
        SELECT 
            TeacherID, 
            FirstName, 
            LastName, 
            Email, 
            SubjectID,
            PhoneNumber,
            AnnualSalary,
            BackgroundCheckStatus
        FROM teachers 
        WHERE TeacherID = ?
    ");
    $stmt->execute([$teacherID]);
    $teacher = $stmt->fetch();
    
    if ($teacher) {
        header('Content-Type: application/json');
        echo json_encode($teacher);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Teacher not found']);
    }
    exit();
}
?>