<?php

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the service
$service = app(\App\Services\GoogleDriveService::class);

// Test upload
echo "Testing Google Drive upload...\n";
$result = $service->uploadFile(
    __DIR__ . '/test-upload.txt',
    'test-oauth-upload-' . date('Y-m-d-His') . '.txt',
    'text/plain'
);

echo "\n=== Upload Result ===\n";
echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
if ($result['success']) {
    echo "File ID: " . $result['file_id'] . "\n";
    echo "Web Link: " . $result['web_link'] . "\n";
    echo "File Name: " . $result['file_name'] . "\n";
} else {
    echo "Error: " . $result['error'] . "\n";
}
