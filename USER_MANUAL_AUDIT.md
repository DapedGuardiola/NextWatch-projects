# USER MANUAL AUDIT REPORT
## NextWatch - End-User Features Analysis

**Date:** June 6, 2026  
**Scope:** User-facing features only (excluding developer/admin routes)  
**Status:** Comprehensive audit for User Manual creation

---

## PART 1: FEATURE INVENTORY

### 1.1 GUEST/PUBLIC FEATURES (Non-Authenticated)

#### Feature: Landing Page
- **Tujuan:** Menampilkan daftar film populer, aktor, dan genre untuk user yang belum login
- **Cara Mengakses:** 
  - URL: `/` (root URL)
  - Direct access tanpa login
- **Halaman Terkait:** `landing.blade.php`
- **Controller Terkait:** `LandingController@index`
- **Konten yang Ditampilkan:**
  - Hero section dengan film populer terbaru
  - Grid film dikelompokkan per genre (Top movies by genre)
  - Daftar 15 aktor paling populer
  - Search bar
  - Modal buttons untuk Login/Register
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Important entry point

#### Feature: User Registration
- **Tujuan:** Membuat akun baru untuk user
- **Cara Mengakses:** 
  - Klik "Register" di landing page
  - Form modal muncul dengan fields: Name, Email, Password, Password Confirmation
- **Halaman Terkait:** Modal di `landing.blade.php` atau redirect ke `/?modal=register`
- **Controller Terkait:** `RegisteredUserController@store`
- **Alur Lengkap:**
  1. User mengisi form register dengan nama, email, password
  2. Sistem mengirim email verifikasi (atau mock email di dev)
  3. User diarahkan ke dashboard
  4. User harus verifikasi email sebelum akses fitur penuh
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Critical first-time user flow

#### Feature: User Login
- **Tujuan:** Masuk ke akun yang sudah terdaftar
- **Cara Mengakses:** 
  - Klik "Login" di landing page
  - Form modal muncul dengan fields: Email, Password, Remember Me
- **Halaman Terkait:** Modal di `landing.blade.php` atau redirect ke `/?modal=login`
- **Controller Terkait:** `AuthenticatedSessionController@store`
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Critical authentication

#### Feature: Email Verification
- **Tujuan:** Memverifikasi email user untuk akses penuh ke aplikasi
- **Cara Mengakses:** 
  - Klik link di email verifikasi yang dikirim saat registrasi
  - Route: `GET /verify-email/{id}/{hash}`
- **Halaman Terkait:** `auth/verify-email.blade.php`
- **Controller Terkait:** `VerifyEmailController@verify`
- **Proses:**
  1. User menerima email dengan link verifikasi
  2. Klik link untuk verifikasi
  3. Setelah verifikasi, user dapat akses full features
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Important security step

#### Feature: Password Reset / Forgot Password ⚠️
- **Tujuan:** Reset password jika user lupa
- **Cara Mengakses:** 
  - Klik "Forgot Password?" di login form
  - URL: `GET /forgot-password`
- **Halaman Terkait:** `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`
- **Controller Terkait:** `PasswordResetLinkController`, `NewPasswordController`
- **Status:** ⚠️ **MOCK IMPLEMENTATION**
  - Email tidak benar-benar dikirim
  - System logs pesan mock saja
  - Code: `"MOCK EMAIL: Permintaan ganti password untuk user..."`
- **Apakah fitur selesai?** ⚠️ **PARTIAL** - Tidak berfungsi penuh di production
- **Layak di User Manual?** ⚠️ **CONDITIONAL** - Jelaskan bahwa fitur ini masih dalam development

---

### 1.2 AUTHENTICATED FEATURES - MAIN NAVIGATION

#### Feature: Main Navigation Bar (Top)
- **Tujuan:** Navigasi utama ke berbagai fitur aplikasi
- **Lokasi:** Navigation component di atas setiap halaman
- **Menu Items yang Tersedia:**
  1. **Home** - Link ke dashboard (`route('dashboard')`)
  2. **Discover** - Buka discover modal (`$dispatch('open-discover')`)
  3. **Top Charted** - Link ke top charted page (`route('dashboard.topCharted')`)
  4. **Profile Dropdown** (authenticated only)
     - Profile UI - Lihat profile dan statistik (`route('profile.index')`)
     - Profile - Edit profile information (`route('profile.edit')`)
     - Log Out - Keluar dari akun (`route('logout')`)
- **Halaman Terkait:** `layouts/navigation.blade.php`
- **Apakah fitur selesai?** ✅ **YES**
- **Layak di User Manual?** ✅ **YES**

---

### 1.3 AUTHENTICATED FEATURES - DASHBOARD

#### Feature: Dashboard / Home
- **Tujuan:** Menampilkan personalized movie recommendations kepada user
- **Cara Mengakses:** 
  - URL: `GET /dashboard`
  - Link "Home" di navigation bar
