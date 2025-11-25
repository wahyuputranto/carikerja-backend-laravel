# Implementasi Proyek CariKerja (Backend Laravel 12)

Dokumen ini mencatat langkah-langkah implementasi teknis yang telah dilakukan pada proyek.

## 1. Setup Awal & Konfigurasi
- [x] **Framework**: Laravel 12
- [x] **Stack**: Inertia.js + Vue 3 + Tailwind CSS
- [x] **Auth**: Laravel Breeze (Vue version)
- [x] **Database**: PostgreSQL (dikonfigurasi via .env)

## 2. Struktur & Layout Frontend
- [x] **Main Layout (`AppLayout.vue`)**:
    - Implementasi Sidebar Navigation (Glassmorphism style).
    - Header dengan User Dropdown & Dark Mode Toggle.
    - Responsive design (Sidebar toggle).
    - Flash Message support (siap menerima data dari backend).
    - Refactored to a "Flowbite-style" stacked layout with top navigation.
    - Added icons to navigation links.

## 3. Routing & Middleware (Inertia)
- [x] **Route Definition (`web.php`)**:
    - `/dashboard` -> Dashboard Page.
    - `/master-data` -> Master Data Index (Protected).
    - `/profile` -> Profile Management.
- [x] **Inertia Shared Data (`HandleInertiaRequests.php`)**:
    - `auth.user`: Data user yang sedang login.
    - `auth.role`: Slug role user (untuk logic frontend).
    - `flash`: Flash messages (`success`, `error`).

## 4. Otorisasi & RBAC (Role-Based Access Control)
- [x] **Database Models**:
    - `Role` model created (`app/Models/Role.php`).
    - `User` model updated:
        - Relasi `belongsTo` ke Role.
        - Helper `hasRole($slug)` untuk pengecekan permission.
- [x] **Middleware Keamanan**:
    - `CheckRole` middleware created (`app/Http/Middleware/CheckRole.php`).
    - Registered alias `role` di `bootstrap/app.php`.
    - Penerapan middleware `role:superadmin` pada route `/master-data`.
- [x] **Policies**:
    - Created `JobPolicy` to authorize job updates and deletions based on user role (superadmin or job owner).

## 5. UI Premium & Aesthetics
- [x] **Design System**:
    - Font: **Outfit** (Google Fonts).
    - Colors: Custom Primary Palette (Indigo/Violet base).
    - Utilities: Glassmorphism (`.glass`, `.glass-dark`), Gradient Text.
- [x] **Components**:
    - `PremiumButton.vue`: Button dengan micro-animations & particle effects.
    - `AppLayout.vue`: Updated with glassmorphism sidebar & page transitions (`slide-up`).
- [x] **Tailwind Config**:
    - Extended animations (`float`, `pulse-slow`).
    - Custom keyframes.

## 6. Master Data Module
- [x] **Document Types CRUD**:
    - [x] CRUD Document Types (`is_mandatory`, `chunkable`).
    - [x] Modal-based form untuk create/edit.
- [x] **Users & Roles Management**:
    - [x] CRUD Users dengan assignment role.
    - [x] Role selection (Admin, Client, Vendor).
- [x] **Full Master Data CRUD**:
    - Created full CRUD interfaces (Controllers, Vue components with modals) for Job Categories, Skills, and Locations.

## 7. Job Posting & Talent Pool
- [x] **Job Posting Module**:
    - [x] Model `Job` dengan UUID support.
    - [x] Controller `JobController` dengan CRUD operations.
    - [x] Form input: Judul, Kuota, Deadline, Kategori, Lokasi, Gaji.
    - [x] Halaman listing dengan status badge (DRAFT, PUBLISHED, CLOSED).
    - [x] Menu sidebar untuk Job Posting.
    - [x] **Role-based Job Creation**: Superadmins can select a client when creating a job; clients have jobs assigned to them automatically.
- [x] **Talent Pool Module**:
    - [x] Model `Candidate` dengan `hiring_status` field.
    - [x] Controller `TalentPoolController` untuk filter kandidat READY_TO_HIRE.
    - [x] Search functionality (nama, email, phone).
    - [x] Halaman listing kandidat dengan profile preview.
    - [x] Menu sidebar untuk Talent Pool.
- [x] **Client Listing**:
    - Created a page to list all clients, visible to superadmins.
    - Implemented full CRUD functionality for clients (Users + Client Profiles).

## 8. Completed Features
- [x] **Job Posting - Create/Edit Form**:
    - [x] Form untuk create dan edit job posting.
    - [x] Field: Title, Description, Requirements, Category, Location, Salary, Quota, Deadline, Status.
    - [x] Delete functionality.
- [x] **Talent Pool - Detail View**:
    - [x] Halaman detail kandidat lengkap (`TalentPool/Show.vue`).
    - [x] Riwayat pendidikan dan pengalaman kerja.
    - [x] Update hiring status dari detail page.
- [x] **Data Integrity**: Semua tabel terkait kandidat (`candidate_profiles`, `candidate_educations`, `candidate_experiences`, `candidate_skills`, `candidate_documents`) memiliki foreign key dengan `onDelete('cascade')`, sehingga menghapus kandidat otomatis menghapus semua data terkait.

