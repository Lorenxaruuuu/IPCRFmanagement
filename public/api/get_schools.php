<?php

/**
 * GET Schools by Municipality
 * POST /api/get_schools.php
 */

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

    // Read JSON request body
    $input = json_decode(file_get_contents('php://input'), true);
    $municipalityId = $input['municipality_id'] ?? null;

    if (!$municipalityId) {
        echo json_encode([
            'success' => false,
            'message' => 'municipality_id missing'
        ]);
        exit;
    }

    // Fetch schools for the given municipality
    $stmt = $pdo->prepare(
        "SELECT id, name
         FROM schools
         WHERE municipality_id = ?
         ORDER BY name ASC"
    );
    $stmt->execute([$municipalityId]);
    $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $schools
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