- **Halaman Terkait:** `dashboard.blade.php`
- **Controller Terkait:** `DashboardController@index`
- **Prasyarat:** 
  - User harus login
  - User harus telah menyelesaikan Personalization setup (`is_personalized = true`)
  - Jika belum, system akan redirect ke personalization page
- **Konten yang Ditampilkan:**
  1. **Search Bar** - Search movies dan actors
  2. **For You Section** - Top 9 recommended movies berdasarkan preferensi user
  3. **Suggested Collections** - Top 7 collections/franchises dari recommended movies
  4. **Top by Genre** - Movies dikelompokkan per genre dari recommendations
  5. **Featured Actors** - Top 12 actors dari recommended movies
- **Fitur Actions:**
  - Click movie → Pergi ke Movie Detail page
  - Click actor → Pergi ke Actor Detail page
  - Click collection → Pergi ke Collection Detail page
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Central hub

#### Feature: Personalization Setup (Initial Setup)
- **Tujuan:** User memilih preferensi genre dan film favorit untuk personalisasi rekomendasi
- **Cara Mengakses:** 
  - URL: `GET /personalization`
  - Automatic redirect saat user login pertama kali (jika `is_personalized = 0`)
- **Halaman Terkait:** `pages/personalization.blade.php`
- **Controller Terkait:** `PersonalizationController@index`, `PersonalizationController@store`
- **Step-by-Step Process:**
  1. **Step 1: Select Genres** - Pilih 3-5 genre favorit dari list
  2. **Step 2: Select Movies** - Pilih 5-10 film favorit untuk dianalisis
  3. **Step 3: Review & Submit** - Review pilihan dan klik submit
  4. **Step 4: Loading & Processing** - Sistem menjalankan background jobs
     - Loading page ditampilkan: `pages/loading-persona.blade.php`
     - AJAX polling: `GET /persona-status` untuk cek status
  5. **Step 5: Redirect** - Setelah selesai, user diarahkan ke Dashboard
- **Background Processing:**
  - Job 1: `ComputePersona` - Analisis preferensi user
  - Job 2: `ComputeRecommendation` - Generate recommendations
  - Duration: Dapat beberapa menit tergantung server
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Critical onboarding feature

---

### 1.4 AUTHENTICATED FEATURES - MOVIE DISCOVERY & SEARCH

#### Feature: Discover Movies
- **Tujuan:** Mencari dan memfilter film berdasarkan genre dan bahasa
- **Cara Mengakses:** 
  - Klik "Discover" di navigation bar
  - URL: `GET /discover`
- **Halaman Terkait:** `discover.blade.php`
- **Controller Terkait:** `DiscoverController@index`, `DiscoverController@results`
- **Fitur:**
  1. **Discover Modal/Page:**
     - Display hero section dengan background blur
     - Show count: "X films found based on your filter"
     - Grid of movies
  
  2. **Filter Options:**
     - **Genre Filter** - Pilih satu atau lebih genres
     - **Language Filter** - Pilih satu atau lebih bahasa original
     - Dengan caching untuk performa
  
  3. **Display:**
     - Grid tampilan film dengan poster, judul, rating, genre
     - Click movie → Pergi ke Movie Detail page
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Important discovery tool

#### Feature: Search Movies & Actors
- **Tujuan:** Mencari film atau aktor spesifik
- **Cara Mengakses:** 
  - Ada search bar di setiap halaman (dashboard, discover, movie detail)
  - Search results page: `GET /search?q=query`
- **Halaman Terkait:** `pages/search-results.blade.php`
- **Controller Terkait:** `SearchController@index`, `SearchController@live`
- **Fitur:**
  1. **Live Search (Auto-complete):**
     - Endpoint: `GET /search/live?q=query` (AJAX)
     - Minimum 2 karakter
     - Return JSON dengan 5 movies + 5 actors terbaik
  
  2. **Full Search Results:**
     - Endpoint: `GET /search?q=query` (Full page)
     - Return 20 movies + 10 actors
     - Sorted by relevance (exact match > contains)
     - Display best match sebagai featured result
     - Actors listed separately
  
  3. **Actions:**
     - Click movie → Movie Detail page
     - Click actor → Actor Detail page
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Core search feature

#### Feature: Top Charted Movies
- **Tujuan:** Melihat daftar film dengan rating tertinggi
- **Cara Mengakses:** 
  - Klik "Top Charted" di navigation bar
  - URL: `GET /top_charted`
- **Halaman Terkait:** `topcharted.blade.php`
- **Controller Terkait:** `TopChartedController@index`
- **Konten:**
  1. **All-Time Best** - Top 10 film dengan rating tertinggi
  2. **Top by Genre** - Top 1 film dari setiap genre
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Curated content

---

### 1.5 AUTHENTICATED FEATURES - MOVIE INTERACTIONS

#### Feature: View Movie Details
- **Tujuan:** Melihat informasi detail tentang sebuah film
- **Cara Mengakses:** 
  - Click pada movie poster dari dashboard, discover, search, atau top charted
  - URL: `GET /movie/detail/{tmdb_movie_id}`
