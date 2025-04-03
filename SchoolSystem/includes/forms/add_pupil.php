<?php include '../includes/header.php'; ?>

<h2>Add New Pupil</h2>

<form method="post" action="process_pupil.php">
    <div>
        <label>First Name:</label>
        <input type="text" name="firstName" required>
    </div>
    
    <div>
        <label>Last Name:</label>
        <input type="text" name="lastName" required>
    </div>
    
    <div>
        <label>Date of Birth:</label>
        <input type="date" name="dob" required>
    </div>
    
    <div>
        <label>Class:</label>
        <select name="classID" required>
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
    
    <div>
        <label>Parent/Guardian 1:</label>
        <select name="parent1ID" required>
            <?php
            include '../includes/db_connect.php';
            $sql = "SELECT ParentID, CONCAT(FirstName, ' ', LastName) AS ParentName FROM Parents";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".$row['ParentID']."'>".$row['ParentName']."</option>";
                }
            }
            $conn->close();
            ?>
        </select>
    </div>
    
    <div>
        <label>Parent/Guardian 2 (optional):</label>
        <select name="parent2ID">
            <option value="">None</option>
            <?php
            include '../includes/db_connect.php';
            $sql = "SELECT ParentID, CONCAT(FirstName, ' ', LastName) AS ParentName FROM Parents";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".$row['ParentID']."'>".$row['ParentName']."</option>";
                }
            }
            $conn->close();
            ?>
        </select>
    </div>
    
    <button type="submit">Add Pupil</button>
</form>

<?php include '../includes/footer.php'; ?>