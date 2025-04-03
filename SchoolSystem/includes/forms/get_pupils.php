<?php
include '../includes/db_connect.php';

$classID = intval($_GET['classID']);
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql = "SELECT p.PupilID, CONCAT(p.FirstName, ' ', p.LastName) AS PupilName
        FROM Pupils p
        WHERE p.ClassID = ?
        ORDER BY p.LastName, p.FirstName";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div>
                <label>".$row['PupilName']."</label>
                <select name='attendance[".$row['PupilID']."]' required>
                    <option value='Present'>Present</option>
                    <option value='Absent'>Absent</option>
                    <option value='Late'>Late</option>
                </select>
              </div>";
    }
} else {
    echo "No pupils found in this class";
}

$stmt->close();
$conn->close();
?>