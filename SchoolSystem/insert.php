<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $class = $_POST['class'];
    $parent_contact = $_POST['parent_contact'];

    $stmt = $conn->prepare("INSERT INTO students (name, age, class, parent_contact) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $name, $age, $class, $parent_contact);

    if ($stmt->execute()) {
        echo "New record added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