- **Halaman Terkait:** `pages/movie-detail.blade.php` (844 lines)
- **Controller Terkait:** `DetailController@index`
- **Konten yang Ditampilkan:**
  1. **Hero Section** - Backdrop image, title, gradient overlay
  2. **Poster & Key Info** - Left side poster dengan rating stars
  3. **Action Buttons:**
     - **Add to Watchlist** - `POST /watchlist/{movie}`
     - **Add to Favorites** - `POST /favorite/{movie}`
     - Status: Show current state (added/not added)
  
  4. **Movie Information:**
     - Runtime, Release Date, Genres, Rating, Vote Count
     - Overview/Synopsis
     - Release Year
  
  5. **Cast & Crew** - Daftar aktor dan sutradara (clickable to actor detail)
  
  6. **Similar Movies** - Rekomendasi film serupa (grid display)
  
  7. **Comments Section:**
     - Daftar komentar dari user lain (thread support)
     - Comment form untuk post komentar
     - Edit/Delete button untuk komentar sendiri
  
  8. **YouTube Trailer** - Embedded trailer jika tersedia
  
  9. **Collections** - Link ke franchise/collection jika ada
- **User Actions pada Page:**
  - Click genre tag → Filter discover by genre
  - Click actor → Go to Actor Detail page
  - Click similar movie → Go to movie detail page
  - Post comment → Comment muncul langsung
  - Edit/Delete comment → Only for comment owner
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Core feature

#### Feature: Add to Watchlist
- **Tujuan:** Menambahkan film ke watchlist untuk ditonton nanti
- **Cara Mengakses:** 
  - Klik tombol "Add to Watchlist" di movie detail page
  - POST form: `POST /watchlist/{movie_id}`
- **Controller Terkait:** `WatchlistController@store`
- **Aksi:**
  1. Film ditambahkan ke `watchlist` table
  2. Button berubah status (disabled atau highlight)
  3. User diarahkan ke halaman yang sama
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Core feature

#### Feature: Remove from Watchlist
- **Tujuan:** Menghapus film dari watchlist
- **Cara Mengakses:** 
  - Klik tombol "Remove from Watchlist" di movie detail page
  - DELETE form: `DELETE /watchlist/{movie_id}`
- **Controller Terkait:** `WatchlistController@destroy`
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Add to Favorites
- **Tujuan:** Menambahkan film ke daftar favorit
- **Cara Mengakses:** 
  - Klik tombol "Add to Favorites" di movie detail page
  - POST form: `POST /favorite/{movie_id}`
- **Controller Terkait:** `FavoriteController@store`
- **Aksi:**
  1. Film ditambahkan ke `favorites` table dengan `is_persona = 0`
  2. Button berubah status
  3. User diarahkan ke halaman yang sama
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Core feature

#### Feature: Remove from Favorites
- **Tujuan:** Menghapus film dari daftar favorit
- **Cara Mengakses:** 
  - Klik tombol "Remove from Favorites" di movie detail page
  - DELETE form: `DELETE /favorite/{movie_id}`
- **Controller Terkait:** `FavoriteController@destroy`
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

---

### 1.6 AUTHENTICATED FEATURES - WATCHLIST & FAVORITES PAGES

#### Feature: View Watchlist
- **Tujuan:** Melihat daftar film yang akan ditonton nanti
- **Cara Mengakses:** 
  - URL: `GET /watchlist`
  - Sidebar menu di profile pages
- **Halaman Terkait:** `pages/watchlist.blade.php`
- **Controller Terkait:** `WatchlistController@index`
- **Layout:**
  - Sidebar navigation (User Profile, Account Settings, Edit Persona, Favorites, Watchlist)
  - Main content: Grid of watchlist movies
  - Empty state: "Your watchlist is empty" dengan link to Discover
  - Each movie card clickable → Go to Movie Detail
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Important user feature

#### Feature: View Favorites
- **Tujuan:** Melihat daftar film favorit user
- **Cara Mengakses:** 
  - URL: `GET /favorites`
  - Sidebar menu di profile pages
- **Halaman Terkait:** `pages/favorites.blade.php`
- **Controller Terkait:** `FavoriteController@index`
- **Layout:** Sama seperti Watchlist page
- **Catatan:** Mencakup SEMUA favorites (termasuk yang dipilih saat personalization)
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

---

### 1.7 AUTHENTICATED FEATURES - COMMENTS

#### Feature: Post Comment
- **Tujuan:** User dapat berkomentar tentang film
- **Cara Mengakses:** 
  - Comments section di movie detail page
  - Form dengan field: Content (max 1000 chars)
  - Optional: Reply to specific comment
- **Controller Terkait:** `CommentController@store`
- **Proses:**
  1. User menulis komentar
  2. Submit form: `POST /movie/comment`
  3. Komentar muncul di halaman (dengan refresh atau AJAX)
  4. User dapat melihat komentar mereka dengan nama dan avatar
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Community feature

