# 🔐 Google Drive OAuth 2.0 Setup - Complete Guide

## ✅ What's Ready

You now have an **OAuth 2.0 Web Application** integration with Google Drive. Your credentials file has been placed at:

```
storage/credentials/google-drive-credentials.json
```

---

## 🔄 Two-Step Authorization Process

### Step 1️⃣: **Admin Authorizes Google Drive** (One-Time)

An admin visits the authorization link to grant your application access to their Google Drive:

```
http://your-app.com/admin/settings/google-drive/authorize
```

**What happens:**
1. User is redirected to Google's OAuth consent screen
2. User grants permission to access Google Drive
3. Google redirects back to your app with an authorization code
4. Your app exchanges the code for a **refresh token**
5. Refresh token is stored in cache (persists across sessions)

---

### Step 2️⃣: **Files Auto-Upload to Google Drive**

Once authorized, all file uploads automatically sync to Google Drive:

```
User uploads → File saved locally → Auto-uploaded to Google Drive
```

The refresh token is automatically used to get access tokens for uploads.

---

## 🚀 How to Authorize

### Option 1: Direct Access (if authenticated)
Visit in your browser:
```
http://localhost:8000/admin/settings/google-drive/authorize
```

### Option 2: Command Line (for testing)
```bash
php artisan tinker
> app(App\Services\GoogleDriveService::class)->getAuthorizationUrl()
# Copy the URL and paste in browser
```

---

## 📋 Implementation Details

### GoogleDriveService Changes

**New Methods:**
- `getAuthorizationUrl()` - Returns OAuth authorization URL
- `handleAuthorizationCallback($authCode)` - Stores refresh token
- `isAuthorized()` - Checks if we have a refresh token
- All upload methods now auto-refresh tokens

**Token Storage:**
- Refresh token stored in **Laravel Cache**
- Persists across sessions
- Configured with 10-year expiration

---

## 🔀 OAuth Flow Diagram

```
┌──────────────────────────────────────────────────────┐
│ User clicks: "Authorize Google Drive"                │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ Redirect to: /admin/settings/google-drive/authorize  │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ GoogleDriveAuthController::authorize()               │
│ Generates OAuth URL & redirect                       │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ Google OAuth Consent Screen                          │
│ User reviews permissions & clicks "Allow"            │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ Google Redirects to: /auth/google/callback           │
│ With authorization code in URL: ?code=...            │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ GoogleDriveAuthController::callback()                │
│ - Gets authorization code                           │
│ - Exchanges for refresh token                        │
│ - Stores in cache                                    │
└──────────────────┬───────────────────────────────────┘
                   ↓
┌──────────────────────────────────────────────────────┐
│ User sees: "Successfully authorized!"                │
│ Redirected to: /admin/settings                       │
└──────────────────────────────────────────────────────┘
                   ↓
            ✅ Ready to Upload Files
```

---

## 📁 Files Created/Updated

| File | Purpose |
|------|---------|
| `app/Services/GoogleDriveService.php` | OAuth 2.0 implementation with token refresh |
| `app/Http/Controllers/GoogleDriveAuthController.php` | OAuth authorize & callback endpoints |
| `routes/web.php` | OAuth routes added |
| `storage/credentials/google-drive-credentials.json` | Your OAuth Web credentials |

---

## 🧪 Testing

### Test 1: Start Authorization
```bash
# Get authorization URL
php artisan tinker
> $url = app(App\Services\GoogleDriveService::class)->getAuthorizationUrl();
> echo $url;
# Copy and paste URL in browser
```

### Test 2: Check if Authorized
```bash
php artisan tinker
> app(App\Services\GoogleDriveService::class)->isAuthorized()
# Should return: true (after authorization)
#              false (before authorization)
```

### Test 3: Upload a File
```bash
php artisan tinker
> $service = app(App\Services\GoogleDriveService::class);
> $result = $service->uploadFile('path/to/file.pdf', 'test-file.pdf', 'application/pdf');
> print_r($result);
```

---

## 🔒 Security Notes

1. **Refresh Token:**
   - Stored in Laravel Cache (configured for 10 years)
   - Never exposed to user
   - Used only for server-side API calls

2. **Access Token:**
   - Generated from refresh token
   - Auto-refreshed when expired
   - Never persisted to disk

3. **Credentials File:**
   - Contains client_secret (never expose publicly)
   - Keep in `storage/` (not in public directory)
   - Add to `.gitignore`

4. **SSL Verification:**
   - Currently disabled for development
   - **MUST** be enabled in production: change `'verify' => false` to `'verify' => true`

---

## ⚙️ Configuration

### In `config/services.php`:
```php
'google_drive' => [
    'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', null),  // Optional
    'enable_upload' => env('GOOGLE_DRIVE_ENABLE', true),
],
```

### In `.env`:
```env
GOOGLE_DRIVE_ENABLE=true
GOOGLE_DRIVE_FOLDER_ID=        # Optional: specific folder to upload to
```

---

## 🎯 Next Steps

1. **Authorize Google Drive:**
   - Click "Authorize Google Drive" button in admin settings
   - Or visit: `/admin/settings/google-drive/authorize`

2. **Test Upload:**
   - Upload a file through the wizard or admin panel
   - Check if it appears in your Google Drive

3. **Verify in Logs:**
   - Check `storage/logs/laravel.log` for upload details
   - Look for: "File uploaded to Google Drive successfully"

4. **Production:**
   - Enable SSL verification: `'verify' => true`
   - Use an actual domain instead of localhost
   - Update redirect URI in Google Cloud Console

---

## 🆘 Troubleshooting

### "Not Authorized" Error
- **Cause:** Refresh token not stored
- **Fix:** Visit authorization URL first
- **Check:** `php artisan tinker` → `app(...)->isAuthorized()`

### "Access Denied" Error  
- **Cause:** User didn't grant permissions
- **Fix:** Try authorization again and click "Allow"

### "Invalid Redirect URI"
- **Cause:** Callback URL doesn't match Google Cloud settings
- **Fix:** Update redirect URI in Google Cloud Console to match your app URL

### "Credentials File Not Found"
- **Cause:** File not in correct location
- **Fix:** Place at `storage/credentials/google-drive-credentials.json`

---

## 📞 Support

Check logs for detailed error messages:
```bash
tail -f storage/logs/laravel.log
```

All Google Drive operations are logged with context:
- Success: `"File uploaded to Google Drive successfully"`
- Errors: `"Google Drive upload failed"`
- Authorization: `"Google Drive authorization successful"`

---

**Status:** ✅ Ready to Use  
**Version:** 1.0  
**Date:** March 25, 2026

Your Google Drive integration is now configured with OAuth 2.0! 🎉
