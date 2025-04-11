
<!-- =====================
     STUDENT LIST // Tejah Bean - Original code with detailed documentation
     ===================== -->
     
     <div class="tab-pane fade" id="view-students" role="tabpanel">
  <h2>Student Directory</h2>
  
  <!-- Search Bar -->
  <div class="search-bar mb-3">
    <input type="text" id="searchStudents" class="form-control" 
           placeholder="Search by name or class...">
  </div>

  <!-- Student Table -->
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Date of Birth</th>
        <th>Class</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Fetch students with class info
      $stmt = $pdo->query("
          SELECT p.*, c.ClassName 
          FROM pupils p
          LEFT JOIN classes c ON p.ClassID = c.ClassID
          ORDER BY p.LastName
      ");
      
      while ($student = $stmt->fetch()) {
          echo "
          <tr data-student-id='{$student['PupilID']}'>
            <td>{$student['FirstName']} {$student['LastName']}</td>
            <td>" . date('d/m/Y', strtotime($student['DateOfBirth'])) . "</td>
            <td>{$student['ClassName']}</td>
            <td>
              <button class='btn btn-sm btn-warning edit-student'>
                <i class='bi bi-pencil'></i>
              </button>
              <button class='btn btn-sm btn-danger delete-student' 
                      data-id='{$student['PupilID']}'>
                <i class='bi bi-trash'></i>
              </button>
            </td>
          </tr>";
      }
      ?>
    </tbody>
  </table>
</div>