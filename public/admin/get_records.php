<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


// DB CONFIG (XAMPP)
$host = 'localhost';
$dbname = 'laravel';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch ipcrf_uploads records
    $stmt = $pdo->prepare("
        SELECT 
            id,
            employee_name,
            employee_id,
            role,
            province_id,
            municipality_id,
            school_id,
            semester,
            school_year,
            file_path,
            status,
            uploaded_at,
            created_at,
            updated_at
        FROM ipcrf_uploads
        ORDER BY created_at DESC
    ");

    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $records
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
