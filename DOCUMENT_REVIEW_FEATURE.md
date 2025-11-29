# Feature Update: Admin Document Review

## ✅ Features Implemented
**Goal:** Allow admins to view, approve, and reject candidate documents from the Laravel Admin Dashboard.

### 1. Backend (Laravel)
- **Model Updates:**
    - `App\Models\Document`: Mapped to `candidate_documents` table, added relationships.
    - `App\Models\Candidate`: Added `documents` relationship.
- **Controller:**
    - Created `CandidateDocumentController` for `approve` and `reject` actions.
    - Updated `TalentPoolController` to load documents in `show` method.
- **Routes:**
    - Added `POST /candidate-documents/{document}/approve`
    - Added `POST /candidate-documents/{document}/reject`

### 2. Frontend (Inertia Vue)
- **UI Updates (`Show.vue`):**
    - Added a **Documents** section in the Candidate Detail page.
    - Displays document name, type, status, and rejection notes.
    - **View Button:** Opens secure MinIO URL.
    - **Approve Button:** Marks document as VALID.
    - **Reject Button:** Opens a modal to enter rejection reason.
- **New Component:**
    - `DocumentRejectModal.vue`: Modal for entering rejection reason.

## 🚀 How to Test
1. **Navigate to Talent Pool:** Select a candidate.
2. **Check Documents Section:** You should see uploaded documents.
3. **View:** Click "View" to open the file.
4. **Reject:** Click "Reject", enter a reason, and submit. The status should change to `INVALID`.
5. **Approve:** Click "Approve". The status should change to `VALID`.

## ⚠️ Notes
- Ensure MinIO is correctly configured in `.env` for `Storage::disk('minio')` to work.
- Run `npm run build` in `carikerja-backend-laravel` to compile the Vue changes.