## 9. Next Steps (To-Do)
- [ ] **Job-Document Relation**:
    - Syarat dokumen khusus per job (many-to-many).
    - Attach document types saat create/edit job.
- [ ] **Candidate Module**:
    - Upload & Review CV.
    - Workflow status lamaran.
- [ ] **Advanced Filters**:
    - Filter Talent Pool by skills, experience level.
    - Filter Jobs by category, location, salary range.

## 10. Bug Fixes & Refinements (Sesi Interaktif)
- [x] **Bug Fix: `JobApplication` Class Not Found**
    - **File:** `app/Models/Candidate.php`
    - **Change:** Corrected a `HasMany` relationship to point to the correct `Application::class` instead of the non-existent `JobApplication::class`.
- [x] **Bug Fix: `applications` Table Not Found**
    - **File:** `app/Models/Application.php`
    - **Change:** Updated the `$table` property to `job_applications` to match the database schema.
- [x] **Feature: Talent Pool Status Update & Redirect**
    - **File:** `app/Http/Controllers/TalentPoolController.php`
    - **Change:** Changed the redirect after status update from `redirect()->back()` to `redirect()->route('talent-pool.index')` as per user request.
- [x] **Feature: Global Flash/Success Messages**
    - **File:** `resources/js/Layouts/AppLayout.vue`
    - **Change:** Added a new component to the main layout to display success and error messages flashed to the session, making feedback visible to the user across the application.
- [x] **Bug Fix: Job & Location Dropdowns**
    - **File:** `database/seeders/DatabaseSeeder.php`
    - **Change:** Added `MasterDataSeeder` to the main seeder to populate job categories and locations.
- [x] **Feature: Enhanced Seeding with Factories**
    - Refactored `ClientProfileSeeder` and `CandidateSeeder` to use factories.
    - Created a new `JobSeeder` to generate 100 job postings.
    - Created factories for `ClientProfile`, `Candidate`, `CandidateProfile`, `CandidateEducation`, `CandidateExperience`, and `Job`.
    - Fixed numerous bugs in models (missing `HasFactory` traits, missing UUID generation) and migrations (missing `quota` and `deadline` columns) that were discovered during the process.
    - Updated the main `DatabaseSeeder` to call all new and refactored seeders in the correct order.

## 11. Recent Updates (Candidate Review & Master Data)
- [x] **Candidate Review Module**:
    - **Smart Table**: Implemented advanced filters (Status, Position, Location) and server-side pagination in `TalentPool/Index.vue`.
    - **Review Actions**: Added Approve, Reject, and Revise actions with modals.
    - **Secure Document Viewer**: Implemented `Storage::temporaryUrl` for secure file access.
    - **Backend Logic**: Updated `TalentPoolController` to handle review actions and filtering.
- [x] **Master Data Enhancements**:
    - **Document Types**: Added `stage` column (`REGISTRATION`, `POST_HIRING`) to `master_document_types` via migration and seeder update.
    - **Locations**: Added Type Filter (Country, Province, City) to `Locations/Index.vue`.
- [x] **Application Tracking**:
    - **Schema Update**: Updated `job_applications` table with new status enum (`APPLIED`, `INTERVIEW`, `OFFERING`, `PROCESSING_VISA`, `DEPLOYED`, `REJECTED`) and `current_step` (1-12).
    - **Seeding**: Created `ApplicationSeeder` to generate realistic application data with mapped statuses and steps.
- [x] **UI Refinements**:
    - **Logo Update**: Replaced application logo with new PNG image in Login and Dashboard.
    - **Login Page**: Redesigned login page with new logo and layout.
- [x] **Interview Management**:
    - **Database**: Created `interviews` table with columns for scheduling, type (Online/Offline), meeting link, results, and feedback.
- [x] **Deployment Pipeline**:
    - **Database**: Created `deployments` table (One-to-One with `applications`) to track post-hiring steps (Contract, Medical, Legal, E-PMI, Ticketing).
- [x] **Recruitment Pipeline Logic**:
    - **Frontend**: Updated `TalentPool/Show.vue` with Application History and Status Dropdown.
    - **Conditional Status**: Implemented logic to show different statuses based on country (Indonesia: HIRED, Others: PROCESSING_VISA/DEPLOYED).
    - **Database**: Added `HIRED` status to `job_applications` enum.
    - **Modals**: Created `InterviewModal` and `DeploymentModal` for step-specific data entry.
    - **Controllers**: Created `InterviewController`, `DeploymentController`, and `ApplicationController`.
- [x] **UI Enhancements**:
    - **Talent Pool**: Added Client Name and Detailed Location (City, Country) to Application History.
    - **Job Posting**: Added Client Name and Detailed Location (City, Country) to Job Cards.
- [x] **Filter & Search Enhancements**:
    - **Job Posting**: Added filters for Status (DRAFT, PUBLISHED, CLOSED) and Location.
    - **Talent Pool**: Removed Position filter as per request.
- [x] **Bug Fixes**:
    - **JobController**: Fixed `Call to undefined method authorize()` error by updating the base `Controller` to use `AuthorizesRequests` trait.
