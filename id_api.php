<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'qcu_enrollment');

// Set headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Function to send JSON response
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

// Create database connection
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

// Get the action parameter
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_id') {
    try {
        $pdo = getConnection();

        // Validate required fields
        $required_fields = [
            'studentNumber' => 'Student Number',
            'fullName' => 'Full Name',
            'programYearLevel' => 'Program / Year Level'
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

        $studentNumber = trim($_POST['studentNumber']);
        $fullName = trim($_POST['fullName']);
        $programYearLevel = trim($_POST['programYearLevel']);

        // Check if student number already exists
        $stmt = $pdo->prepare("SELECT id FROM id_records WHERE student_number = ?");
        $stmt->execute([$studentNumber]);
        if ($stmt->fetch()) {
            sendResponse(false, 'Student Number already exists.');
        }

        // Handle ID Photo upload
        $idPhotoPath = '';
        if (isset($_FILES['idPhoto']) && $_FILES['idPhoto']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['idPhoto']['tmp_name'];
            $fileName = $_FILES['idPhoto']['name'];
            $fileSize = $_FILES['idPhoto']['size'];
            $fileType = $_FILES['idPhoto']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'png', 'jpeg'];
            if (!in_array($fileExtension, $allowedfileExtensions)) {
                sendResponse(false, 'Invalid file type. Only JPG and PNG are allowed.');
            }

            if ($fileSize > 2 * 1024 * 1024) { // 2MB max size
                sendResponse(false, 'File size exceeds 2MB limit.');
            }

            $uploadFileDir = 'uploads/id_photos/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Create directory if it doesn't exist
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)) {
                $idPhotoPath = $destPath;
            } else {
                sendResponse(false, 'Failed to upload ID photo.');
            }
        } else {
            sendResponse(false, 'ID Photo is required.');
        }

        // Insert into id_records table
        $sql = "INSERT INTO id_records (student_number, full_name, program_year_level, id_photo_path) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$studentNumber, $fullName, $programYearLevel, $idPhotoPath]);

        if ($success) {
            sendResponse(true, 'ID record created successfully!');
        } else {
            // If photo was uploaded but DB insert failed, try to delete the photo
            if (!empty($idPhotoPath) && file_exists($idPhotoPath)) {
                unlink($idPhotoPath);
            }
            sendResponse(false, 'Failed to create ID record.');
        }

    } catch (Exception $e) {
        sendResponse(false, 'Error creating ID record: ' . $e->getMessage());
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_id_records') {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT * FROM id_records ORDER BY created_at DESC");
        $records = $stmt->fetchAll();
        sendResponse(true, 'ID records loaded successfully', ['records' => $records]);
    } catch (Exception $e) {
        sendResponse(false, 'Error loading ID records: ' . $e->getMessage());
    }
} else {
    sendResponse(false, 'Invalid action or request method.');
}
?>
