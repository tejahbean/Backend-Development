<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $class = $_POST['class'];
    $parent_contact = $_POST['parent_contact'];

    $stmt = $conn->prepare("UPDATE students SET name=?, age=?, class=?, parent_contact=? WHERE id=?");
    $stmt->bind_param("sissi", $name, $age, $class, $parent_contact, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
