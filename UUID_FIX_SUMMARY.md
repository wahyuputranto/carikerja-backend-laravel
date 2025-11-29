# Fix Summary: Invalid UUID Error

## ✅ Issue Resolved
**Problem:** `QueryException` with message `invalid input syntax for type uuid: "94"`.
**Cause:** The `Document` model was treating the UUID primary key as an integer (Laravel default). This caused the UUID to be cast to an integer (e.g., "94..." became 94) when sent to the frontend, resulting in an invalid ID being sent back to the server.

## 🛠️ Fix Implemented
Updated `App\Models\Document.php` to explicitly define the primary key type:
```php
protected $keyType = 'string';
public $incrementing = false;
```

## 🚀 Next Steps
1.  **Restart Server:** Stop and restart `php artisan serve`.
2.  **Refresh Page:** Reload the Candidate Detail page.
3.  **Test:** Try Approving or Rejecting a document again.
