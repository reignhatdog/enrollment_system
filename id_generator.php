<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCU Admin - ID Layout Generator</title>
    <link rel="icon" type="image/png" href="qcu_logo_circular.png">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="id_generator.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
                        <a href="main.php">
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
                        <a href="id_generator.php" class="active">
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
                    <h1>QCU ID Layout Generator</h1>
                    <p>Generate and preview student ID cards</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <div class="generator-controls">
                <div class="form-group">
                    <label for="selectIdRecord">Select Existing ID Record:</label>
                    <select id="selectIdRecord" onchange="loadIdRecord()">
                        <option value="">-- Select an ID --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genStudentNumber">Student Number</label>
                    <input type="text" id="genStudentNumber" onkeyup="updateIdPreview()">
                </div>
                <div class="form-group">
                    <label for="genFullName">Full Name</label>
                    <input type="text" id="genFullName" onkeyup="updateIdPreview()">
                </div>
                <div class="form-group">
                    <label for="genProgramYearLevel">Program / Year Level</label>
                    <input type="text" id="genProgramYearLevel" placeholder="" onkeyup="updateIdPreview()">
                </div>
                
                <div class="form-group">
                    <label for="uploadIdPhoto">Upload ID Photo</label>
                    <input type="file" id="uploadIdPhoto" accept="image/jpeg, image/png" onchange="handlePhotoUpload(event)">
                   
                </div>
                <div class="button-group">
                    <button class="btn" onclick="downloadIdCard()">Download ID Card</button>
                    <button class="btn btn-secondary" onclick="printIdCard()">Print ID Card</button>
                </div>
            </div>

            <div class="id-preview-area">
                <h3>ID Card Preview</h3>
                <br>
                <div class="id-card" id="idCardPreview">
                 
                    <div class="id-header">
                        <img src="logo.jpg" alt="QCU Logo" class="id-logo">
                        <div class="id-header-text">
                            <h4>Quezon City University</h4>
                            <p>Student ID Card</p>
                        </div>
                    </div>
                    
                    <div class="id-photo-container">
                        <img src="/placeholder.svg?height=150&width=150" alt="Student Photo" class="id-photo" id="previewPhoto">
                    </div>
                    
                   
                    <div class="id-details">
                        <p class="id-name" id="previewFullName">FULL NAME</p>
                        <p class="id-program" id="previewProgramYearLevel">PROGRAM / YEAR LEVEL</p>
                        <p class="id-student-number">Student No.: <span id="previewStudentNumber">XXXX-XXXX</span></p>
                        <p class="id-website">www.qcu.edu.ph</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
   
        function toggleSidebar() {
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

     
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        let allIdRecords = [];


        async function loadIdRecordsDropdown() {
            try {
                const response = await fetch('id_api.php?action=get_id_records');
                const result = await response.json();

                if (result.success) {
                    allIdRecords = result.records;
                    const select = document.getElementById('selectIdRecord');
                    select.innerHTML = '<option value="">-- Select an ID --</option>'; // Reset options
                    allIdRecords.forEach(record => {
                        const option = document.createElement('option');
                        option.value = record.id;
                        option.textContent = `${record.student_number} - ${record.full_name}`;
                        select.appendChild(option);
                    });
                } else {
                    console.error('Error loading ID records:', result.message);
                }
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        function loadIdRecord() {
            const selectedId = document.getElementById('selectIdRecord').value;
            const record = allIdRecords.find(r => r.id == selectedId);
            const previewPhoto = document.getElementById('previewPhoto');
            const uploadIdPhotoInput = document.getElementById('uploadIdPhoto');

            if (record) {
                document.getElementById('genStudentNumber').value = record.student_number;
                document.getElementById('genFullName').value = record.full_name;
                document.getElementById('genProgramYearLevel').value = record.program_year_level;
                
              
                previewPhoto.src = record.id_photo_path;
                console.log('Attempting to load photo from path:', record.id_photo_path);
                
                uploadIdPhotoInput.value = ''; 

            } else {
             
                document.getElementById('genStudentNumber').value = '';
                document.getElementById('genFullName').value = '';
                document.getElementById('genProgramYearLevel').value = '';
                
                previewPhoto.src = '/placeholder.svg?height=150&width=150';
                console.log('Resetting photo to placeholder.');
                uploadIdPhotoInput.value = ''; 
            }
            updateIdPreview(); 
        }

    
        function handlePhotoUpload(event) {
            const file = event.target.files[0];
            const previewPhoto = document.getElementById('previewPhoto');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewPhoto.src = e.target.result;
                    console.log('Uploaded photo preview loaded successfully');
                };
                reader.readAsDataURL(file);
            }
        }

        // Update ID card preview dynamically (only text fields)
        function updateIdPreview() {
            document.getElementById('previewStudentNumber').textContent = document.getElementById('genStudentNumber').value || 'XXXX-XXXX';
            document.getElementById('previewFullName').textContent = document.getElementById('genFullName').value || 'FULL NAME';
            document.getElementById('previewProgramYearLevel').textContent = document.getElementById('genProgramYearLevel').value || 'PROGRAM / YEAR LEVEL';
        }

       
        function downloadIdCard() {
            const idCard = document.getElementById('idCardPreview');
            
         
            html2canvas(idCard, { 
                scale: 3, 
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                width: idCard.offsetWidth,
                height: idCard.offsetHeight
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'student_id_card.png';
                link.href = canvas.toDataURL('image/png', 1.0); // max quality
                link.click();
            }).catch(error => {
                console.error('Error generating ID card:', error);
                alert('Error generating ID card. Please try again.');
            });
        }


        function printIdCard() {
            const idCard = document.getElementById('idCardPreview');
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print ID Card</title>');
            printWindow.document.write('<link rel="stylesheet" href="id_generator.css">');
            printWindow.document.write('<style>@media print { body { margin: 0; padding: 20px; } .id-card { box-shadow: none; border: 1px solid #ccc; page-break-inside: avoid; } }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(idCard.outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

   
        document.addEventListener('DOMContentLoaded', function() {
            loadIdRecordsDropdown();
            updateIdPreview();
        });
    </script>
</body>
</html>
