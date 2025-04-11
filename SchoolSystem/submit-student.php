
<?php
require 'db_config.php';
// Tejah Bean - Original code with detailed documentation

try {
    $pdo->beginTransaction();

    // Insert student
    $stmt = $pdo->prepare("
        INSERT INTO pupils 
        (FirstName, LastName, DateOfBirth, ClassID, Address, MedicalInformation)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['dob'],
        $_POST['classID'],
        $_POST['address'],
        $_POST['medical'] ?? null
    ]);
    $pupilID = $pdo->lastInsertId();

    // Insert parents
    foreach ($_POST['parents'] as $parent) {
        $stmt = $pdo->prepare("
            INSERT INTO parents 
            (FullName, Relationship, Email, Phone)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $parent['name'],
            $parent['relationship'],
            $parent['email'],
            $parent['phone']
        ]);
        $parentID = $pdo->lastInsertId();
        
        // Link to student
        $stmt = $pdo->prepare("
            INSERT INTO pupil_parent (PupilID, ParentID)
            VALUES (?, ?)
        ");
        $stmt->execute([$pupilID, $parentID]);
    }

    $pdo->commit();
    header("Location: admin-portal.php?success=student_added");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: admin-portal.php?error=" . urlencode($e->getMessage()));
    exit();
}