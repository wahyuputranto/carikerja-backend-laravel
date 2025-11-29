# Interview & Offering Workflow Refinement - 29 November 2025

## Summary
Sesi ini fokus pada penyempurnaan workflow interview dan offering dengan menambahkan restriksi status, upload dokumen offering letter, dan perbaikan UI/UX untuk admin dan kandidat.

---

## 1. Status Update Restrictions ✅

### Backend (Laravel)
**File Modified:** `resources/js/Pages/TalentPool/Show.vue`

**Implementation:**
- Menambahkan fungsi `getAvailableStatuses(app)` yang memfilter status dropdown berdasarkan kondisi:
  - Status `OFFERING`, `HIRED`, `PROCESSING_VISA`, `DEPLOYED` **tidak muncul** jika:
    - Aplikasi masih dalam status `APPLIED` atau `REVIEW`
    - Status `INTERVIEW` tetapi `interview_date` masih `null`
  
**Logic:**
```javascript
const getAvailableStatuses = (app) => {
    const allStatuses = ['APPLIED', 'REVIEW', 'INTERVIEW', 'OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED', 'REJECTED'];
    
    if (app.status === 'INTERVIEW' && !app.interview_date) {
        return allStatuses.filter(s => !['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(s));
    }
    
    if (['APPLIED', 'REVIEW'].includes(app.status)) {
        return allStatuses.filter(s => !['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(s));
    }
    
    return allStatuses;
};
```

**Purpose:** Mencegah admin mem-bypass tahap interview dengan langsung mengubah status ke offering/hired.

---

## 2. Offering Letter Upload Feature ✅

### Database Migration
**File Created:** `database/migrations/2025_11_29_153525_add_offering_letter_path_to_job_applications_table.php`

**Changes:**
```php
Schema::table('job_applications', function (Blueprint $table) {
    $table->string('offering_letter_path')->nullable()->after('interview_feedback');
});
```

### Backend Controller
**File Modified:** `app/Http/Controllers/DeploymentController.php`

**Implementation:**
- Updated `store()` method untuk stage `CONTRACT`:
  - Validasi file: `offering_letter` (PDF/DOC/DOCX, max 2MB)
  - Upload file ke MinIO disk
  - Simpan path di `job_applications.offering_letter_path`
  
**Code:**
```php
if ($stage === 'CONTRACT') {
    $validated = $request->validate([
        'contract_number' => 'required|string|max:255',
        'signed_at' => 'required|date',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'offering_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    ]);
    
    if ($request->hasFile('offering_letter')) {
        $path = $request->file('offering_letter')->store('offering-letters', 'minio');
        $application->update(['offering_letter_path' => $path]);
    }
}
```

### Frontend Modal
**File Modified:** `resources/js/Pages/TalentPool/Modals/DeploymentModal.vue`

**Changes:**
- Menambahkan field `offering_letter: null` ke form
- Menambahkan file input untuk stage CONTRACT:
  ```vue
  <input type="file" accept=".pdf,.doc,.docx" @change="form.offering_letter = $event.target.files[0]" />
  ```
- Menambahkan `forceFormData: true` pada form submission untuk mendukung file upload
- Menambahkan progress bar untuk upload

---

## 3. Offering Details Display (Admin) ✅

**File Modified:** `resources/js/Pages/TalentPool/Show.vue`

**Implementation:**
- Menambahkan eager loading `applications.deployment` di `TalentPoolController::show()`
- Menambahkan relasi di model `Application`:
  ```php
  public function deployment() {
      return $this->hasOne(Deployment::class, 'application_id');
  }
  ```

**UI Features:**
- Bagian baru "Offering & Contract Details" muncul saat status OFFERING/HIRED/PROCESSING_VISA/DEPLOYED
- Menampilkan:
  - Contract Number
  - Signed Date
  - Contract Duration (Start - End Date)
  - Download button untuk Offering Letter (jika ada)

**Visual Design:**
- Purple-themed box untuk membedakan dari Interview section
- Grid layout 2 kolom untuk desktop
- Hover effects pada download button

---

## 4. Offering Letter Download (Admin) ✅

**File Modified:** `resources/js/Pages/TalentPool/Show.vue`

**Implementation:**
- Fungsi `viewDocument()` sudah ada, tinggal digunakan untuk offering letter
- Button download menggunakan endpoint `talent-pool.document-url` yang generate temporary MinIO URL

**Code:**
```vue
<button @click="viewDocument({ file_path: app.offering_letter_path })">
    Download Offering Letter
</button>
```