#### Feature: Reply to Comment
- **Tujuan:** User dapat membalas komentar lain
- **Cara Mengakses:** 
  - Klik "Reply" pada komentar di movie detail
  - Form reply muncul dengan field untuk reply_id dan content
- **Controller Terkait:** `CommentController@store` (dengan `reply_id`)
- **Proses:**
  1. Komentar balasan tertambah sebagai child dari parent comment
  2. Display hierarchical di halaman (indented)
  3. Sorted dari oldest first
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Edit Comment
- **Tujuan:** User dapat mengedit komentar mereka sendiri
- **Cara Mengakses:** 
  - Klik "Edit" button pada komentar user sendiri (di movie detail page)
  - PUT form: `PUT /comments/{comment_id}`
- **Controller Terkait:** `CommentController@update`
- **Authorization:** Hanya comment owner yang bisa edit
- **Proses:**
  1. Form edit muncul dengan content lama
  2. User mengedit konten
  3. Submit → Komentar ter-update
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Delete Comment
- **Tujuan:** User dapat menghapus komentar mereka sendiri
- **Cara Mengakses:** 
  - Klik "Delete" button pada komentar user sendiri
  - DELETE form: `DELETE /comments/{comment_id}`
- **Controller Terkait:** `CommentController@destroy`
- **Authorization:** Hanya comment owner yang bisa delete
- **Cascade:** Semua replies dari comment ini juga terhapus
- **Proses:**
  1. Konfirmasi delete muncul
  2. User confirm → Komentar dan semua replies terhapus
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: View Comments
- **Tujuan:** Melihat komentar dari user lain tentang film
- **Cara Mengakses:** 
  - Comments section di movie detail page
  - Automatically loaded dengan film detail
- **Display:**
  - Daftar top-level comments (sorted oldest first)
  - Setiap comment show: User avatar, name, content, timestamp
  - Replies ditampilkan di bawah parent comment (indented)
  - Edit/Delete buttons hanya visible untuk comment owner
- **Service:** `CommentService@getCommentsByMovie`
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

---

### 1.8 AUTHENTICATED FEATURES - ACTOR DETAIL

#### Feature: View Actor Profile
- **Tujuan:** Melihat detail profil aktor dan filmografinya
- **Cara Mengakses:** 
  - Click actor name/image di movie detail page
  - Click actor di landing page featured actors section
  - URL: `GET /actor/{tmdb_actor_id}`
- **Halaman Terkait:** `pages/actor-detail.blade.php`
- **Controller Terkait:** `DashboardController@getActorMovie`
- **Konten yang Ditampilkan:**
  1. **Hero Section:**
     - Large actor photo
     - Actor name
     - Metadata: Actor/Actress badge, Popularity score, Birth year (if available)
  
  2. **Biography** - Detailed biography text
  
  3. **Filmography** - Grid of movies where actor appeared
     - Each movie card clickable → Go to Movie Detail
  
  4. **Similar Actors** - Up to 6 actors dengan genre serupa
     - Based on shared genres dari filmography
     - Click → Go to similar actor detail
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Discovery feature

---

### 1.9 AUTHENTICATED FEATURES - COLLECTION/FRANCHISE

#### Feature: View Collection Details
- **Tujuan:** Melihat semua film dalam sebuah franchise/collection
- **Cara Mengakses:** 
  - Click collection link di movie detail page
  - URL: `GET /collection/{tmdb_collection_id}`
- **Halaman Terkait:** `pages/collection-detail.blade.php`
- **Controller Terkait:** `CollectionController@show`
- **Konten:**
  1. **Collection Name** - Large heading
  2. **Collection Overview** - Description/synopsis
  3. **Movies Grid** - Semua film dalam collection (5 columns)
     - Each movie card clickable → Go to Movie Detail
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES** - Related content feature

---

### 1.10 AUTHENTICATED FEATURES - PROFILE & SETTINGS

#### Feature: Profile UI / Profile Dashboard
- **Tujuan:** Melihat statistik profil dan preferensi user
- **Cara Mengakses:** 
  - Klik user avatar → "Profile UI" di navigation dropdown
  - URL: `GET /profileUI`
- **Halaman Terkait:** `Profile/index.blade.php`
- **Controller Terkait:** `ProfileController@index`
- **Layout:**
  - Sidebar: User Profile, Account Settings, Edit Persona, Favorites, Watchlist, Sign Out
  - Main content: Profile statistics
- **Konten:**
  1. **Profile Section:**
     - User avatar (large)
     - Button "Edit Profile" (untuk change avatar)
     - User info: Name, Email
  
  2. **Statistics Section:**
     - Genre Preferences chart (dengan percentage)
     - Preferred Actors list (dengan percentage scores)
     - Preferred Directors list (dengan percentage scores)
     - Preferred Era/Decade data
  
  3. **Data Source:** `UserTaste` model (computed dari recommendations)
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Edit Profile (Profile Information)
- **Tujuan:** Edit informasi personal user
- **Cara Mengakses:** 
  - Klik "Edit Profile" button di Profile Dashboard
  - URL: `GET /profile` atau `PUT /profile/update`
