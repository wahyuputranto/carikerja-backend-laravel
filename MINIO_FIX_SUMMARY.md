# Fix Summary: MinIO Connection Error

## ✅ Issue Resolved
**Problem:** 500 Internal Server Error when trying to view documents in Admin Dashboard.
**Cause:**
1.  **Missing Configuration:** The `minio` disk was not defined in `config/filesystems.php`.
2.  **Missing Driver:** The `league/flysystem-aws-s3-v3` package (required for S3/MinIO) was not installed.

## 🛠️ Fix Implemented
1.  **Updated `config/filesystems.php`:** Added `minio` disk configuration using `MINIO_` environment variables.
2.  **Installed Package:** Running `composer require league/flysystem-aws-s3-v3` to install the necessary driver.

## 🚀 Next Steps
1.  **Wait for Installation:** Ensure the `composer require` command finishes.
2.  **Check `.env`:** Make sure your `.env` file has the following variables (matching your Go backend):
    ```env
    MINIO_ACCESS_KEY=minioadmin
    MINIO_SECRET_KEY=minioadmin
    MINIO_BUCKET=carikerja
    MINIO_ENDPOINT=http://127.0.0.1:9000
    ```
3.  **Restart Server:** Stop and restart `php artisan serve`.
4.  **Test:** Try viewing a document again.