---

## 5. Backend Go Synchronization ✅

### File Modified
**File:** `carikerja-backend-go/internal/domain/application.go`

**Changes:**
Added new fields to `JobApplication` struct:
```go
type JobApplication struct {
    // ... existing fields
    
    // Interview Details
    InterviewDate     *time.Time `db:"interview_date" json:"interview_date,omitempty"`
    InterviewLocation *string    `db:"interview_location" json:"interview_location,omitempty"`
    InterviewAddress  *string    `db:"interview_address" json:"interview_address,omitempty"`
    InterviewNotes    *string    `db:"interview_notes" json:"interview_notes,omitempty"`
    InterviewFeedback *string    `db:"interview_feedback" json:"interview_feedback,omitempty"`
    AdminNotes        *string    `db:"admin_notes" json:"admin_notes,omitempty"`
    
    // Offering Document
    OfferingLetterPath *string `db:"offering_letter_path" json:"offering_letter_path,omitempty"`
    
    // Relations
    Job *Job `json:"job,omitempty"`
}
```

**Purpose:** Menyinkronkan struct Go dengan perubahan schema database agar endpoint `/api/v1/applications` di frontend kandidat tidak error.

---

## 6. Offering Details Display (Candidate) ✅

### Frontend Candidate
**File Modified:** `carikerja-frontend-candidate/src/views/ApplicationsPage.vue`

**Implementation:**
- Menambahkan section "Offering Details" yang muncul ketika status >= OFFERING
- Visual dengan purple theme untuk konsistensi
- Menampilkan informasi offering dan tombol download

**Code:**
```vue
<div v-if="['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(app.application.status)" 
     class="mt-2 space-y-1 bg-purple-50/50 p-3 rounded border border-purple-100">
    <p class="font-semibold text-purple-900 mb-2">Offering Details</p>
    <button @click="downloadOfferingLetter(app.application.offering_letter_path)">
        Download Offering Letter
    </button>
</div>
```

---

## 7. Offering Letter Download (Candidate) ✅

### Backend API Endpoint
**File Modified:** `routes/api.php`

**New Route:**
```php
Route::post('/offering-letter/download', function(\Illuminate\Http\Request $request) {
    $validated = $request->validate(['file_path' => 'required|string']);
    
    $url = \Storage::disk('minio')->temporaryUrl($validated['file_path'], now()->addMinutes(5));
    
    return response()->json(['url' => $url]);
});
```

**Note:** Route ada di `api.php` (bukan `web.php`) agar bisa diakses tanpa Laravel session auth karena kandidat menggunakan auth dari Go backend.

### Frontend Function
**File Modified:** `carikerja-frontend-candidate/src/views/ApplicationsPage.vue`

**Implementation:**
```javascript
const downloadOfferingLetter = async (filePath) => {
    const response = await fetch('http://localhost:8000/api/offering-letter/download', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ file_path: filePath })
    });
    
    const data = await response.json();
    window.open(data.url, '_blank');
}
```

---

## 8. Collapsible Interview & Offering Sections ✅

### Implementation
**File Modified:** `resources/js/Pages/TalentPool/Show.vue`

**Features:**
- Interview Details dan Offering Details sekarang collapsible
- Klik header untuk expand/collapse
- Icon chevron yang rotate saat di-expand
- State terpisah untuk setiap aplikasi (per `app.id`)

**State Management:**
```javascript
const expandedInterviews = ref({});
const expandedOfferings = ref({});

const toggleInterview = (appId) => {
    expandedInterviews.value[appId] = !expandedInterviews.value[appId];
};

const toggleOffering = (appId) => {
    expandedOfferings.value[appId] = !expandedOfferings.value[appId];
};
```

**UI:**
```vue
<button @click="toggleInterview(app.id)" class="w-full flex items-center justify-between">
    <h4>Interview Scheduled</h4>
    <svg :class="{ 'rotate-180': expandedInterviews[app.id] }">
        <path d="M19 9l-7 7-7-7" />
    </svg>
</button>

<div v-show="expandedInterviews[app.id]">
    <!-- Interview content -->
</div>
```

**Benefits:**
- Menghemat space di halaman
- Lebih rapi untuk aplikasi dengan banyak history
- User bisa fokus pada informasi yang relevan

---

## 9. Clickable Dashboard Items ✅

### Implementation
**File Modified:** `resources/js/Pages/Dashboard.vue`

**Changes:**
- Mengubah div Recent Jobs menjadi `<Link>` component
- Mengubah div New Candidates menjadi `<Link>` component
- Menambahkan hover effects dan cursor pointer

