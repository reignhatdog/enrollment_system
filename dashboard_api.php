<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$host = 'localhost';
$dbname = 'qcu_enrollment';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}


function getStats($pdo) {
    $stats = [];
    
    // total students
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
    $stats['total'] = $stmt->fetch()['total'];
    
    // active students
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM students WHERE status = 'active'");
    $stats['active'] = $stmt->fetch()['active'];
    
    // inactive students
    $stmt = $pdo->query("SELECT COUNT(*) as inactive FROM students WHERE status = 'inactive'");
    $stats['inactive'] = $stmt->fetch()['inactive'];
    
    return $stats;
}


function getProgramData($pdo) {
    $stmt = $pdo->query("
        SELECT program, COUNT(*) as count 
        FROM students 
        GROUP BY program 
        ORDER BY count DESC
    ");
    
    $labels = [];
    $values = [];
    
    while ($row = $stmt->fetch()) {
        $labels[] = $row['program'];
        $values[] = (int)$row['count'];
    }
    
    return ['labels' => $labels, 'values' => $values];
}

function getYearData($pdo) {
    $stmt = $pdo->query("
        SELECT year_level, COUNT(*) as count 
        FROM students 
        GROUP BY year_level 
        ORDER BY year_level
    ");
    
  
    $yearLabels = [
        '1' => '1st Year',
        '2' => '2nd Year', 
        '3' => '3rd Year',
        '4' => '4th Year'
    ];
    
    $data = [];
    while ($row = $stmt->fetch()) {
        $yearLevel = $row['year_level'];
        $data[$yearLevel] = (int)$row['count'];
    }
    
   
    $finalLabels = [];
    $finalValues = [];
    
    foreach ($yearLabels as $level => $label) {
        $finalLabels[] = $label;
        $finalValues[] = isset($data[$level]) ? $data[$level] : 0;
    }
    
    return ['labels' => $finalLabels, 'values' => $finalValues];
}

function getStatusData($pdo) {
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM students 
        WHERE status IN ('active', 'inactive')
        GROUP BY status 
        ORDER BY status DESC
    ");
    
    $labels = [];
    $values = [];
    
    while ($row = $stmt->fetch()) {
        $labels[] = ucfirst($row['status']);
        $values[] = (int)$row['count'];
    }

    $finalLabels = ['Active', 'Inactive'];
    $finalValues = [0, 0];

    foreach ($labels as $index => $label) {
        if ($label === 'Active') {
            $finalValues[0] = $values[$index];
        } elseif ($label === 'Inactive') {
            $finalValues[1] = $values[$index];
        }
    }
    
    return ['labels' => $finalLabels, 'values' => $finalValues];
}


function getTrendData($pdo) {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM students 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month
    ");
    
    $data = [];
    while ($row = $stmt->fetch()) {
        $data[$row['month']] = (int)$row['count'];
    }
    
    $labels = [];
    $values = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthName = date('M Y', strtotime("-$i months"));
        
        $labels[] = $monthName;
        $values[] = isset($data[$month]) ? $data[$month] : 0;
    }
    
    return ['labels' => $labels, 'values' => $values];
}


$response = [
    'success' => true,
    'stats' => getStats($pdo),
    'charts' => [
        'programs' => getProgramData($pdo),
        'years' => getYearData($pdo),
        'status' => getStatusData($pdo),
        'trend' => getTrendData($pdo)
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>
