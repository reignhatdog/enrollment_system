<?php
session_start();

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
    <title>QCU Admin - Create ID</title>
    <link rel="icon" type="image/png" href="qcu_logo_circular.png">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="id_creation.css">
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
                        <a href="id_creation.php" class="active">
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
                    <h1>QCU ID Creation</h1>
                    <p>Manually create student ID records</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Manual ID Creation Form</h2>
            <div id="message" class="alert hidden"></div>
            
            <form id="idCreationForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="studentNumber">Student Number</label>
                    <input type="text" id="studentNumber" name="studentNumber" required>
                </div>
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="programYearLevel">Program / Year Level</label>
                    <input type="text" id="programYearLevel" name="programYearLevel" placeholder="" required>
                </div>
                <div class="form-group">
                    <label for="idPhoto">ID Photo</label>
                    <input type="file" id="idPhoto" name="idPhoto" accept="image/jpeg, image/png" required>
                    <div class="photo-preview" id="photoPreview"></div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Create ID Record</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Creating ID record...</p>
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

      
        document.getElementById('idPhoto').addEventListener('change', function(event) {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = ''; // Clear previous preview
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'ID Photo Preview';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

 
        document.getElementById('idCreationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loading = document.getElementById('loading');
            const messageDiv = document.getElementById('message');
            
            loading.style.display = 'block';
            messageDiv.classList.add('hidden');
            messageDiv.textContent = '';
            messageDiv.classList.remove('alert-success', 'alert-error');
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('id_api.php?action=create_id', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageDiv.classList.remove('hidden');
                    messageDiv.classList.add('alert-success');
                    messageDiv.textContent = result.message;
                    this.reset();
                    document.getElementById('photoPreview').innerHTML = ''; // Clear photo preview
                } else {
                    messageDiv.classList.remove('hidden');
                    messageDiv.classList.add('alert-error');
                    messageDiv.textContent = result.message;
                }
            } catch (error) {
                messageDiv.classList.remove('hidden');
                messageDiv.classList.add('alert-error');
                messageDiv.textContent = 'An unexpected error occurred: ' + error.message;
            }
            
            loading.style.display = 'none';
        });
    </script>
</body>
</html>
