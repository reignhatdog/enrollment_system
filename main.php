<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCU Enrollment System</title>
    <link rel="icon" type="image/png" href="qcu_logo_circular.png">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Menu</h3>
                <p>QCU Enrollment System</p>
            </div>
            <div class="sidebar-content">
                <ul class="sidebar-menu">
                    <li>
                        <a href="main.php" class="active">
                            <span class="menu-icon"></span>
                            Main
                        </a>
                    </li>
                    <li>
                        <a href="dashboard.php">
                            <span class="menu-icon"></span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="id_creation.php">
                            <span class="menu-icon"></span>
                            Create ID
                        </a>
                    </li>
                    <li>
                        <a href="id_generator.php">
                            <span class="menu-icon"></span>
                            ID Layout Generator
                        </a>
                    </li>
                    <li class="logout-item">
                        <a href="logout.php">
                            <span class="menu-icon"></span>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
              
                <div class="menu-toggle">
                    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
                </div>
                
                <img src="logo.jpg" alt="QCU Logo" class="logo">
                <div class="header-text">
                    <h1>Quezon City University</h1>
                    <p>Student Enrollment System</p>
                </div>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('enroll')">New Enrollment</button>
            <button class="nav-tab" onclick="showTab('students')">View Students</button>
        </div>

        <div id="enroll-tab" class="tab-content active">
            <h2>Student Enrollment Form</h2>
            <div id="message"></div>
            
            <form id="enrollmentForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="middleName">Middle Name</label>
                        <input type="text" id="middleName" name="middleName">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Date of Birth</label>
                        <input type="date" id="birthdate" name="birthdate" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Complete Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="program">Program</label>
                        <select id="program" name="program" required>
                            <option value="">Select Program</option>
                            <option value="BSIS">Bachelor of Science in Information Systems (BSIS)</option>
                            <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                            <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                            <option value="BSIE">Bachelor of Science in Industrial Engineering (BSIE)</option>
                            <option value="BSCPE">Bachelor of Science in Computer Engineering (BSCPE)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="yearLevel">Year Level</label>
                        <select id="yearLevel" name="yearLevel" required>
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="status">Student Status</label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="guardianName">Parent/Guardian Name</label>
                    <input type="text" id="guardianName" name="guardianName" required>
                </div>

                <div class="form-group">
                    <label for="guardianPhone">Parent/Guardian Phone</label>
                    <input type="tel" id="guardianPhone" name="guardianPhone" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Enroll Student</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing enrollment...</p>
            </div>
        </div>

        <div id="students-tab" class="tab-content">
            <h2>Enrolled Students</h2>
            <div class="form-row" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label for="filterProgram">Search by Program</label>
                    <select id="filterProgram" onchange="searchStudents()">
                        <option value="">All Programs</option>
                        <option value="BSIS">BSIS</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIE">BSIE</option>
                        <option value="BSCPE">BSCPE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="searchStudent">Search by Student ID</label>
                    <input type="text" id="searchStudent" placeholder="Enter Student ID" onkeyup="searchStudents()">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn" onclick="clearFilters()" style="width: 100%;">Clear All</button>
                </div>
            </div>
            
            <div id="studentsContainer">
                <div class="loading" id="studentsLoading">
                    <div class="spinner"></div>
                    <p>Loading students...</p>
                </div>
            </div>
        </div>
    </div>

  
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Student</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div id="editMessage"></div>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" id="editFirstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" id="editLastName" name="lastName" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="editMiddleName">Middle Name</label>
                        <input type="text" id="editMiddleName" name="middleName">
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email Address</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="editPhone">Phone Number</label>
                        <input type="tel" id="editPhone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="editBirthdate">Date of Birth</label>
                        <input type="date" id="editBirthdate" name="birthdate" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="editAddress">Complete Address</label>
                    <textarea id="editAddress" name="address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="editProgram">Program</label>
                        <select id="editProgram" name="program" required>
                            <option value="">Select Program</option>
                            <option value="BSIS">Bachelor of Science in Information Systems (BSIS)</option>
                            <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                            <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                            <option value="BSIE">Bachelor of Science in Industrial Engineering (BSIE)</option>
                            <option value="BSCPE">Bachelor of Science in Computer Engineering (BSCPE)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editYearLevel">Year Level</label>
                        <select id="editYearLevel" name="yearLevel" required>
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                </div>

               
                <div class="form-group">
                    <label for="editStatus">Student Status</label>
                    <select id="editStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editGuardianName">Parent/Guardian Name</label>
                    <input type="text" id="editGuardianName" name="guardianName" required>
                </div>

                <div class="form-group">
                    <label for="editGuardianPhone">Parent/Guardian Phone</label>
                    <input type="tel" id="editGuardianPhone" name="guardianPhone" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Update Student</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>

            <div class="loading" id="editLoading">
                <div class="spinner"></div>
                <p>Updating student...</p>
            </div>
        </div>
    </div>

    <script>

        function toggleSidebar() {
            console.log('Toggle sidebar clicked'); 
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && overlay) {
                const isShowing = sidebar.classList.contains('show');
                
                if (isShowing) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.style.overflow = 'auto';
                } else {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                console.error('Sidebar elements not found');
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && overlay) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        // close sidebar with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        // prevent body scroll when sidebar is open
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.addEventListener('transitionend', function() {
                    if (!sidebar.classList.contains('show')) {
                        document.body.style.overflow = 'auto';
                    }
                });
            }
        });

      
        let searchTimeout;
        let allStudents = [];


        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'students') {
                loadAllStudents();
            }
        }

  
        function clearFilters() {
            const filterProgram = document.getElementById('filterProgram');
            const searchStudent = document.getElementById('searchStudent');
            
            if (filterProgram) filterProgram.value = '';
            if (searchStudent) searchStudent.value = '';
            
            displayStudents(allStudents);
        }

   
        function searchStudents() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterStudents();
            }, 300);
        }

       
        function filterStudents() {
            const filterProgram = document.getElementById('filterProgram');
            const searchStudent = document.getElementById('searchStudent');
            
            if (!filterProgram || !searchStudent) return;
            
            const programFilter = filterProgram.value.trim();
            const searchTerm = searchStudent.value.trim().toLowerCase();
            
            let filteredStudents = allStudents;
            
            if (programFilter) {
                filteredStudents = filteredStudents.filter(student => 
                    student.program === programFilter
                );
            }
            
            if (searchTerm) {
                filteredStudents = filteredStudents.filter(student => 
                    student.student_id.toLowerCase().includes(searchTerm)
                );
            }
            
            displayStudents(filteredStudents);
        }

        async function loadAllStudents() {
            const container = document.getElementById('studentsContainer');
            const loading = document.getElementById('studentsLoading');
            
            if (!container || !loading) {
                console.error('Required elements not found');
                return;
            }
            
            loading.style.display = 'block';
            
            try {
                const response = await fetch('api.php?action=students');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error('Invalid response from server.');
                }
                
                if (result.success) {
                    allStudents = result.students;
                    displayStudents(allStudents);
                } else {
                    container.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
                }
            } catch (error) {
                container.innerHTML = `<div class="alert alert-error">Error loading students: ${error.message}</div>`;
            }
            
            loading.style.display = 'none';
        }

        // display students in tablee
        function displayStudents(students) {
            const container = document.getElementById('studentsContainer');
            
            if (!container) {
                console.error('Students container not found');
                return;
            }
            
            if (students.length === 0) {
                container.innerHTML = '<p>No students found.</p>';
                return;
            }
            
            let html = `
        <div class="table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Enrollment Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
            
            students.forEach(student => {
                html += `
            <tr>
                <td>${student.student_id}</td>
                <td>${student.first_name} ${student.last_name}</td>
                <td><span class="program-badge program-${student.program.toLowerCase()}">${student.program}</span></td>
                <td>${student.year_level}</td>
                <td>${student.email}</td>
                <td>${student.phone}</td>
                <td>${new Date(student.created_at).toLocaleDateString()}</td>
                <td>${student.status.charAt(0).toUpperCase() + student.status.slice(1)}</td>
                <td>
                    <button class="btn btn-small" onclick="editStudent(${student.id})">Edit</button>
                    <button class="btn btn-small btn-danger" onclick="deleteStudent(${student.id}, '${student.student_id}')">Delete</button>
                </td>
            </tr>
        `;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        // edit student
        async function editStudent(studentId) {
            try {
                const response = await fetch(`api.php?action=get_student&id=${studentId}`);
                const result = await response.json();
                
                if (result.success) {
                    const student = result.student;
                    
              
                    document.getElementById('editId').value = student.id;
                    document.getElementById('editFirstName').value = student.first_name;
                    document.getElementById('editLastName').value = student.last_name;
                    document.getElementById('editMiddleName').value = student.middle_name || '';
                    document.getElementById('editEmail').value = student.email;
                    document.getElementById('editPhone').value = student.phone;
                    document.getElementById('editBirthdate').value = student.birthdate;
                    document.getElementById('editAddress').value = student.address;
                    document.getElementById('editProgram').value = student.program;
                    document.getElementById('editYearLevel').value = student.year_level;
                    document.getElementById('editGuardianName').value = student.guardian_name;
                    document.getElementById('editGuardianPhone').value = student.guardian_phone;
                    document.getElementById('editStatus').value = student.status; // Add this line
                    
                 
                    document.getElementById('editModal').style.display = 'block';
                    document.getElementById('editMessage').innerHTML = '';
                } else {
                    alert('Error loading student: ' + result.message);
                }
            } catch (error) {
                alert('Error loading student: ' + error.message);
            }
        }

      
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editForm').reset();
            document.getElementById('editMessage').innerHTML = '';
        }

        // delete student
        async function deleteStudent(studentId, studentIdDisplay) {
            if (!confirm(`Are you sure you want to delete student ${studentIdDisplay}?`)) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('id', studentId);
                
                const response = await fetch('api.php?action=delete_student', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    loadAllStudents(); 
                } else {
                    alert('Error deleting student: ' + result.message);
                }
            } catch (error) {
                alert('Error deleting student: ' + error.message);
            }
        }

        document.getElementById('enrollmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loading = document.getElementById('loading');
            const messageDiv = document.getElementById('message');
            
            if (!loading || !messageDiv) return;
            
            loading.style.display = 'block';
            messageDiv.innerHTML = '';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api.php?action=enroll', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error('error.');
                }
                
                if (result.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    this.reset();
                    
                    setTimeout(() => {
                        loadAllStudents();
                    }, 1000);
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="alert alert-error">Error: ${error.message}</div>`;
            }
            
            loading.style.display = 'none';
        });

        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loading = document.getElementById('editLoading');
            const messageDiv = document.getElementById('editMessage');
            
            if (!loading || !messageDiv) return;
            
            loading.style.display = 'block';
            messageDiv.innerHTML = '';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api.php?action=update_student', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    
                    setTimeout(() => {
                        closeEditModal();
                        loadAllStudents();
                    }, 1500);
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="alert alert-error">Error: ${error.message}</div>`;
            }
            
            loading.style.display = 'none';
        });

       
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                loadAllStudents();
            }, 100);
        });
    </script>
</body>
</html>
