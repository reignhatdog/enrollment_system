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
    <title>QCU Dashboard - Analytics</title>
    <link rel="icon" type="image/png" href="qcu_logo_circular.png">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a href="dashboard.php" class="active">
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
                    <h1>QCU Analytics Dashboard</h1>
                    <p>Student Enrollment Statistics</p>
                </div>
            </div>
        </div>

       
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">Total</div>
                    <div class="stat-content">
                        <h3 id="totalStudents">0</h3>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">Active</div>
                    <div class="stat-content">
                        <h3 id="activeStudents">0</h3>
                        <p>Active Students</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">Inactive</div> 
                    <div class="stat-content">
                        <h3 id="inactiveStudents">0</h3>
                        <p>Inactive Students</p> 
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">Programs</div>
                    <div class="stat-content">
                        <h3 id="totalPrograms">5</h3>
                        <p>Programs</p>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="charts-section">
            <div class="charts-grid">
            
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Students by Program</h3>
                        <p>Distribution of students across different programs</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="programChart"></canvas>
                    </div>
                </div>

             
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Students by Year Level</h3>
                        <p>Number of students in each year level</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="yearChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Student Status</h3>
                        <p>Active, Inactive, and Graduated students</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

          
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Monthly Enrollment Trend</h3>
                        <p>New enrollments over the past 12 months</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Loading dashboard data...</p>
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

        let programChart, yearChart, statusChart, trendChart;

       
        async function loadDashboardData() {
            const loading = document.getElementById('loading');
            loading.style.display = 'block';

            try {
                const response = await fetch('dashboard_api.php');
                const data = await response.json();

                if (data.success) {
                    updateStats(data.stats);
                    createCharts(data.charts);
                } else {
                    console.error('Error loading dashboard data:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }

            loading.style.display = 'none';
        }

    
        function updateStats(stats) {
            document.getElementById('totalStudents').textContent = stats.total || 0;
            document.getElementById('activeStudents').textContent = stats.active || 0;
            document.getElementById('inactiveStudents').textContent = stats.inactive || 0; // Changed to inactiveStudents
        }

   
        function createCharts(chartData) {
            createProgramChart(chartData.programs);
            createYearChart(chartData.years);
            createStatusChart(chartData.status);
            createTrendChart(chartData.trend);
        }

        function createProgramChart(data) {
            const ctx = document.getElementById('programChart').getContext('2d');
            
            programChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Year Level Bar Chart
        function createYearChart(data) {
            const ctx = document.getElementById('yearChart').getContext('2d');
            
            yearChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Number of Students',
                        data: data.values,
                        backgroundColor: '#36A2EB',
                        borderColor: '#2E86AB',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

       
        function createStatusChart(data) {
            const ctx = document.getElementById('statusChart').getContext('2d');
            
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#4CAF50',
                            '#FF9800',
                            '#2196F3'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

   
        function createTrendChart(data) {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'New Enrollments',
                        data: data.values,
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FF6384',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

      
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
