<?php

error_reporting(0); 
ini_set('display_errors', 0);


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '');


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}


function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response);
    exit;
}

function getConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        sendResponse(false, 'Database connection failed');
    }
}


$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'enroll') {

    try {
        $pdo = getConnection();
        
      
        $required_fields = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name', 
            'email' => 'Email',
            'phone' => 'Phone',
            'birthdate' => 'Birth Date',
            'address' => 'Address',
            'program' => 'Program',
            'yearLevel' => 'Year Level',
            'guardianName' => 'Guardian Name',
            'guardianPhone' => 'Guardian Phone',
            'status' => 'Status' 
        ];
        
        $errors = [];
        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = "$label is required";
            }
        }
        
        if (!empty($errors)) {
            sendResponse(false, implode(', ', $errors));
        }
        
   
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Invalid email format');
        }
 
        $valid_programs = ['BSIS', 'BSIT', 'BSCS', 'BSIE', 'BSCPE'];
        if (!in_array($_POST['program'], $valid_programs)) {
            sendResponse(false, 'Invalid program selected');
        }

   
        $valid_statuses = ['active', 'inactive'];
        if (!in_array($_POST['status'], $valid_statuses)) {
            sendResponse(false, 'Invalid status selected');
        }
        
     
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        
        if ($stmt->fetch()) {
            sendResponse(false, 'Email address already registered');
        }
        
       
        $year = date('Y');
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM students WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $result = $stmt->fetch();
        $count = ($result ? $result['count'] : 0) + 1;
        $student_id = $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        
        $sql = "INSERT INTO students (
            student_id, first_name, middle_name, last_name, email, phone, 
            birthdate, address, program, year_level, guardian_name, guardian_phone, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $student_id,
            trim($_POST['firstName']),
            trim($_POST['middleName'] ?? ''),
            trim($_POST['lastName']),
            trim($_POST['email']),
            trim($_POST['phone']),
            $_POST['birthdate'],
            trim($_POST['address']),
            $_POST['program'],
            $_POST['yearLevel'],
            trim($_POST['guardianName']),
            trim($_POST['guardianPhone']),
            trim($_POST['status']) 
        ]);
        
        if ($success) {
            sendResponse(true, "Student enrolled successfully! Student ID: $student_id", [
                'student_id' => $student_id
            ]);
        } else {
            sendResponse(false, 'Failed to enroll student');
        }
        
    } catch (Exception $e) {
        sendResponse(false, 'Enrollment error occurred');
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'students') {

    try {
        $pdo = getConnection();
        
        $sql = "SELECT 
            id, student_id, first_name, middle_name, last_name, 
            email, phone, birthdate, address, program, year_level, 
            guardian_name, guardian_phone, status, created_at, updated_at
        FROM students WHERE 1=1";
        $params = [];
        
     
        if (!empty($_GET['program']) && trim($_GET['program']) !== '') {
            $program = trim($_GET['program']);
            $valid_programs = ['BSIS', 'BSIT', 'BSCS', 'BSIE', 'BSCPE'];
            if (in_array($program, $valid_programs)) {
                $sql .= " AND program = ?";
                $params[] = $program;
            }
        }
        

        if (!empty($_GET['search']) && trim($_GET['search']) !== '') {
            $search_term = '%' . trim($_GET['search']) . '%';
            $sql .= " AND student_id LIKE ?";
            $params[] = $search_term;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT 100";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();
        
        sendResponse(true, 'Students loaded successfully', [
            'students' => $students,
            'count' => count($students),
            'filters_applied' => [
                'program' => $_GET['program'] ?? '',
                'search' => $_GET['search'] ?? ''
            ]
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Error loading students: ' . $e->getMessage());
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_student') {

    try {
        $pdo = getConnection();
        
        if (empty($_GET['id'])) {
            sendResponse(false, 'Student ID is required');
        }
        
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $student = $stmt->fetch();
        
        if (!$student) {
            sendResponse(false, 'Student not found');
        }
        
        sendResponse(true, 'Student loaded successfully', [
            'student' => $student
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Error loading student: ' . $e->getMessage());
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_student') {

    try {
        $pdo = getConnection();
        
        if (empty($_POST['id'])) {
            sendResponse(false, 'Student ID is required');
        }
       
        $required_fields = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name', 
            'email' => 'Email',
            'phone' => 'Phone',
            'birthdate' => 'Birth Date',
            'address' => 'Address',
            'program' => 'Program',
            'yearLevel' => 'Year Level',
            'guardianName' => 'Guardian Name',
            'guardianPhone' => 'Guardian Phone',
            'status' => 'Status'
        ];
        
        $errors = [];
        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = "$label is required";
            }
        }
        
        if (!empty($errors)) {
            sendResponse(false, implode(', ', $errors));
        }
        
        // validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Invalid email format');
        }
        
        // program
        $valid_programs = ['BSIS', 'BSIT', 'BSCS', 'BSIE', 'BSCPE'];
        if (!in_array($_POST['program'], $valid_programs)) {
            sendResponse(false, 'Invalid program selected');
        }

        // active and inactive status
        $valid_statuses = ['active', 'inactive'];
        if (!in_array($_POST['status'], $valid_statuses)) {
            sendResponse(false, 'Invalid status selected');
        }
        
        // check if email already exists for other students
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
        $stmt->execute([$_POST['email'], $_POST['id']]);
        
        if ($stmt->fetch()) {
            sendResponse(false, 'Email address already registered to another student');
        }
        
        // update the student record
        $sql = "UPDATE students SET 
            first_name = ?, middle_name = ?, last_name = ?, email = ?, phone = ?, 
            birthdate = ?, address = ?, program = ?, year_level = ?, 
            guardian_name = ?, guardian_phone = ?, status = ?, updated_at = NOW()
        WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            trim($_POST['firstName']),
            trim($_POST['middleName'] ?? ''),
            trim($_POST['lastName']),
            trim($_POST['email']),
            trim($_POST['phone']),
            $_POST['birthdate'],
            trim($_POST['address']),
            $_POST['program'],
            $_POST['yearLevel'],
            trim($_POST['guardianName']),
            trim($_POST['guardianPhone']),
            trim($_POST['status']), 
            $_POST['id']
        ]);
        
        if ($success) {
            sendResponse(true, "Student updated successfully!");
        } else {
            sendResponse(false, 'Failed to update student');
        }
        
    } catch (Exception $e) {
        sendResponse(false, 'Update error occurred');
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_student') {

    try {
        $pdo = getConnection();
        
        if (empty($_POST['id'])) {
            sendResponse(false, 'Student ID is required');
        }
        
  
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $student = $stmt->fetch();
        
        if (!$student) {
            sendResponse(false, 'Student not found');
        }
        
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $success = $stmt->execute([$_POST['id']]);
        
        if ($success) {
            sendResponse(true, "Student deleted successfully!");
        } else {
            sendResponse(false, 'Failed to delete student');
        }
        
    } catch (Exception $e) {
        sendResponse(false, 'Delete error occurred');
    }

} else {
   
    sendResponse(false, "Invalid request");
}
?>