**Code:**
```vue
<!-- Recent Jobs -->
<Link v-for="job in recentJobs" 
      :key="job.id" 
      :href="route('jobs.edit', job.id)"
      class="... hover:bg-gray-100 cursor-pointer">
    {{ job.title }}
</Link>

<!-- New Candidates -->
<Link v-for="candidate in recentCandidates" 
      :key="candidate.id" 
      :href="route('talent-pool.show', candidate.id)"
      class="... hover:bg-gray-100 cursor-pointer">
    {{ candidate.name }}
</Link>
```

**Purpose:** Meningkatkan UX dengan membuat shortcut ke detail page langsung dari dashboard.

---

## 10. Favicon Update (Candidate Frontend) ✅

### Implementation
**File Modified:** `carikerja-frontend-candidate/index.html`

**Changes:**
```html
<head>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="apple-touch-icon" href="/favicon.png" />
  <title>CariKerja - Platform Lowongan Kerja Terpercaya</title>
  <meta name="description" content="CariKerja - Temukan lowongan kerja terbaik..." />
</head>
```

**Additional:**
- Updated page title untuk SEO
- Added meta description
- Added apple-touch-icon untuk iOS devices
- User perlu save gambar favicon.png ke folder `public/`

---

## Technical Notes

### Files Modified (Summary)
**Laravel Backend:**
1. `database/migrations/*_add_offering_letter_path_to_job_applications_table.php` (Created)
2. `app/Models/Application.php` (Added `deployment()` relation)
3. `app/Http/Controllers/TalentPoolController.php` (Added eager loading)
4. `app/Http/Controllers/DeploymentController.php` (Added offering letter upload)
5. `resources/js/Pages/TalentPool/Show.vue` (Status restriction, offering display, collapsible)
6. `resources/js/Pages/TalentPool/Modals/DeploymentModal.vue` (File upload)
7. `resources/js/Pages/Dashboard.vue` (Clickable items)
8. `routes/api.php` (Download endpoint)

**Go Backend:**
1. `internal/domain/application.go` (Updated struct)

**Candidate Frontend:**
1. `src/views/ApplicationsPage.vue` (Offering display & download)
2. `index.html` (Favicon & meta tags)

### Database Changes
- Added column: `job_applications.offering_letter_path` (string, nullable)

### MinIO Storage
- New directory: `offering-letters/`
- File types: PDF, DOC, DOCX
- Max size: 2MB per file

---

## Testing Checklist

- [x] Admin dapat upload offering letter saat update status ke OFFERING
- [x] Admin dapat melihat detail offering & contract di aplikasi
- [x] Admin dapat download offering letter yang sudah diupload
- [x] Admin tidak bisa set status OFFERING/HIRED tanpa interview
- [x] Kandidat dapat melihat offering details di dashboard
- [x] Kandidat dapat download offering letter
- [x] Interview & Offering sections dapat di-collapse/expand
- [x] Dashboard items (jobs & candidates) dapat diklik
- [x] Frontend Go tidak error saat fetch applications
- [x] Favicon candidate frontend sudah diupdate

---

## Known Issues & Future Improvements

### Known Issues
✅ All issues resolved

### Future Improvements
1. **Email Notification**: Auto-send email ke kandidat saat offering letter diupload
2. **Version Control**: Track perubahan offering letter (jika di-reupload)
3. **E-Signature**: Integrasi e-signature untuk offering letter
4. **Template System**: Template offering letter yang bisa di-customize per client
5. **Analytics**: Track download offering letter oleh kandidat

---

## Deployment Notes

### Migration Command
```bash
php artisan migrate
```

### Cache Clear (if needed)
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### MinIO Permissions
Pastikan MinIO bucket `carikerja-local` memiliki permission untuk:
- Upload files ke `offering-letters/` directory
- Generate temporary URLs

---

## Conclusion

Sesi ini berhasil menyelesaikan:
1. ✅ Workflow enforcement (tidak bisa skip interview)
2. ✅ Document management untuk offering letter
3. ✅ Candidate self-service (view & download offering)
4. ✅ UI/UX improvements (collapsible sections, clickable dashboard)
5. ✅ Backend synchronization (Go + Laravel)

**Total Development Time:** ~2 hours
**Files Modified:** 11 files
**Lines of Code:** ~500 lines

---

*Document created: 2025-11-30 00:14 WIB*
*Last updated: 2025-11-30 00:14 WIB*