- **Halaman Terkait:** `Profile/index.blade.php` (form integrated)
- **Controller Terkait:** `ProfileController@updateProfile`
- **Form Fields yang Dapat Diubah:**
  - Name (required, min 4 chars)
  - Gender (select: Male, Female, Other)
  - Date of Birth (date picker)
  - Bio (textarea, optional)
- **Avatar Upload:**
  - Separate button: "Edit Profile" (untuk upload avatar)
  - Form: `POST /profile/avatar` (multipart/form-data)
  - Supported: jpeg, png, jpg, gif (max 2MB)
  - Storage: `public/storage/avatars/`
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Account Settings
- **Tujuan:** Mengubah email, phone, dan password
- **Cara Mengakses:** 
  - Klik "Account Settings" di sidebar atau navigation dropdown
  - URL: `GET /settings` atau `PUT /settings/update`
- **Halaman Terkait:** `Profile/settings.blade.php`
- **Controller Terkait:** `ProfileController@updateSettings`
- **Form Fields:**
  1. **Email** - Changing email triggers re-verification (mock currently)
  2. **Phone** - Optional phone number
  3. **Password** - Optional password change
- **Status:** ⚠️ Email verification adalah MOCK (tidak benar-benar mengirim email)
- **Apakah fitur selesai?** ⚠️ **PARTIAL** - Email mock only
- **Layak di User Manual?** ✅ **YES** - Tapi jelaskan bahwa email verification masih mock

#### Feature: Edit Persona / Edit Preferences
- **Tujuan:** User dapat update preferensi genre dan film favorit untuk re-compute recommendations
- **Cara Mengakses:** 
  - Klik "Edit Persona" di sidebar atau profile menu
  - URL: `GET /profile/persona`
- **Halaman Terkait:** `Profile/persona.blade.php`
- **Controller Terkait:** `ProfileController@persona`, `ProfileController@updateGenres`
- **Fitur:**
  1. **Favorite Genres Section:**
     - Display current selected genres (dengan X button untuk remove)
     - Max 4 genres
     - DELETE route: `DELETE /profile/persona/genres/{genre}`
  
  2. **Persona Favorites Section:**
     - Display film yang dipilih saat personalization
     - Grid display dengan movie posters
     - Clickable ke movie detail
  
  3. **Action:**
     - User dapat remove genres individual
     - User dapat re-run personalization jika update preferences
     - Button: "Update Persona" → Re-trigger background jobs
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

#### Feature: Logout / Sign Out
- **Tujuan:** User dapat keluar dari akun
- **Cara Mengakses:** 
  - Klik "Log Out" atau "Sign Out" button di sidebar/navigation
  - POST form: `POST /logout`
- **Controller Terkait:** `AuthenticatedSessionController@destroy`
- **Proses:**
  1. Session invalidated
  2. CSRF token regenerated
  3. User diarahkan ke landing page
- **Apakah fitur selesai?** ✅ **YES** - Fully functional
- **Layak di User Manual?** ✅ **YES**

---

## PART 2: USER NAVIGATION FLOW

### Flow 1: First-Time User (Guest)

```
START (Landing Page /)
    ├─ View: Movies by genre, Popular actors, Search bar
    └─ Choice:
        ├─ LOGIN
        │   ├─ Click "Login" button
        │   ├─ Modal form appears (Email, Password, Remember Me)
        │   ├─ Submit: POST /login
        │   └─ Redirect to Dashboard
        │
        ├─ REGISTER
        │   ├─ Click "Register" button
        │   ├─ Modal form appears (Name, Email, Password, Confirm)
        │   ├─ Submit: POST /register
        │   ├─ Email verification sent (or mock)
        │   ├─ Redirect to Dashboard
        │   └─ Next: Email Verification (GET /verify-email/{id}/{hash})
        │
        └─ CONTINUE AS GUEST
            ├─ Search for movies (but results limited)
            └─ Cannot access watchlist, favorites, personalization
```

### Flow 2: Authenticated User - Initial Onboarding

```
After Login/Register
    ├─ System checks: is_personalized == 0?
    ├─ YES → REDIRECT to Personalization Page (/personalization)
    │   ├─ Step 1: Select 3-5 Genres
    │   ├─ Step 2: Select 5-10 Movies
    │   ├─ Step 3: Review & Submit (POST /personalization)
    │   ├─ Step 4: Loading Screen appears (AJAX polling: GET /persona-status)
    │   └─ Step 5: Jobs complete → Redirect to Dashboard
    │
    └─ NO → Go directly to Dashboard (/dashboard)
```

### Flow 3: Authenticated User - Main Navigation

