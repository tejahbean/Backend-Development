<?php
// Tejah Bean - Original code with detailed documentation
require 'db_config.php';

try {
    // Validate teacher ID exists
    if (!isset($_GET['id'])) {
        throw new Exception("No teacher specified for deletion");
    }

    // Sanitize and validate ID
    $teacherID = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$teacherID || $teacherID < 1) {
        throw new Exception("Invalid teacher identifier");
    }

    // Start transaction
    $pdo->beginTransaction();

    // 1. Check if teacher exists
    $stmt = $pdo->prepare("SELECT TeacherID FROM teachers WHERE TeacherID = ?");
    $stmt->execute([$teacherID]);
    if (!$stmt->fetch()) {
        throw new Exception("Teacher record not found");
    }

    // 2. Check for class associations
    $stmt = $pdo->prepare("SELECT ClassID FROM classes WHERE TeacherID = ?");
    $stmt->execute([$teacherID]);
    if ($stmt->fetch()) {
        throw new Exception("Cannot delete - teacher is assigned to classes");
    }

    // 3. Delete from users table (if using separate auth system)
    $stmt = $pdo->prepare("DELETE FROM users WHERE AssociatedID = ? AND Role = 'Teacher'");
    $stmt->execute([$teacherID]);

    // 4. Delete teacher record
    $stmt = $pdo->prepare("DELETE FROM teachers WHERE TeacherID = ?");
    $stmt->execute([$teacherID]);

    // Commit transaction
    $pdo->commit();

    // Redirect with success
    header("Location: admin-portal.php?success=teacher_deleted");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    $errorMessage = match ($e->getCode()) {
        1451 => "Teacher cannot be deleted - linked to existing records",
        default => "Database error: " . $e->getMessage()
    };
    header("Location: admin-portal.php?error=" . urlencode($errorMessage));
    exit();
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: admin-portal.php?error=" . urlencode($e->getMessage()));
    exit();
}