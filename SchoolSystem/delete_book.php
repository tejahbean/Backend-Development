
<?php
// Tejah Bean - Original code with detailed documentation
require 'db_config.php';

if (isset($_GET['id'])) {
  $stmt = $pdo->prepare("DELETE FROM librarybooks WHERE BookID = ?");
  $stmt->execute([$_GET['id']]);
  
  header("Location: admin-portal.php?success=book_deleted");
  exit();
}