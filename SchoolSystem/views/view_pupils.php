<?php include '../includes/header.php'; ?>

<h2>Pupil Records</h2>

<?php
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<p style='color:green;'>Pupil added successfully!</p>";
}
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Date of Birth</th>
        <th>Class</th>
        <th>Parent 1</th>
        <th>Parent 2</th>
    </tr>
    
    <?php
    include '../includes/db_connect.php';
    
    $sql = "SELECT p.PupilID, p.FirstName, p.LastName, p.DateOfBirth, 
                   c.ClassName, 
                   CONCAT(par1.FirstName, ' ', par1.LastName) AS Parent1,
                   IFNULL(CONCAT(par2.FirstName, ' ', par2.LastName), 'None') AS Parent2
            FROM Pupils p
            JOIN Classes c ON p.ClassID = c.ClassID
            LEFT JOIN Parents par1 ON p.Parent1ID = par1.ParentID
            LEFT JOIN Parents par2 ON p.Parent2ID = par2.ParentID
            ORDER BY c.ClassName, p.LastName, p.FirstName";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["PupilID"]."</td>
                    <td>".$row["FirstName"]." ".$row["LastName"]."</td>
                    <td>".$row["DateOfBirth"]."</td>
                    <td>".$row["ClassName"]."</td>
                    <td>".$row["Parent1"]."</td>
                    <td>".$row["Parent2"]."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No pupils found</td></tr>";
    }
    $conn->close();
    ?>
</table>

<?php include '../includes/footer.php'; ?>