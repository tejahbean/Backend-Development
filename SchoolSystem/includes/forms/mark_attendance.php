<?php include '../includes/header.php'; ?>

<h2>Mark Attendance</h2>

<form method="post" action="process_attendance.php">
    <div>
        <label>Date:</label>
        <input type="date" name="attendanceDate" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    
    <div>
        <label>Class:</label>
        <select name="classID" id="classSelect" required>
            <option value="">Select Class</option>
            <?php
            include '../includes/db_connect.php';
            $sql = "SELECT ClassID, ClassName FROM Classes";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".$row['ClassID']."'>".$row['ClassName']."</option>";
                }
            }
            $conn->close();
            ?>
        </select>
    </div>
    
    <div id="pupilList"></div>
    
    <button type="submit">Submit Attendance</button>
</form>

<script>
document.getElementById('classSelect').addEventListener('change', function() {
    var classID = this.value;
    if (classID) {
        fetch('get_pupils.php?classID=' + classID)
            .then(response => response.text())
            .then(data => {
                document.getElementById('pupilList').innerHTML = data;
            });
    } else {
        document.getElementById('pupilList').innerHTML = '';
    }
});
</script>

<?php include '../includes/footer.php'; ?>