# 🚀 Google Drive Integration - Quick Start

## What's Been Done

Your Laravel application now has full Google Drive integration. Here's what was implemented:

### Files Created/Modified:

1. **GoogleDriveService** (`app/Services/GoogleDriveService.php`)
   - Handles all Google Drive API interactions
   - Upload, delete, list, and create folder functionality
   - Comprehensive error handling and logging

2. **Migration** (`database/migrations/2026_03_25_add_google_drive_to_ipcrf_records.php`)
   - Adds `google_drive_file_id` column
   - Adds `google_drive_link` column
   - Tracks file references for audit and access

3. **Configuration Updates**
   - `config/services.php` - Added Google Drive service config
   - `app/Models/IpcrfRecord.php` - Added Google Drive fields to fillable
   - `app/Http/Admin/IpcrfController.php` - Integrated Google Drive uploads

4. **Credentials**
   - `storage/credentials/google-drive-credentials.json` - Your OAuth credentials

## Next Steps

### Step 1: Update Environment Variables

Add these lines to your `.env` file:

```env
# Google Drive Configuration
GOOGLE_DRIVE_ENABLE=true
GOOGLE_DRIVE_FOLDER_ID=          # Leave empty for root, or add your folder ID
```

### Step 2: Create the Folder (Optional but Recommended)

If you want files uploaded to a specific folder:

1. Go to [Google Drive](https://drive.google.com)
2. Create a new folder (e.g., "IPCRF Uploads")
3. Right-click → Share
4. Add the service account email from your credentials
5. Copy the folder ID from the URL and add to `.env`

### Step 3: Run the Migration

```bash
php artisan migrate
```

This adds the Google Drive columns to the database.

### Step 4: Test the Upload

1. Navigate to the upload form in your application
2. Fill in the form and select a file
3. Submit
4. The file should appear in your Google Drive folder within seconds

### Step 5: Verify Setup

Check your application logs:

```bash
tail -f storage/logs/laravel.log
```

You should see entries like:
```
[INFO] File uploaded to Google Drive successfully
```

## How It Works

```
User Uploads File
        ↓
Local Storage (Backup)
        ↓
Google Drive Upload (Simultaneous)
        ↓
Database Updated with Links
        ↓
Confirmation Shown to User
```

## File Management

### Viewing Uploaded Files

Files are accessible through:

1. **Google Drive Web Interface**
   - Direct link stored in database
   - Files organized by folder (if specified)

2. **Application Database**
   - Query `ipcrf_records` table
   - Access `google_drive_link` column for direct web link

### File Naming

Uploaded files follow this pattern:
```
{employee_id}_{employee_name}_{semester}_{school_year}.{ext}
```

Example:
```
2024-00123_Juan_Dela_Cruz_1st_2025-2026.pdf
```

## Troubleshooting

### Files Not Appearing in Google Drive

**Check:**
1. Is `GOOGLE_DRIVE_ENABLE=true` in `.env`?
2. Are logs showing upload success?
3. Are credentials file valid?

**Solution:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Verify credentials existence
ls -la storage/credentials/google-drive-credentials.json
```

### "Folder not found" Error

**Solution:**
1. Remove `GOOGLE_DRIVE_FOLDER_ID` from `.env`
2. Files will upload to Google Drive root
3. Once working, add the folder ID

### Clear Cache

After making env changes:
```bash
php artisan config:cache
php artisan config:clear
```

## Features Available

###📤 Upload
```php
$service = new GoogleDriveService();
$result = $service->uploadFile($filePath, $fileName, $mimeType);
```

### 📁 Create Folder
```php
$folderId = $service->createFolder('My Folder');
```

### 📋 List Files
```php
$files = $service->listFilesInFolder($folderId);
```

### 🗑️ Delete File
```php
$service->deleteFile($googleDriveFileId);
```

## Security Notes

1. **Credentials**: Never commit `storage/credentials/` to git
2. **Environment**: Never share your `.env` file
3. **Access Control**: Use Google Drive permissions to control folder access
4. **Logging**: Check logs regularly for errors

## API Response Example

When a file is uploaded, the database stores:

```json
{
  "id": 123,
  "file_name": "document.pdf",
  "google_drive_file_id": "1a2b3c4d5e6f7g",
  "google_drive_link": "https://drive.google.com/file/d/1a2b.../view",
  "uploaded_at": "2026-03-25T10:30:00Z"
}
```

## Need Help?

1. Check `GOOGLE_DRIVE_SETUP.md` for detailed documentation
2. Review application logs in `storage/logs/laravel.log`
3. Verify credentials in `storage/credentials/google-drive-credentials.json`
4. Ensure all environment variables are set correctly

---

**Status**: ✅ Integration Complete - Ready to Use
**Last Updated**: March 25, 2026
