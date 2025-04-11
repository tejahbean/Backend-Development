 
// Tejah Bean - Original code with detailed documentation

// Parents Management - Load and display parents' data
async function loadParents() {
  // Fetch the parents data from the API
  const response = await fetch('/api/parents');
  const parents = await response.json();
  
  // Get the table body element to insert parent data
  const tbody = document.getElementById('parentsList');
  
  // Map through the parents data and generate HTML rows
  tbody.innerHTML = parents.map(parent => `
    <tr>
      <td>${parent.FirstName} ${parent.LastName}</td>
      <td>${parent.Email}</td>
      <td>${parent.Telephone}</td>
      <td>
        <!-- Edit parent button -->
        <button class="btn btn-sm btn-warning" onclick="editParent(${parent.ParentID})">
          <i class="bi bi-pencil"></i>
        </button>
        <!-- Delete parent button -->
        <button class="btn btn-sm btn-danger" onclick="deleteParent(${parent.ParentID})">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    </tr>
  `).join('');
}

// Subjects Management - Load and display subjects
async function loadSubjects() {
  // Fetch the subjects data from the API
  const response = await fetch('/api/subjects');
  const subjects = await response.json();
  
  // Get the list element to display the subjects
  const list = document.getElementById('subjectList');
  
  // Map through the subjects data and generate list items
  list.innerHTML = subjects.map(subject => `
    <li class="list-group-item d-flex justify-content-between align-items-center">
      ${subject.SubjectName}
      <!-- Delete subject button -->
      <button class="btn btn-sm btn-danger" onclick="deleteSubject(${subject.SubjectID})">
        <i class="bi bi-trash"></i>
      </button>
    </li>
  `).join('');
}

// Enrollment Management - Load and display enrollment options
async function loadEnrollment() {
  // Get the class selection dropdown element
  const classSelect = document.getElementById('enrollmentClass');
  
  // Fetch the classes data from the API
  const classes = await (await fetch('/api/classes')).json();
  
  // Populate the class select dropdown with available classes
  classSelect.innerHTML = classes.map(c => `
    <option value="${c.ClassID}">${c.ClassName}</option>
  `).join('');
  
  // Add event listener for when the user selects a class
  classSelect.addEventListener('change', async (e) => {
      const classId = e.target.value;
      
      // Fetch the enrollment data for the selected class
      const response = await fetch(`/api/enrollment/${classId}`);
      const enrollment = await response.json();
      
      // Display the enrollment data in a table format
      document.getElementById('enrollmentGrid').innerHTML = `
        <table class="table">
          <thead>
            <tr>
              <th>Pupil</th>
              <th>Enrollment Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${enrollment.map(entry => `
              <tr>
                <td>${entry.FirstName} ${entry.LastName}</td>
                <td>${entry.EnrollmentDate}</td>
                <td>
                  <!-- Unenroll button -->
                  <button class="btn btn-sm btn-danger" onclick="unenroll(${entry.EnrollmentID})">
                    <i class="bi bi-x-circle"></i>
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;
  });
}

// Initialize all components and event listeners when the page content is loaded
document.addEventListener('DOMContentLoaded', () => {
  // Get all tab triggers (links or buttons to switch tabs)
  const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
  
  // Add event listeners to each tab trigger to load respective content
  tabTriggers.forEach(trigger => {
      trigger.addEventListener('shown.bs.tab', (e) => {
          const target = e.target.getAttribute('href');
          
          // Load data based on the selected tab
          switch(target) {
              case '#manage-parents':
                  loadParents(); // Load parents data when the 'Manage Parents' tab is shown
                  break;
              case '#subjects':
                  loadSubjects(); // Load subjects data when the 'Subjects' tab is shown
                  break;
              case '#enrollment':
                  loadEnrollment(); // Load enrollment data when the 'Enrollment' tab is shown
                  break;
          }
      });
  });
});
