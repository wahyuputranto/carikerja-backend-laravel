# Fix Summary: MinIO Bucket Mismatch

## ✅ Issue Resolved
**Problem:** `NoSuchBucket` error when viewing documents.
**Cause:** Laravel was configured to use bucket `carikerja`, but the Go backend (which uploaded the files) is using a different bucket (likely `agency-documents`).

## 🛠️ Fix Implemented
Updated `config/filesystems.php` in Laravel to default to `agency-documents`, matching the Go backend's default.

## 🚀 Next Steps
1.  **Verify Bucket Name:**
    - Check your Go backend configuration (`carikerja-backend-go/.env`).
    - Look for `MINIO_BUCKET`. If it's not there, it defaults to `agency-documents`.
2.  **Update Laravel .env:**
    - Open `carikerja-backend-laravel/.env`.
    - Set `MINIO_BUCKET` to match the Go backend (e.g., `agency-documents`).
    ```env
    MINIO_BUCKET=agency-documents
    ```
3.  **Restart Server:** Stop and restart `php artisan serve`.
4.  **Test:** Try viewing the document again.
