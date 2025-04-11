<?php
/*
STUDENT SUBMISSION HANDLER
- Validates input
- Prevents SQL injection
- Handles database errors
*/
// Tejah Bean - Original code with detailed documentation

require 'db_config.php';

try {
    // Validate required fields
    $required = ['firstName', 'lastName', 'dob', 'classID'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Start transaction
    $pdo->beginTransaction();

    // Prepare SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO pupils 
        (FirstName, LastName, DateOfBirth, ClassID, Address, MedicalInformation)
        VALUES (:fname, :lname, :dob, :class, :address, :medical)
    ");

    // Execute with named parameters
    $stmt->execute([
        ':fname' => htmlspecialchars($_POST['firstName']),
        ':lname' => htmlspecialchars($_POST['lastName']),
        ':dob'   => $_POST['dob'],
        ':class' => (int)$_POST['classID'],
        ':address' => $_POST['address'] ?? null,
        ':medical' => $_POST['medical'] ?? null
    ]);

    // Commit transaction
    $pdo->commit();
    
    // Redirect with success
    header("Location: admin-portal.php?success=student_added");
    exit();

} catch (PDOException $e) {
    // Database error handling
    $pdo->rollBack();
    $errorCode = $e->getCode();
    
    $message = match($errorCode) {
        1452 => "Invalid class selection - class doesn't exist",
        1406 => "Input too long - check field lengths",
        default => "Database error #$errorCode"
    };
    
    header("Location: admin-portal.php?error=" . urlencode($message));
    exit();
    
} catch (Exception $e) {
    // General error handling
    header("Location: admin-portal.php?error=" . urlencode($e->getMessage()));
    exit();
}






