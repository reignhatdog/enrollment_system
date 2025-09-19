<?php
require_once 'config.php';


setHeaders();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendResponse(false, 'Method not allowed');
}

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
        'guardianPhone' => 'Guardian Phone'
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
    
    // validate program
    $valid_programs = ['BSIS', 'BSIT', 'BSCS', 'BSIE', 'BSCPE'];
    if (!in_array($_POST['program'], $valid_programs)) {
        sendResponse(false, 'Invalid program selected');
    }
    
    // validate year level
    $valid_years = ['1', '2', '3', '4'];
    if (!in_array($_POST['yearLevel'], $valid_years)) {
        sendResponse(false, 'Invalid year level selected');
    }
    
    // check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    
    if ($stmt->fetch()) {
        sendResponse(false, 'Email address already registered');
    }
    
    // generate student ID
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM students WHERE YEAR(created_at) = ?");
    $stmt->execute([$year]);
    $result = $stmt->fetch();
    $count = ($result ? $result['count'] : 0) + 1;
    $student_id = $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    
    // insert student record
    $sql = "INSERT INTO students (
        student_id, first_name, middle_name, last_name, email, phone, 
        birthdate, address, program, year_level, guardian_name, guardian_phone, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
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
        trim($_POST['guardianPhone'])
    ]);
    
    if ($success) {
        sendResponse(true, "Student enrolled successfully! Student ID: $student_id", [
            'student_id' => $student_id
        ]);
    } else {
        sendResponse(false, 'Failed to enroll student');
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendResponse(false, 'Database error occurred');
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
}
?>
