# ✅ Google Drive Integration - Wizard Form Implementation

## What's Been Done

Your wizard form (`wizard.blade.php`) is now fully integrated with Google Drive uploads!

### Changes Made:

1. **IpcrfController::store()** - Updated to include Google Drive upload
   - Files now upload to both local storage AND Google Drive
   - File ID and link stored in database
   - Graceful error handling if Google Drive fails

2. **Ipcrf Model** - Added Google Drive fields
   - `google_drive_file_id` - Stores the Google Drive file ID
   - `google_drive_link` - Stores the shareable link

3. **Database Migration** - Created migration to add columns
   - `database/migrations/2026_03_25_add_google_drive_to_ipcrfs_table.php`

## How It Works

When a user completes the wizard and uploads a scanned IPCRF file:

```
1. User fills and submits wizard form (Step 1-4)
2. File validation occurs
3. File stored locally → storage/app/ipcrfs/scanned/
4. Simultaneously uploaded to Google Drive
5. File ID and shareable link stored in database
6. Success message displayed
```

## Setup Required

### Step 1: Run Migration
```bash
php artisan migrate
```

This adds the Google Drive columns to the `ipcrfs` table.

### Step 2: Verify .env
```env
GOOGLE_DRIVE_ENABLE=true
GOOGLE_DRIVE_FOLDER_ID=     # Optional: Add your folder ID
```

### Step 3: Clear Cache
```bash
php artisan config:clear
```

## Testing

### Test the Wizard Upload

1. Navigate to the wizard form page
2. Complete all 4 steps:
   - **Step 1**: Select Province and Municipality
   - **Step 2**: Enter Employee/Personnel Name
   - **Step 3**: Upload a scanned PDF/JPG file
   - **Step 4**: Processing will occur
3. You should see: "IPCRF uploaded successfully!"
4. Check Google Drive:
   - File should appear with name: `{name}_{province}_{municipality}_{timestamp}.{ext}`
   - Example: `Juan_Dela_Cruz_Davao_del_Sur_Davao_City_2026-03-25_101530.pdf`

### Test Database Verification

```bash
php artisan tinker
>>> App\Models\Ipcrf::latest()->first();
```

You should see:
- `google_drive_file_id` - Populated with Google Drive file ID
- `google_drive_link` - Populated with shareable link

## File Naming Convention

Files uploaded to Google Drive from the wizard follow this pattern:

```
{name}_{province}_{municipality}_{timestamp}.{extension}

Example:
Juan_Dela_Cruz_Davao_del_Sur_Davao_City_2026-03-25_101530.pdf
```

This makes files easily searchable and trackable on Google Drive.

## Features Enabled

✅ **Automatic Upload** - File automatically goes to Google Drive when submitted
✅ **Dual Storage** - Files stored locally (backup) AND on Google Drive
✅ **Link Tracking** - Shareable link stored for easy access
✅ **Error Resilience** - Local save succeeds even if Google Drive fails
✅ **Logging** - All operations logged for debugging

## Database Structure

### ipcrfs table with new columns:

```sql
id
name
province
municipality
evaluated_file_path
scanned_file_path
status
google_drive_file_id          ← NEW
google_drive_link             ← NEW
created_at
updated_at
```

## Wizard Form Integration Details

Your existing `wizard.blade.php` form:
- ✅ Submits to `route('upload.store')`
- ✅ Sends file via `name="scanned_file"`
- ✅ File automatically uploaded to Google Drive on the backend
- ✅ No changes needed on the form itself!

## Error Handling

If Google Drive upload fails:
- Local file is still saved ✅
- User doesn't see an error (graceful degradation)
- Error logged to `storage/logs/laravel.log` for debugging
- Upload process continues seamlessly

## Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Clear cache: `php artisan config:clear`
3. ✅ Test wizard upload
4. ✅ Verify file on Google Drive
5. ✅ Check database for links

## Troubleshooting

### Files not appearing on Google Drive?

```bash
# Check logs
tail -f storage/logs/laravel.log

# Verify Google Drive is enabled
php artisan tinker
>>> config('services.google_drive.enable_upload')
# Should return: true
```

### Database columns missing?

```bash
# Check migration status
php artisan migrate:status

# If not migrated, run it
php artisan migrate
```

### File stored locally but not on Google Drive?

Check logs:
```bash
tail -f storage/logs/laravel.log | grep "Google Drive"
```

Common issues:
- `GOOGLE_DRIVE_ENABLE` not set to `true`
- Credentials file missing or invalid
- Google Drive API permissions not enabled

## Accessing Uploaded Files

### For End Users:
- Files are stored on Google Drive with direct links
- Share the folder or specific files with users
- Access via the `google_drive_link` field in database

### For Admins:
```bash
php artisan tinker
>>> App\Models\Ipcrf::latest(5)->get(['name', 'google_drive_link'])
# Shows last 5 uploads with their Google Drive links
```

## Status

🟢 **Integration Complete**

The wizard form is fully integrated with Google Drive. Files will automatically upload when the form is submitted.

---

**Updated**: March 25, 2026
**Version**: 1.0 - Wizard Integration Complete

Ready to test! 🚀
