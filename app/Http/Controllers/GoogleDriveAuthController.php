<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleDriveAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen
     */
    public function authorize(GoogleDriveService $googleDriveService)
    {
        try {
            $authUrl = $googleDriveService->getAuthorizationUrl();
            return redirect()->away($authUrl);
        } catch (\Exception $e) {
            Log::error('Google Drive authorization failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to initiate Google Drive authorization: ' . $e->getMessage());
        }
    }

    /**
     * Handle OAuth callback from Google
     */
    public function callback(Request $request, GoogleDriveService $googleDriveService)
    {
        try {
            // Check for errors from Google
            if ($request->has('error')) {
                $error = $request->get('error');
                Log::error('Google OAuth error: ' . $error);
                return redirect('/admin/settings?google_error=' . urlencode($error))
                    ->with('error', 'Google authorization failed: ' . $error);
            }

            // Get authorization code
            $authCode = $request->get('code');
            if (!$authCode) {
                return redirect('/admin/settings')
                    ->with('error', 'No authorization code received from Google');
            }

            // Handle the callback and store refresh token
            if ($googleDriveService->handleAuthorizationCallback($authCode)) {
                Log::info('Google Drive authorization successful');
                return redirect('/admin/settings')
                    ->with('success', 'Google Drive authorized successfully! You can now upload files.');
            } else {
                return redirect('/admin/settings')
                    ->with('error', 'Failed to authorize Google Drive');
            }

        } catch (\Exception $e) {
            Log::error('Google Drive callback error: ' . $e->getMessage());
            return redirect('/admin/settings')
                ->with('error', 'Authorization error: ' . $e->getMessage());
        }
    }
}
