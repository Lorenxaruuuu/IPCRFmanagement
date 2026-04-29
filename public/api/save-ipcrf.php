<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// DB CONFIG (XAMPP)
$host = 'localhost';
$dbname = 'laravel';
$username = 'root';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get JSON or form data
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }

        // Validate required fields
        if (empty($data['name']) || empty($data['province']) || empty($data['municipality'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields'
            ]);
            exit;
        }

        // Insert into ipcrf table
        $stmt = $pdo->prepare("
            INSERT INTO ipcrfs (
                name,
                province,
                municipality,
                scanned_file_name,
                scanned_file_path,
                zapier_upload_status,
                status,
                submitted_at,
                created_at,
                updated_at
            ) VALUES (
                :name,
                :province,
                :municipality,
                :scanned_file_name,
                :scanned_file_path,
                :zapier_upload_status,
                :status,
                :submitted_at,
                NOW(),
                NOW()
            )
        ");

        $stmt->execute([
            ':name' => $data['name'],
            ':province' => $data['province'],
            ':municipality' => $data['municipality'],
            ':scanned_file_name' => $data['scanned_file_name'] ?? null,
            ':scanned_file_path' => null,
            ':zapier_upload_status' => 'success',
            ':status' => 'Sent to Zapier',
            ':submitted_at' => date('Y-m-d H:i:s')
        ]);

        $id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'id' => $id,
            'message' => 'IPCRF record created successfully'
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests allowed'
    ]);
}
?>
