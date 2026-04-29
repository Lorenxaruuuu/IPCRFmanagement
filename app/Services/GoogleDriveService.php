<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleDriveService
{
    private $folderId;
    private $credentialsPath;
    private $tokenPath;

    public function __construct()
    {
        try {
            // Set credential and token paths
            $this->credentialsPath = storage_path('credentials/google-drive-credentials.json');
            $this->tokenPath = storage_path('app/google-drive-token.json');
            
            if (!file_exists($this->credentialsPath)) {
                throw new Exception("Google Drive credentials file not found at: {$this->credentialsPath}");
            }
            
            // Set the folder ID where files will be uploaded (optional)
            $this->folderId = config('services.google_drive.folder_id');
            
            Log::info('Google Drive Service initialized');
            
        } catch (Exception $e) {
            Log::error('Google Drive Service initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a configured Google Client instance
     * 
     * @return Google_Client
     */
    private function getClient()
    {
        $client = new Google_Client();
        $client->setAuthConfig($this->credentialsPath);
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setRedirectUri('http://localhost:8000/auth/google/callback');
        
        // 🔥 CRITICAL: These are REQUIRED to get refresh token
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        
        // Disable SSL verification for development
        $client->setHttpClient(new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 60
        ]));
        
        return $client;
    }

    /**
     * Get the OAuth authorization URL for user to grant access
     * 
     * @return string - Authorization URL
     */
    public function getAuthorizationUrl()
    {
        $client = $this->getClient();
        return $client->createAuthUrl();
    }

    /**
     * Handle OAuth callback and store token to file
     * 
     * @param string $authCode - Authorization code from OAuth callback
     * @return bool
     */
    public function handleAuthorizationCallback($authCode)
    {
        try {
            $client = $this->getClient();
            
            // 🔥 Exchange authorization code for token
            $token = $client->fetchAccessTokenWithAuthCode($authCode);
            
            if (isset($token['error'])) {
                throw new Exception($token['error_description'] ?? 'OAuth error');
            }
            
            // 🔥 VERY IMPORTANT: Save FULL token to file
            file_put_contents($this->tokenPath, json_encode($token));
            
            Log::info('Google Drive authorization successful, token stored');
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to handle Google Drive authorization: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if we have a valid authorization (token file exists)
     * 
     * @return bool
     */
    public function isAuthorized()
    {
        return file_exists($this->tokenPath);
    }

    /**
     * Load and prepare token, handling expiration
     * 
     * @return Google_Client - Authenticated client ready for API calls
     */
    private function prepareClient()
    {
        $client = $this->getClient();
        
        // Check if authorized (token file exists)
        if (!file_exists($this->tokenPath)) {
            throw new Exception('Google Drive not authorized. Please complete OAuth authorization first.');
        }
        
        // Load token from file
        $accessToken = json_decode(file_get_contents($this->tokenPath), true);
        $client->setAccessToken($accessToken);
        
        // 🔄 Handle token expiration
        if ($client->isAccessTokenExpired()) {
            Log::info('Access token expired, refreshing...');
            
            $refreshToken = $client->getRefreshToken();
            if (!$refreshToken) {
                throw new Exception('No refresh token available. Please re-authorize Google Drive.');
            }
            
            // Get new token using refresh token
            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            
            if (isset($newToken['error'])) {
                throw new Exception('Failed to refresh token: ' . ($newToken['error_description'] ?? $newToken['error']));
            }
            
            // Ensure refresh token is preserved
            $newToken['refresh_token'] = $refreshToken;
            
            // Save updated token back to file
            file_put_contents($this->tokenPath, json_encode($newToken));
            
            // Set the new token on the client
            $client->setAccessToken($newToken);
            
            Log::info('Access token refreshed and saved');
        }
        
        return $client;
    }

    /**
     * Upload a file to Google Drive
     * 
     * @param string $filePath - Path to the file to upload
     * @param string $fileName - Name for the file in Google Drive
     * @param string $mimeType - MIME type of the file
     * @return array - Contains 'success', 'file_id', 'web_link' and optional 'error'
     */
    public function uploadFile($filePath, $fileName, $mimeType = 'application/octet-stream')
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("File not found at path: {$filePath}");
            }

            // 🔥 Prepare client with token and handle expiration
            $client = $this->prepareClient();
            $service = new Google_Service_Drive($client);

            // Prepare file metadata
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'description' => 'IPCRF uploaded from Laravel application'
            ]);

            // Add to specific folder if folder ID is set
            if ($this->folderId) {
                $fileMetadata->setParents([$this->folderId]);
            }

            // Read file content
            $content = file_get_contents($filePath);

            Log::info('Starting Google Drive upload', [
                'file_name' => $fileName,
                'file_size' => strlen($content),
                'mime_type' => $mimeType
            ]);

            // Upload file
            $file = $service->files->create(
                $fileMetadata,
                [
                    'data' => $content,
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id, webViewLink, name'
                ]
            );

            Log::info('File uploaded to Google Drive successfully', [
                'file_id' => $file->id,
                'file_name' => $fileName,
                'web_link' => $file->webViewLink
            ]);

            return [
                'success' => true,
                'file_id' => $file->id,
                'web_link' => $file->webViewLink,
                'file_name' => $file->name
            ];

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            
            Log::error('Google Drive upload failed', [
                'file_name' => $fileName,
                'file_path' => $filePath,
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }

    /**
     * Delete a file from Google Drive
     * 
     * @param string $fileId - Google Drive file ID
     * @return bool
     */
    public function deleteFile($fileId)
    {
        try {
            $client = $this->prepareClient();
            $service = new Google_Service_Drive($client);
            
            $service->files->delete($fileId);
            Log::info('File deleted from Google Drive', ['file_id' => $fileId]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete file from Google Drive: ' . $e->getMessage(), [
                'file_id' => $fileId
            ]);
            return false;
        }
    }

    /**
     * Get file information from Google Drive
     * 
     * @param string $fileId - Google Drive file ID
     * @return array|null
     */
    public function getFile($fileId)
    {
        try {
            $client = $this->prepareClient();
            $service = new Google_Service_Drive($client);
            
            $file = $service->files->get($fileId, [
                'fields' => 'id, name, mimeType, createdTime, modifiedTime, size, webViewLink'
            ]);

            return [
                'id' => $file->id,
                'name' => $file->name,
                'mime_type' => $file->mimeType,
                'created_time' => $file->createdTime,
                'modified_time' => $file->modifiedTime,
                'size' => $file->size,
                'web_link' => $file->webViewLink
            ];
        } catch (Exception $e) {
            Log::error('Failed to get file from Google Drive: ' . $e->getMessage(), [
                'file_id' => $fileId
            ]);
            return null;
        }
    }

    /**
     * Create a folder in Google Drive
     * 
     * @param string $folderName - Name of the folder
     * @param string $parentFolderId - Optional parent folder ID
     * @return string|null - Folder ID if successful, null otherwise
     */
    public function createFolder($folderName, $parentFolderId = null)
    {
        try {
            $client = $this->prepareClient();
            $service = new Google_Service_Drive($client);
            
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder'
            ]);

            if ($parentFolderId) {
                $fileMetadata->setParents([$parentFolderId]);
            }

            $folder = $service->files->create($fileMetadata, [
                'fields' => 'id'
            ]);

            Log::info('Folder created in Google Drive', [
                'folder_id' => $folder->id,
                'folder_name' => $folderName
            ]);

            return $folder->id;
        } catch (Exception $e) {
            Log::error('Failed to create folder in Google Drive: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * List files in a folder
     * 
     * @param string $folderId - Folder ID
     * @param int $pageSize - Number of files to return
     * @return array|null
     */
    public function listFilesInFolder($folderId, $pageSize = 10)
    {
        try {
            $client = $this->prepareClient();
            $service = new Google_Service_Drive($client);
            
            $results = $service->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed=false",
                'spaces' => 'drive',
                'pageSize' => $pageSize,
                'fields' => 'files(id, name, mimeType, createdTime)'
            ]);

            return $results->getFiles();
        } catch (Exception $e) {
            Log::error('Failed to list files from Google Drive: ' . $e->getMessage());
            return null;
        }
    }
}
