<?php
require_once 'config.php';


setHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    sendResponse(false, 'Method not allowed');
}

try {
    $pdo = getConnection();
    
  
    $sql = "SELECT * FROM students WHERE 1=1";
    $params = [];
    
   
    if (!empty($_GET['program'])) {
        $valid_programs = ['BSIS', 'BSIT', 'BSCS', 'BSIE', 'BSCPE'];
        if (in_array($_GET['program'], $valid_programs)) {
            $sql .= " AND program = ?";
            $params[] = $_GET['program'];
        }
    }
    
   
    if (!empty($_GET['search'])) {
        $search_term = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', $last_name) LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT 100"; 
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
   
    $formatted_students = [];
    foreach ($students as $student) {
        $formatted_students[] = [
            'id' => $student['id'],
            'student_id' => $student['student_id'],
            'first_name' => $student['first_name'],
            'middle_name' => $student['middle_name'],
            'last_name' => $student['last_name'],
            'email' => $student['email'],
            'phone' => $student['phone'],
            'program' => $student['program'],
            'year_level' => $student['year_level'],
            'created_at' => $student['created_at']
        ];
    }
    
    sendResponse(true, 'Students loaded successfully', [
        'students' => $formatted_students,
        'count' => count($formatted_students)
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendResponse(false, 'Database error occurred');
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendResponse(false, 'Error loading students');
}
?>
