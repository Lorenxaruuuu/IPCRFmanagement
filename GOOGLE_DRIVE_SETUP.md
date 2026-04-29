# Google Drive Integration Setup Guide

## Overview
This application now supports automatic uploading of IPCRF files to Google Drive. Files are stored both locally (for backup) and on Google Drive (for easy access and sharing).

## Prerequisites
- Google Cloud Project with Google Drive API enabled
- OAuth 2.0 credentials (Client Secret JSON file)
- A Google Drive folder (optional, but recommended)

## Setup Steps

### 1. Add Environment Variables

Add the following to your `.env` file:

```env
# Google Drive Configuration
GOOGLE_DRIVE_ENABLE=true
GOOGLE_DRIVE_FOLDER_ID=your_google_drive_folder_id_here
```

**Note:** The `GOOGLE_DRIVE_FOLDER_ID` is optional. If not set, files will be uploaded to the root directory of your Google Drive.

### 2. Credentials File

The application looks for credentials at:
```
storage/credentials/google-drive-credentials.json
```

The credentials file has been placed in the correct location with your provided credentials.

### 3. Database Migration

Run the migration to add Google Drive fields to the database:

```bash
php artisan migrate
```

This adds two new columns to the `ipcrf_records` table:
- `google_drive_file_id` - Stores the Google Drive file ID
- `google_drive_link` - Stores the shareable Google Drive link

### 4. File Storage Configuration

Ensure that your storage path is correctly configured in `config/filesystems.php`. The application uses the 'private' disk for file storage.

## How It Works

When a user uploads an IPCRF file:

1. **Local Storage**: File is first stored locally at `storage/app/private/ipcrf_records/`
2. **Google Drive Upload**: If enabled, the file is simultaneously uploaded to Google Drive
3. **Database Records**: Both local path and Google Drive file ID/link are stored in the database

## Configuration Options

### Disabling Google Drive Upload

If you need to disable Google Drive uploads for testing:

```env
GOOGLE_DRIVE_ENABLE=false
```

### Setting a Default Folder

To upload all files to a specific Google Drive folder:

1. Navigate to your Google Drive folder
2. Copy the folder ID from the URL (example: `1a2b3c4d5e6f7g8h9i0j`)
3. Add to `.env`:

```env
GOOGLE_DRIVE_FOLDER_ID=1a2b3c4d5e6f7g8h9i0j
```

## Google Cloud Setup (For Reference)

If you need to set up a new Google Cloud Project:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable the Google Drive API
4. Create OAuth 2.0 credentials (Service Account or OAuth Client)
5. Download the JSON credentials file
6. Place it at `storage/credentials/google-drive-credentials.json`

## File Naming Convention

Files uploaded to Google Drive follow this naming pattern:
```
{employee_id}_{employee_name}_{semester}_{school_year}.{extension}
```

Example:
```
2024-00123_Juan_Dela_Cruz_1st_2025-2026.pdf
```

## Error Handling

If Google Drive upload fails:
- The local file is still saved successfully
- An error is logged to `storage/logs/laravel.log`
- The upload process continues without blocking the user
- No error is shown to the user (graceful degradation)

## Troubleshooting

### Issue: "Credentials file not found"

**Solution:** Ensure the file exists at:
```
storage/credentials/google-drive-credentials.json
```

### Issue: "Invalid credentials"

**Solution:** 
- Verify your credentials JSON is valid
- Check that the `client_id` and `client_secret` are correct
- Ensure the Google Drive API is enabled in Google Cloud Console

### Issue: "Access denied when accessing Google Drive"

**Solution:**
- The service account may not have access to the specified folder
- Grant access to the service account email in that folder
- Or remove the `GOOGLE_DRIVE_FOLDER_ID` to upload to root

### Issue: Files uploaded but no Google Drive link stored

**Solution:**
- Check the application logs: `storage/logs/laravel.log`
- Verify the credentials have Drive API permissions
- Check that `GOOGLE_DRIVE_ENABLE=true` in your `.env`

## API Responses

The upload API returns this structure:

```json
{
  "success": true,
  "data": {
    "id": "record_id",
    "employee_name": "Juan Dela Cruz",
    "file_name": "example.pdf",
    "uploaded_at": "2026-03-25T10:30:00Z"
  }
}
```

## Security Considerations

1. **Credentials File**: Keep `storage/credentials/google-drive-credentials.json` out of version control
   - Add to `.gitignore`:
   ```
   storage/credentials/
   ```

2. **Environment Variables**: Never commit `.env` file with real credentials

3. **File Access**: Use Google Drive permissions to control who can access uploaded files

4. **Logging**: Sensitive information is logged. Ensure logs are securely stored

## Support

For issues or questions:
1. Check the application logs: `storage/logs/laravel.log`
2. Review Google Drive API documentation
3. Ensure all configurations are correct in `.env`

---

**Last Updated:** March 25, 2026
**Version:** 1.0
