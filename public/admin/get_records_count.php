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

    // Get count of ipcrf_uploads records
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_records
        FROM ipcrf_uploads
    ");

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => (int)$result['total_records']
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