```
DASHBOARD (/dashboard)
    ├─ Content: For You (9), Collections (7), Top by Genre, Actors
    ├─ Actions:
    │   ├─ HOME (already here)
    │   ├─ DISCOVER → /discover (select genre/language filters)
    │   ├─ TOP CHARTED → /top_charted (view best movies)
    │   ├─ SEARCH → /search?q=query (search movie/actor)
    │   ├─ Click MOVIE → /movie/detail/{id}
    │   │   ├─ View details, cast, similar movies
    │   │   ├─ Add to Watchlist → POST /watchlist/{movie}
    │   │   ├─ Add to Favorites → POST /favorite/{movie}
    │   │   └─ Comments: View, Post, Edit, Delete
    │   ├─ Click ACTOR → /actor/{id}
    │   │   ├─ View profile, filmography
    │   │   └─ Similar actors
    │   └─ Click COLLECTION → /collection/{id}
    │       └─ View all movies in collection
    │
    ├─ PROFILE MENU (avatar dropdown):
    │   ├─ Profile UI → /profileUI (view stats)
    │   ├─ Profile → /profile (edit info)
    │   └─ Log Out → POST /logout
    │
    └─ SIDEBAR (di profile pages):
        ├─ User Profile → /profileUI
        ├─ Account Settings → /settings (edit email/phone/password)
        ├─ Edit Persona → /profile/persona (update preferences)
        ├─ Favorite Movies → /favorites (view favorites list)
        ├─ Watchlist → /watchlist (view watchlist)
        └─ Sign Out → POST /logout
```

---

## PART 3: MENU MAPPING (ALL UI MENUS)

### Top Navigation Bar (Always Visible)

| Menu Item | Authenticated | Guest | Function | Route/Action |
|-----------|---|---|---|---|
| Home | ✅ | ✅ | Go to Dashboard | GET /dashboard |
| Discover | ✅ | ✅ | Open discover modal | Dispatch event |
| Top Charted | ✅ | ✅ | Go to top charted | GET /top_charted |
| Profile Avatar (Dropdown) | ✅ | ❌ | Show user menu | - |
| → Profile UI | ✅ | ❌ | View profile | GET /profileUI |
| → Profile | ✅ | ❌ | Edit profile | GET /profile |
| → Log Out | ✅ | ❌ | Logout | POST /logout |
| Guest Icon (Dropdown) | ❌ | ✅ | Show auth menu | - |
| → Login | ❌ | ✅ | Login form | POST /login |
| → Register | ❌ | ✅ | Register form | POST /register |

### Sidebar Menu (Profile Pages Only)

| Menu Item | Pages | Function | Route/Action |
|-----------|---|---|---|
| User Profile | All profile pages | Go to profile UI | GET /profileUI |
| Account Settings | All profile pages | Go to settings | GET /settings |
| Edit Persona | All profile pages | Go to edit preferences | GET /profile/persona |
| Favorite Movies | All profile pages | Go to favorites | GET /favorites |
| Watchlist | All profile pages | Go to watchlist | GET /watchlist |
| Sign Out | All profile pages | Logout | POST /logout |

### Movie Detail Page Actions

| Action | Target | Function |
|--------|--------|----------|
| Genre Tag (Clickable) | - | Filter discover by genre |
| Actor Name/Photo | Actor | Go to actor detail |
| Similar Movie | Movie | Go to movie detail |
| Add to Watchlist | Button | POST /watchlist/{movie} |
| Add to Favorites | Button | POST /favorite/{movie} |
| Post Comment | Form | POST /movie/comment |
| Edit Comment | Button (own) | PUT /comments/{id} |
| Delete Comment | Button (own) | DELETE /comments/{id} |

---

## PART 4: USER MANUAL RECOMMENDATION

### Recommended Book Structure with Feature Mapping

