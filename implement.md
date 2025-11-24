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

## 5. Next Steps (To-Do)
- [ ] **Master Data Module**:
    - CRUD Document Types.
    - CRUD Users & Roles management.
- [ ] **Candidate Module**:
    - Upload & Review CV.
    - Workflow status lamaran.
- [ ] **UI Polish**:
    - Implementasi animasi transisi halaman.
    - Komponen Flash Message UI.
