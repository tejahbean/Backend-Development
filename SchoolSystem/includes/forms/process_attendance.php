<?php
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['attendanceDate'];
    $classID = intval($_POST['classID']);
    $attendanceRecords = $_POST['attendance'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        foreach ($attendanceRecords as $pupilID => $status) {
            $pupilID = intval($pupilID);
            $status = $conn->real_escape_string($status);
            
            // Check if attendance already exists for this pupil on this date
            $checkSql = "SELECT AttendanceID FROM Attendance WHERE PupilID = ? AND Date = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("is", $pupilID, $date);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                // Update existing record
                $updateSql = "UPDATE Attendance SET Status = ? WHERE PupilID = ? AND Date = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("sis", $status, $pupilID, $date);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Insert new record
                $insertSql = "INSERT INTO Attendance (PupilID, Date, Status) VALUES (?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("iss", $pupilID, $date, $status);
                $insertStmt->execute();
                $insertStmt->close();
            }
            
            $checkStmt->close();
        }
        
        $conn->commit();
        header("Location: ../views/view_attendance.php?classID=$classID&date=$date&success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
$conn->close();
?>