```
═══════════════════════════════════════════════════════════
                     USER MANUAL STRUCTURE
═══════════════════════════════════════════════════════════

BAB 1: GETTING STARTED (PEMULA)
───────────────────────────────────────────────────────
Fitur: Landing Page, Registration, Login, Email Verification
Screenshot diperlukan:
  ✓ Landing page overview
  ✓ Registration modal form
  ✓ Login modal form
  ✓ Email verification process
  ✓ Welcome to dashboard

Screenshot tidak diperlukan:
  ✗ Backend code
  ✗ Database diagrams
  ✗ Mock email notification

Estimated Pages: 8-12

---

BAB 2: ACCOUNT MANAGEMENT
───────────────────────────────────────────────────────
Fitur:
  - Profile UI / Profile Dashboard
  - Edit Profile (name, gender, dob, bio)
  - Avatar Upload
  - Account Settings (email, phone, password)
  - Logout

Screenshot diperlukan:
  ✓ Profile UI dashboard with statistics
  ✓ Edit profile form
  ✓ Avatar upload interface
  ✓ Account settings form
  ✓ Genre preference chart
  ✓ Actor/Director preference lists

Screenshot tidak diperlukan:
  ✗ Password reset (mock feature)
  ✗ Email verification process (repeated)

Estimated Pages: 10-15

⚠️ CATATAN: Email verification & password reset masih MOCK
    - Jelaskan bahwa fitur ini masih dalam development
    - Current behavior: System hanya log message, tidak kirim email
    - User tidak perlu melakukan aksi nyata untuk verifikasi di local/dev

---

BAB 3: PERSONALIZED RECOMMENDATION
───────────────────────────────────────────────────────
Fitur:
  - Personalization Setup (First time)
  - Dashboard (For You, Collections, Top by Genre, Actors)
  - Edit Persona / Update Preferences
  - How recommendations work (overview, bukan technical)

Screenshot diperlukan:
  ✓ Personalization Step 1 (genre selection)
  ✓ Personalization Step 2 (movie selection)
  ✓ Personalization Step 3 (review & submit)
  ✓ Loading screen (processing)
  ✓ Dashboard - For You section
  ✓ Dashboard - Collections section
  ✓ Dashboard - Top by Genre
  ✓ Edit Persona page
  ✓ Genre management (add/remove)

Screenshot tidak diperlukan:
  ✗ Background jobs architecture
  ✗ Flask API integration details
  ✗ ML algorithm explanation

Estimated Pages: 15-20

---

BAB 4: MOVIE DISCOVERY
───────────────────────────────────────────────────────
Fitur:
  - Discover with Filters (Genre, Language)
  - Top Charted Movies
  - Search (Live & Full)

Screenshot diperlukan:
  ✓ Discover page with hero
  ✓ Genre filter interface
  ✓ Language filter interface
  ✓ Filter results display
  ✓ Top Charted page
  ✓ Search live (autocomplete)
  ✓ Search results page
  ✓ Best match featured result
  ✓ Additional results below

Screenshot tidak diperlukan:
  ✗ Search algorithm details
  ✗ Filter caching mechanism
  ✗ Query optimization

Estimated Pages: 12-18

---

BAB 5: MOVIE DETAILS & INTERACTIONS
───────────────────────────────────────────────────────
Fitur:
  - Movie Detail Page
  - View similar movies
  - View cast & crew
  - View collection link

Screenshot diperlukan:
  ✓ Movie detail hero section
  ✓ Poster & key information
  ✓ Movie synopsis
  ✓ Cast section
  ✓ Similar movies section
  ✓ Collection link
  ✓ All UI elements

Screenshot tidak diperlukan:
  ✗ Backend data retrieval
  ✗ Recommendation scoring algorithm

Estimated Pages: 8-12

---

BAB 6: WATCHLIST & FAVORITES
───────────────────────────────────────────────────────
Fitur:
  - Add to Watchlist
  - Remove from Watchlist
  - View Watchlist
  - Add to Favorites
  - Remove from Favorites
  - View Favorites
  - Difference between Watchlist & Favorites

Screenshot diperlukan:
  ✓ Movie detail page with Watchlist button
  ✓ Movie detail page with Favorites button
  ✓ Watchlist page (with movies grid)
  ✓ Watchlist empty state
  ✓ Favorites page (with movies grid)
  ✓ Favorites empty state
  ✓ Button state (added vs not added)

Screenshot tidak diperlukan:
  ✗ Database schema
  ✗ Activity logging details

Estimated Pages: 10-14

---

BAB 7: COMMUNITY FEATURES (COMMENTS)
───────────────────────────────────────────────────────
Fitur:
  - View Comments
  - Post Comment
  - Reply to Comment
  - Edit Comment
  - Delete Comment
  - Comment threading

Screenshot diperlukan:
  ✓ Comments section on movie detail
  ✓ Existing comments with user info
  ✓ Comment form
  ✓ Reply form
  ✓ Comment with replies (threaded)
  ✓ Edit button on own comment
  ✓ Delete confirmation
  ✓ Updated comment display

Screenshot tidak diperlukan:
  ✗ Comment database structure
  ✗ Cascading delete logic

Estimated Pages: 10-15

---

BAB 8: EXPLORATION FEATURES (ACTOR & COLLECTION)
───────────────────────────────────────────────────────
Fitur:
  - View Actor Profile
  - View Actor Filmography
  - View Similar Actors
  - View Collection
  - Browse Collection Movies

Screenshot diperlukan:
  ✓ Actor detail page
  ✓ Actor biography section
  ✓ Actor filmography (grid)
  ✓ Similar actors section
  ✓ Collection detail page
  ✓ Collection name & overview
  ✓ Movies in collection (grid)
  ✓ Clickable movie cards

Screenshot tidak diperlukan:
  ✗ Genre similarity algorithm
  ✗ TMDB API integration

Estimated Pages: 10-14

---

TOTAL ESTIMATED PAGES: 90-120 pages (dengan screenshots)

═══════════════════════════════════════════════════════════
```

---

