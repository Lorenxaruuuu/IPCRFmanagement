<?php

/**
 * IPCRF Upload API Endpoint
 * Handles file uploads and data saving directly with PDO
 * POST /api/upload.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// DB CONFIG (XAMPP)
$host = 'localhost';
$dbname = 'laravel';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate required fields
    $required = ['role', 'employee_name', 'employee_id', 'province_id', 'municipality_id', 'school_id', 'semester', 'school_year'];
    foreach ($required as $field) {
        if (empty($_POST[$field] ?? null)) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Validate file upload
    if (empty($_FILES['file'] ?? null)) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    $allowedExtensions = ['pdf', 'xlsx', 'xls'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExtensions)) {
        throw new Exception("File type not allowed. Allowed: " . implode(', ', $allowedExtensions));
    }

    if ($file['size'] > 10485760) { // 10MB
        throw new Exception('File size exceeds 10MB limit');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }

    // Validate foreign keys exist
    $stmt = $pdo->prepare("SELECT id FROM provinces WHERE id = ?");
    $stmt->execute([$_POST['province_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid province selected');
    }

    $stmt = $pdo->prepare("SELECT id FROM municipalities WHERE id = ? AND province_id = ?");
    $stmt->execute([$_POST['municipality_id'], $_POST['province_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid municipality selected');
    }

    $stmt = $pdo->prepare("SELECT id FROM schools WHERE id = ? AND municipality_id = ?");
    $stmt->execute([$_POST['school_id'], $_POST['municipality_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid school selected');
    }

    // check for existing similar record to prevent duplicates
    $dupStmt = $pdo->prepare(
        "SELECT id FROM ipcrf_uploads
         WHERE employee_id = ? AND role = ?
           AND province_id = ? AND municipality_id = ?
           AND school_id = ? AND semester = ? AND school_year = ?
         LIMIT 1"
    );
    $dupStmt->execute([
        $_POST['employee_id'], $_POST['role'],
        $_POST['province_id'], $_POST['municipality_id'],
        $_POST['school_id'], $_POST['semester'], $_POST['school_year']
    ]);
    if ($dupStmt->fetch()) {
        throw new Exception('A record for this employee and period already exists');
    }

    // Create storage directory if not exists
    $storagePath = __DIR__ . '/../../storage/app/private/ipcrf';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }

    // Generate unique filename
    $fileName = uniqid('ipcrf_') . '.' . $fileExt;
    $filePath = $storagePath . '/' . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to save file');
    }

    // Save to database
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare(
        "INSERT INTO ipcrf_uploads (
            employee_name, employee_id, role, province_id, 
            municipality_id, school_id, semester, school_year, 
            file_path, status, uploaded_at, created_at, updated_at
        ) VALUES (
            :employee_name, :employee_id, :role, :province_id,
            :municipality_id, :school_id, :semester, :school_year,
            :file_path, :status, :uploaded_at, :created_at, :updated_at
        )"
    );

    $result = $stmt->execute([
        ':employee_name' => $_POST['employee_name'],
        ':employee_id' => $_POST['employee_id'],
        ':role' => $_POST['role'],
        ':province_id' => $_POST['province_id'],
        ':municipality_id' => $_POST['municipality_id'],
        ':school_id' => $_POST['school_id'],
        ':semester' => $_POST['semester'],
        ':school_year' => $_POST['school_year'],
        ':file_path' => 'ipcrf/' . $fileName,
        ':status' => 'uploaded',
        ':uploaded_at' => $now,
        ':created_at' => $now,
        ':updated_at' => $now
    ]);

    if (!$result) {
        // Delete uploaded file if database insert fails
        unlink($filePath);
        throw new Exception('Failed to save record to database');
    }

    $insertId = $pdo->lastInsertId();

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'IPCRF uploaded successfully',
        'data' => [
            'id' => $insertId,
            'employee_name' => $_POST['employee_name'],
            'file_name' => $fileName,
            'uploaded_at' => $now
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);

    // Clean up file if there was an error
    if (isset($filePath) && file_exists($filePath)) {
        @unlink($filePath);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);

    // Clean up file if there was an error
    if (isset($filePath) && file_exists($filePath)) {
        @unlink($filePath);
    }
}
?>