## PART 5: FEATURES EXCLUDED FROM USER MANUAL

### Developer/Internal Routes (DO NOT DOCUMENT)

| Route | Purpose | Reason for Exclusion |
|-------|---------|---------------------|
| `/test-redis` | Redis cache testing | Developer route |
| `/test-job` | Queue worker testing | Developer route |
| `/test-function` | Debug data retrieval | Developer route |
| `/persona-loading-test` | Loading screen test | Developer route |

### Partially Implemented Features (⚠️ DOCUMENT WITH CAUTION)

| Feature | Status | Recommendation |
|---------|--------|-----------------|
| Password Reset | ⚠️ MOCK | Include in manual tapi jelaskan: "Feature masih dalam development. Email tidak benar-benar dikirim." |
| Email Verification | ⚠️ MOCK | Include tapi note: "Email verification adalah simulasi untuk environment development" |

### Incomplete/Unused Features (❌ DO NOT DOCUMENT)

| Feature | Reason |
|---------|--------|
| Rating system (user submit) | Not implemented, hanya view ratings |
| Social following | Not implemented |
| Notifications | Not implemented |
| Admin dashboard | Not accessible to end users |
| Upcoming movies (commented out) | Code disabled, fitur not active |

---

## PART 6: IMPORTANT NOTES FOR USER MANUAL AUTHOR

### ✅ Must Include:
1. Clear distinction between public (guest) and authenticated features
2. Step-by-step screenshots for complex flows (personalization, profile update)
3. Explanation of Watchlist vs Favorites difference
4. How recommendations work (non-technical overview)
5. Comment threading explanation
6. Navigation map (all menus and their functions)

### ⚠️ Must Mention:
1. Email verification is mock (development feature)
2. Password reset not fully implemented
3. Personalization may take few minutes (background jobs)
4. Max 4 genres can be selected for preferences
5. Comments support threading (nested replies)

### ❌ Must NOT Include:
1. Developer test routes
2. Backend/API documentation
3. Database schema
4. Code examples
5. Admin features
6. Mock implementations (unless clearly marked as "IN DEVELOPMENT")

### 📸 Screenshot Priority (High to Low):
1. **HIGH (Must have):**
   - Landing page
   - Registration/Login forms
   - Dashboard
   - Personalization flow (all 3 steps + loading)
   - Movie detail page
   - Watchlist/Favorites pages
   - Profile UI dashboard
   - Edit profile form

2. **MEDIUM (Should have):**
   - Search results
   - Actor detail
   - Collection page
   - Comments section
   - Account settings
   - Discover with filters
   - Top charted

3. **LOW (Nice to have):**
   - Button states (active/inactive)
   - Empty states
   - Success messages
   - Error messages

### 📝 Writing Style Recommendations:
- Use simple, non-technical language
- Include "Tips" sections for better usage
- Add "What's Next?" at end of each chapter
- Use visual callouts for important notes
- Include keyboard shortcuts if applicable
- Provide troubleshooting FAQ section

---

## PART 7: SUMMARY MATRIX

| Feature | User Facing | Complete | Mock | Ready for Manual | Priority |
|---------|---|---|---|---|---|
| Landing Page | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Registration | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Login | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Email Verification | ✅ | ⚠️ | ✅ | ✅ | HIGH (note mock) |
| Password Reset | ✅ | ⚠️ | ✅ | ✅ | HIGH (note incomplete) |
| Dashboard | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Personalization | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Discover | ✅ | ✅ | ❌ | ✅ | HIGH |
| Search | ✅ | ✅ | ❌ | ✅ | HIGH |
| Top Charted | ✅ | ✅ | ❌ | ✅ | HIGH |
| Movie Detail | ✅ | ✅ | ❌ | ✅ | CRITICAL |
| Watchlist | ✅ | ✅ | ❌ | ✅ | HIGH |
| Favorites | ✅ | ✅ | ❌ | ✅ | HIGH |
| Comments | ✅ | ✅ | ❌ | ✅ | HIGH |
| Actor Detail | ✅ | ✅ | ❌ | ✅ | MEDIUM |
| Collection | ✅ | ✅ | ❌ | ✅ | MEDIUM |
| Profile UI | ✅ | ✅ | ❌ | ✅ | HIGH |
| Edit Profile | ✅ | ✅ | ❌ | ✅ | HIGH |
| Settings | ✅ | ⚠️ | ✅ | ✅ | HIGH (note mock email) |
| Edit Persona | ✅ | ✅ | ❌ | ✅ | HIGH |
| Logout | ✅ | ✅ | ❌ | ✅ | HIGH |

**Total Features Ready for Manual:** 21 ✅  
**Total Features with Notes:** 3 ⚠️  
**Total Features to Exclude:** 4 ❌

---

**END OF USER MANUAL AUDIT**

*This audit ensures that only production-ready, end-user facing features are documented in the User Manual. Features marked as MOCK or incomplete are flagged for author awareness and appropriate documentation approach.*
