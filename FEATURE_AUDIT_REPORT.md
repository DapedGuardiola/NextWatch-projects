# Feature Inventory & Audit Report
## NextWatch Laravel Application

**Audit Date:** June 6, 2026  
**Repository:** NextWatch-projects (Development Branch)  
**Scope:** Complete source code analysis covering routes, controllers, services, models, and views

---

## 1. PUBLIC FEATURES (Non-Authenticated Users)

### 1.1 Landing Page
- **Route:** `GET /` 
- **Controller:** `LandingController@index`
- **View:** `landing.blade.php`
- **Features:**
  - Display hero section with top-rated movie
  - Show movies grouped by genre
  - Display featured actors list
  - Search bar integration
  - Modal triggers for login/register (redirects to `/?modal=login` or `/?modal=register`)
- **Services:** `LandingService`, `ActorService`, `TopChartedService`
- **Data Sources:** 
  - `Movie` model (popular movies by rating)
  - `Genre` model 
  - `Actor` model (top 15 actors)

### 1.2 Authentication Routes
- **Registration:** `POST /register` → `RegisteredUserController@store`
  - Form fields: name, email, password (confirmed)
  - Redirects to personalization after registration
  - Event: `Registered` event triggered
  
- **Login:** `POST /login` → `AuthenticatedSessionController@store`
  - Email verification required
  
- **Password Reset:** 
  - `GET /forgot-password` → `PasswordResetLinkController@create`
  - `POST /forgot-password` → `PasswordResetLinkController@store`
  - `GET /reset-password/{token}` → `NewPasswordController@create`
  - `POST /reset-password` → `NewPasswordController@store`

- **Email Verification:** 
  - `GET /verify-email` → `EmailVerificationPromptController`
  - `GET /verify-email/{id}/{hash}` → `VerifyEmailController@verify`
  - `POST /email/verification-notification` → `EmailVerificationNotificationController@store`

---

## 2. AUTHENTICATED FEATURES (After Login)

### 2.1 Dashboard
- **Route:** `GET /dashboard`
- **Controller:** `DashboardController@index`
- **View:** `dashboard.blade.php`
- **Middleware:** `auth`, `verified`
- **Access Logic:**
  - If `is_personalized == 0` → Redirects to personalization setup
  - Shows personalized content after persona setup
- **Content Sections:**
  - **For You:** Top 9 recommended movies based on user preferences
  - **Suggested Collections:** Top 7 collections from user's recommended movies
  - **Top by Genre:** Movies grouped by recommended genres
  - **Actors:** Featured actors from recommended movies
  - **Others:** Additional recommendations beyond top 9
- **Services:** `DashboardService`, `ActorService`
- **Status Flags:**
  - `is_personalized`: User completed preference setup
  - `persona_ready`: Recommendation computation completed

### 2.2 Personalization Setup (Initial Setup Only)
- **Route:** `GET /personalization`
- **Controller:** `PersonalizationController@index`
- **View:** `pages/personalization.blade.php`
- **Step-by-step Process:**
  1. Select 3-5 favorite genres
  2. Select 5-10 favorite movies
  3. Submit and trigger background jobs
  4. Loading screen (`pages/loading-persona.blade.php`)
  5. Status check via `GET /persona-status` (AJAX)
- **Form:** `POST /personalization` → `PersonalizationController@store`
  - Saves to `user_genres`, `favorites` (with `is_persona = 1`)
  - Dispatches queued jobs:
    - `ComputePersona` (computes taste profile from selections)
    - `ComputeRecommendation` (generates recommendations after persona is ready)
- **Output:** Sets `is_personalized = true` and eventually `persona_ready = true`

### 2.3 Profile Management
- **Profile UI (Dashboard):** 
  - `GET /profileUI` → `ProfileController@index`
  - View: `profile/index.blade.php`
  - Displays:
    - Genre preference chart (weight-based percentages)
    - Preferred actors list (with percentage scores)
    - Preferred directors list (with percentage scores)
    - Preferred era/decade data
  - Data source: `UserTaste` model with computed statistics

- **Profile Edit:**
  - `GET /profile` → `ProfileController@edit` → `profile/edit.blade.php`
  - `PUT /profile/update` → `ProfileController@updateProfile`
  - Editable fields: name, gender, date of birth, bio

- **Profile Settings:**
  - `GET /settings` → `ProfileController@settings` → `profile/settings.blade.php`
  - `PUT /settings/update` → `ProfileController@updateSettings`
  - Editable fields: email (triggers re-verification), phone, password
  - **MOCK FEATURE:** Email verification is logged but not sent (simulated)

- **Avatar Upload:**
  - `POST /profile/avatar` → `ProfileController@updateAvatar`
  - Stores in `public/storage/avatars/`
  - Supports: jpeg, png, jpg, gif (max 2MB)

- **Account Deletion:**
  - `DELETE /profile` → `ProfileController@destroy`
  - Requires current password confirmation

- **Persona Management:**
  - `GET /profile/persona` → `ProfileController@persona` → `profile/persona.blade.php`
  - View persona-marked favorites and manage genres
  - `POST /profile/persona/update` → Sets `is_personalized = true`
  - `POST /profile/persona/genres` → `ProfileController@updateGenres` (max 4 genres)
  - `DELETE /profile/persona/genres/{genre}` → `ProfileController@destroyGenre`

---

## 3. RECOMMENDATION SYSTEM FEATURES

### 3.1 Recommendation Engine
- **Status Flags:**
  - `is_personalized`: User selected initial genres/movies
  - `persona_ready`: Python service completed persona computation
- **Jobs Processing:**
  1. **ComputePersona** (`App\Jobs\ComputePersona`)
     - Triggered after personalization form submission
     - Reads user's selected genres and favorite movies
     - Sends data to Flask API via `FlaskService@ComputeNewTaste`
     - Stores result in `UserTaste` model (preferred_actors, preferred_directors, preferred_era, etc.)
     - Updates user `is_personalized = true`
  
  2. **ComputeRecommendation** (`App\Jobs\ComputeRecommendation`)
     - Chained after ComputePersona completes
     - Reads user genres and computed taste profile
     - Filters candidate movies by user's preferred genres
     - Sends to Flask API via `FlaskService@computeRecommendation`
     - Stores recommendation IDs in `UserRecommendation` table
     - Caches results for 7600 seconds (2.1 hours)
     - Sets `persona_ready = true`
  
  3. **ReevalTriger** (`App\Jobs\ReevalTriger`)
     - Triggered when user activity log reaches 5+ unevaluated entries
     - Re-computes recommendations based on new user behavior
     - Updates `UserRecommendation` table

- **Activity Tracking:** `LogActivityModel`
  - Logged activities: `click`, `favorite`, `watchlist`, `comment`, `search`
  - Automatically triggers re-evaluation after 5+ activities

- **Recommendation Caching:**
  - `user_rec_movie_{user_id}`: User's recommended movie IDs
  - `movie_detail_{movie_id}`: Movie details
  - `user_rec_actor_{user_id}`: Top actors from recommendations
  - `collection_detail_{collection_id}`: Collection details
  - TTL: 7600 seconds (2.1 hours)

### 3.2 Similar Movies Calculation
- **Route:** Used in movie detail page
- **Service:** `DetailService@filterSimilar`
- **Algorithm:**
  1. Get target movie's genres
  2. Filter candidate movies by same genres
  3. Send to Flask API for similarity scoring (using genre vectors)
  4. Return top similar movies
- **Flask Integration:** `FlaskService@getSimilar`

### 3.3 Similar Actors Discovery
- **Route:** Used in actor detail page
- **Service:** `ActorService@getSimilarActors`
- **Algorithm:**
  1. Collect all genres from actor's filmography
  2. Find other actors whose films share genres
  3. Score based on shared genre count
  4. Return top 6 similar actors

---

## 4. WATCHLIST FEATURES

### 4.1 Add to Watchlist
- **Route:** `POST /watchlist/{movie}` → `WatchlistController@store`
- **Parameters:** `movie` = `tmdb_movie_id`
- **Action:** Creates entry in `watchlist` table with `first_or_create`
- **Side Effect:** Logs activity to `LogActivityModel` (type: 'watchlist')
- **Response:** Redirects back to previous page

### 4.2 Remove from Watchlist
- **Route:** `DELETE /watchlist/{movie}` → `WatchlistController@destroy`
- **Parameters:** `movie` = `tmdb_movie_id`
- **Action:** Deletes watchlist entry for current user
- **Response:** Redirects back to previous page

### 4.3 View Watchlist
- **Route:** `GET /watchlist` → `WatchlistController@index`
- **View:** `pages/watchlist.blade.php`
- **Layout:** 
  - Sidebar with navigation to Profile, Settings, Persona, Favorites
  - Main content area displaying watchlist items in grid
  - Each item shows poster with title and rating
  - Click on item navigates to movie detail page
- **Data Source:** `Watchlist::with('movie')->where('user_id', auth()->id())->latest()`
- **Empty State:** Displays "+" button to discover more movies

---

## 5. FAVORITE FEATURES

### 5.1 Add to Favorites
- **Route:** `POST /favorite/{movie}` → `FavoriteController@store`
- **Parameters:** `movie` = `tmdb_movie_id`
- **Action:** Creates entry in `favorites` table with `first_or_create`
- **Flags:** 
  - During personalization: `is_persona = 1`
  - Regular favorite: `is_persona = 0`
- **Side Effect:** Logs activity to `LogActivityModel` (type: 'favorite')
- **Response:** Redirects back to previous page

### 5.2 Remove from Favorites
- **Route:** `DELETE /favorite/{movie}` → `FavoriteController@destroy`
- **Parameters:** `movie` = `tmdb_movie_id`
- **Action:** Deletes favorite entry for current user
- **Response:** Redirects back to previous page

### 5.3 View Favorites
- **Route:** `GET /favorites` → `FavoriteController@index`
- **View:** `pages/favorites.blade.php`
- **Layout:** Same as Watchlist page (sidebar + grid)
- **Data Source:** `Favorite::with('movie')->where('user_id', auth()->id())->latest()`
- **Note:** Includes both `is_persona = 1` (from setup) and `is_persona = 0` (regular favorites)

---

## 6. SEARCH FEATURES

### 6.1 Search Results Page
- **Route:** `GET /search` → `SearchController@index`
- **Query Parameter:** `q` (search query)
- **View:** `pages/search-results.blade.php`
- **Results:**
  - Movies: Top match displayed with details, additional results below
  - Actors: Listed in separate section
  - Both sorted by relevance (title starts with query > contains query)
  - Limit: 20 movies, 10 actors
- **Service:** `SearchService@search`
- **Side Effect:** Logs first movie ID to activity log (type: 'search')

### 6.2 Live Search (Auto-complete)
- **Route:** `GET /search/live` → `SearchController@live` (AJAX)
- **Query Parameter:** `q` (minimum 2 characters)
- **Response:** JSON with movies and actors
- **Format:**
  ```json
  {
    "movies": [{"id": ..., "title": ..., "poster_url": ..., "rating": ..., "url": ...}],
    "actors": [{"id": ..., "name": ..., "image_url": ..., "url": ...}]
  }
  ```
- **Service:** `SearchService@live`
- **Limit:** 5 movies, 5 actors

---

## 7. COLLECTION FEATURES

### 7.1 View Collection Detail
- **Route:** `GET /collection/{id}` → `CollectionController@show`
- **Parameters:** `id` = `tmdb_collection_id`
- **View:** `pages/collection-detail.blade.php`
- **Content:**
  - Collection name as heading
  - Collection overview/description
  - Grid of all movies in collection (5 columns)
  - Each movie card links to detail page
- **Data Source:** `CollectionModel::with('movies.genres.genre')->where('tmdb_collection_id', $id)`

---

## 8. MOVIE DETAIL FEATURES

### 8.1 Movie Detail Page
- **Route:** `GET /movie/detail/{id}` → `DetailController@index`
- **Parameters:** `id` = `tmdb_movie_id`
- **View:** `pages/movie-detail.blade.php` (844 lines)
- **Sections:**
  1. **Hero Section:** Backdrop image, title, overlay gradient
  2. **Poster & Info:** Left side poster with ratings
  3. **Buttons:** Watchlist, Favorite (show current state)
  4. **Movie Details:** Runtime, release date, genres, rating, vote count
  5. **Overview:** Full description
  6. **Cast & Crew:** Listed with links (if enabled)
  7. **Similar Movies:** Grid of recommendations
  8. **Comments Section:** User comments with threading support
  9. **YouTube Video:** Embedded trailer (if available)
  10. **Collections:** Link to related collections

- **Data Gathering:**
  - Movie details: `Movie::where('tmdb_movie_id', $id)->with('genres.genre')`
  - Similar movies: `DetailService@filterSimilar` (Flask-powered)
  - Comments: `CommentService@getCommentsByMovie`
  - Watchlist status: Check `Watchlist` table
  - Favorite status: Check `Favorite` table

- **Relationships Loaded:**
  - Genres with genre metadata
  - Actors (from `movie_actors`)
  - Directors (from `movie_directors`)
  - Collections (from `tmdb_collection_id`)

---

## 9. ACTOR DETAIL FEATURES

### 9.1 Actor Detail Page
- **Route:** `GET /actor/{id}` → `DashboardController@getActorMovie`
- **Parameters:** `id` = `tmdb_actor_id`
- **View:** `pages/actor-detail.blade.php`
- **Sections:**
  1. **Hero Section:** Actor photo, name, metadata
  2. **Biography:** Detailed biography text
  3. **Filmography:** Grid of movies actor appeared in
  4. **Similar Actors:** Recommended similar actors (up to 6)

- **Services:**
  - `ActorService@getActorMovies`: Get actor details with filmography
  - `ActorService@getSimilarActors`: Calculate similar actors based on genre overlap

- **Similar Actors Algorithm:**
  1. Collect all genres from actor's filmography
  2. Query actors in same genres (excluding self)
  3. Score based on shared genre count
  4. Order by score descending
  5. Limit to 6 results

---

## 10. COMMENT FEATURES

### 10.1 Add Comment
- **Route:** `POST /movie/comment` → `CommentController@store`
- **Form Fields:**
  - `movie_id`: Required
  - `content`: Required, max 1000 chars
  - `reply_id`: Optional (for nested replies)
  - `tagged_user_id`: Optional (for mentions)
- **Service:** `CommentService@store`
- **Action:** Creates entry in `comments` table with current user
- **Response:** Redirects back to movie detail page
- **Relationships:** Supports threading (parent-child comments)

### 10.2 Edit Comment
- **Route:** `PUT /comments/{comment}` → `CommentController@update`
- **Authorization:** Only comment owner can edit
- **Form Fields:**
  - `content`: Required, max 1000 chars
- **Service:** `CommentService@update`
- **Response:** Redirects back

### 10.3 Delete Comment
- **Route:** `DELETE /comments/{comment}` → `CommentController@destroy`
- **Authorization:** Only comment owner can delete
- **Cascade:** Deletes all nested replies recursively
- **Service:** `CommentService@destroy`
- **Response:** Redirects back

### 10.4 View Comments
- **Display:** On movie detail page
- **Service:** `CommentService@getCommentsByMovie`
- **Query:**
  ```
  Comment::with(['user', 'replies'])
    ->where('movie_id', $movieId)
    ->whereNull('reply_id')  // Only top-level comments
    ->oldest()
    ->get()
  ```
- **Threading:** Replies displayed under parent comment
- **Display Order:** Oldest first

---

## 11. DISCOVER & FILTER FEATURES

### 11.1 Discover with Filters
- **Route:** `GET /discover` → `DiscoverController@index`
- **View:** `discover.blade.php`
- **Query Parameters:**
  - `genre`: Filter by single genre name
  - `language`: Filter by original language code
- **Response:** Grid display of filtered movies (limit 20)

### 11.2 Advanced Discover Results
- **Route:** `GET /discover/results` → `DiscoverController@results`
- **Form Parameters:**
  - `genres`: Array of genre names
  - `languages`: Array of language codes
- **Service:** `DiscoverService@filterTest`
- **Features:**
  - Caches results for 7 days using MD5 hash of filters
  - Falls back to non-cached query if cache unavailable
  - Supports unlimited genre/language combinations
- **Display:** Same grid as basic discover

### 11.3 Available Filters
- **Genres:** Fetched from `Genre` model (via `DiscoverService@getGenres`)
- **Languages:** Extracted from `Movie.original_language` field with helper (via `DiscoverService@getLanguages`)

---

## 12. TOP CHARTED FEATURES

### 12.1 Top Charted Page
- **Route:** `GET /top_charted` → `TopChartedController@index`
- **View:** `topcharted.blade.php`
- **Content:**
  - Top 10 all-time best movies
  - Top 10 movies grouped by genre (one per genre)
- **Service:** `TopChartedService`
  - `getAllTimeBest(10)`: All-time highest rated movies
  - `getBestMoviesByGenre(10)`: One top movie per genre

---

## 13. ADDITIONAL ROUTES & FEATURES

### 13.1 Activity Logging
- **Route:** `POST /log-activity` → `LogActivityService@click`
- **Purpose:** Track movie clicks and interaction patterns
- **Logged Data:** `tmdb_movie_id`, `user_id`, `type`, timestamp
- **Trigger:** Automatically on most movie interactions
- **Re-eval Check:** After 5+ unevaluated activities, dispatch `ReevalTriger` job

### 13.2 Logout
- **Route:** `POST /logout` → `AuthenticatedSessionController@destroy`
- **Middleware:** `auth`
- **Action:** Invalidates session and regenerates CSRF token

### 13.3 Loading Screen
- **Route:** `GET /persona-loading` → Returns `pages/loading-persona.blade.php`
- **Purpose:** Show loading state while background jobs compute
- **AJAX Status Check:** `GET /persona-status` returns `{"ready": boolean}`

### 13.4 Test Routes
- **`GET /test-redis`:** Verifies Redis cache is working
- **`GET /test-job`:** Verifies queue worker is processing jobs
- **`GET /test-function`:** Dev route for debugging (returns filtered movie data)

---

## 14. AUTHENTICATION FEATURES

### 14.1 Session-Based Authentication
- **Method:** Laravel's built-in session authentication
- **Provider:** `auth` middleware
- **Guest Routes:** Register, Login, Password Reset (protected with `guest` middleware)
- **Verified Routes:** Email verification required on most authenticated routes

### 14.2 Email Verification
- **Trigger:** Automatically sent on registration
- **Route:** `GET /verify-email/{id}/{hash}` → `VerifyEmailController@verify`
- **Middleware:** `signed` (URL signature check)
- **Throttle:** 6 attempts per minute
- **Resend:** `POST /email/verification-notification`

### 14.3 Password Confirmation
- **Route:** `GET /confirm-password` → `ConfirmablePasswordController@show`
- **Route:** `POST /confirm-password` → `ConfirmablePasswordController@store`
- **Purpose:** Confirm identity before sensitive actions

---

## 15. DATABASE MODELS & RELATIONSHIPS

### Core Models:
1. **User** - Authenticated user account
   - Fillable: name, email, is_personalized, password, avatar, gender, dob, bio, phone
   - Relationships: comments(), watchlists(), favorites(), userGenres(), taste()

2. **Movie** - Film data from TMDB
   - Key fields: tmdb_movie_id, title, poster_path, backdrop_path, rating, popularity, release_date, runtime, overview
   - Relationships: genres, actors, directors, normalizedData, genreVector

3. **Watchlist** - User's movies to watch later
   - Fields: user_id, movie_id, created_at

4. **Favorite** - User's favorite movies
   - Fields: user_id, movie_id, is_persona (0 or 1), created_at
   - Note: `is_persona = 1` marks movies selected during personalization

5. **Comment** - User comments on movies
   - Fields: user_id, movie_id, content, reply_id (for threading)
   - Relationships: user, replies (recursive)

6. **UserGenre** - User's preferred genres with weighted scores
   - Fields: user_id, genre_id, weight (computed by ML service)

7. **UserTaste** - User's computed taste profile
   - Fields: 
     - preferred_actors (JSON)
     - preferred_directors (JSON)
     - preferred_era (JSON)
     - preferred_normalized_rating
     - preferred_normalized_popularity
   - Status: persona_ready (boolean)

8. **UserRecommendation** - Computed movie recommendations
   - Fields: user_id, tmdb_movie_id, created_at
   - Purpose: Cache for recommendation results

9. **LogActivityModel** - User interaction tracking
   - Fields: tmdb_movie_id, user_id, type (click/favorite/watchlist/comment/search), is_evaluated
   - Triggers re-evaluation after 5+ unevaluated entries

10. **Actor** - Actor/actress data
    - Fields: tmdb_actor_id, name, image_path, biography, birthday, popularity

11. **CollectionModel** - TMDB collections (e.g., "Avatar", "Fast & Furious")
    - Fields: tmdb_collection_id, name, overview, backdrop_path, poster_path
    - Relationships: movies

---

## 16. SERVICES SUMMARY

| Service | Purpose | Key Methods |
|---------|---------|------------|
| `DashboardService` | Personalized dashboard content | `getMainContent()`, `rankTopByGenre()` |
| `DetailService` | Movie detail & similar movies | `filterSimilar()`, `getSelectedMovie()` |
| `SearchService` | Movie/actor search | `search()`, `live()` |
| `ActorService` | Actor data & discovery | `getActorMovies()`, `getSimilarActors()`, `getActor()` |
| `CommentService` | Comment CRUD | `store()`, `update()`, `destroy()`, `getCommentsByMovie()` |
| `LogActivityService` | Track user interactions | `click()`, `favorite()`, `watchlist()`, `search()`, `comment()` |
| `LandingService` | Landing page content | `getMoviesByGenre()`, `getPopularMovie()` |
| `TopChartedService` | Top-rated content | `getAllTimeBest()`, `getBestMoviesByGenre()` |
| `DiscoverService` | Discovery & filtering | `filterTest()`, `getGenres()`, `getLanguages()` |
| `FlaskService` | Python ML service integration | `ComputeNewTaste()`, `computeRecommendation()`, `getSimilar()` |

---

## 17. JOBS (QUEUE-BASED PROCESSING)

| Job | Trigger | Purpose | Output |
|-----|---------|---------|--------|
| `ComputePersona` | After personalization form | Extract taste profile from user selections | `UserTaste` record, `UserGenre` weights |
| `ComputeRecommendation` | After ComputePersona completes | Generate movie recommendations | `UserRecommendation` records, cache |
| `ReevalTriger` | After 5+ user activities | Re-compute recommendations based on behavior | Updated `UserRecommendation` records |

---

## 18. PARTIALLY IMPLEMENTED / UNUSED FEATURES ⚠️

1. **Email Password Reset** - MOCK ONLY
   - Code path: `ProfileController@updateSettings`
   - Status: Logs message instead of sending email
   - Comment: `"MOCK EMAIL: Permintaan ganti password..."`

2. **Test Routes** - Development only
   - `/test-redis`, `/test-job`, `/test-function`
   - Should be removed before production

3. **Upcoming Movies** - Commented out in `DashboardService`
   - Filtered but never displayed
   - Lines: ~212 in DashboardService

4. **Flask Service Debug** - Some placeholder responses
   - `/test-function` in `DiscoverController` uses `dd()` debug helper

---

## 19. FEATURE USAGE FLOW DIAGRAMS

### User Flow: Landing → Registration → Personalization → Dashboard

```
┌─────────────┐
│ Landing (/) │  ← GET / (public)
└──────┬──────┘
       │ Click Register
       ▼
┌──────────────────┐
│ Register Modal   │  ← POST /register
│ (on landing)     │
└──────┬───────────┘
       │ Creates account
       ▼
┌──────────────────────┐
│ Personalization      │  ← GET /personalization
│ (Step 1: Genres)     │
└──────┬───────────────┘
       │ (Step 2: Movies)
       │ (Step 3: Submit)
       ▼
┌──────────────────┐
│ Loading Screen   │  ← GET /persona-loading
│ (Job processing) │  ← AJAX: GET /persona-status
└──────┬───────────┘
       │ Jobs complete (persona_ready=true)
       ▼
┌──────────────────┐
│ Dashboard        │  ← GET /dashboard
│ (For You + etc)  │
└──────────────────┘
```

### User Flow: Movie Discovery

```
┌─────────────┐
│  Dashboard  │
└──────┬──────┘
       │ 
   ┌───┴────┬──────────┬────────────┐
   │         │          │            │
   ▼         ▼          ▼            ▼
┌──────┐ ┌────────┐ ┌─────────┐ ┌──────────┐
│Click │ │Search  │ │Discover │ │Top       │
│Movie │ │Results │ │& Filter  │ │Charted   │
└──┬───┘ └────┬───┘ └────┬────┘ └───┬──────┘
   │           │         │          │
   └───────┬───┴─────────┴──────────┘
           │
           ▼
┌────────────────────┐
│ Movie Detail Page  │
│ - Info + Cast      │
│ - Similar Movies   │
│ - Comments         │
│ - Watchlist/Fav    │
└────┬───────────────┘
     │
 ┌───┴──────────┬──────────┐
 ▼              ▼          ▼
┌─────────┐ ┌──────────┐ ┌────────┐
│Add to   │ │Add to    │ │View    │
│Watchlist│ │Favorites │ │Actor   │
└─────────┘ └──────────┘ └────┬───┘
                               │
                               ▼
                         ┌──────────────┐
                         │ Actor Detail │
                         │+ Similar     │
                         │  Actors      │
                         └──────────────┘
```

### User Flow: Collections

```
┌────────────────────┐
│ Movie Detail Page  │
│ (Collections Link) │
└────────┬───────────┘
         │
         ▼
┌──────────────────┐
│ Collection Page  │
│ - Name + Overview│
│ - Grid of movies │
└────┬─────────────┘
     │
     │ Click movie
     ▼
┌────────────────┐
│ Movie Detail   │
│ (from collection)
└────────────────┘
```

---

## 20. USER INTERFACE ROUTES SUMMARY

| Route | Method | View | Purpose |
|-------|--------|------|---------|
| `/` | GET | landing.blade.php | Landing page for all users |
| `/dashboard` | GET | dashboard.blade.php | Personalized feed (authenticated) |
| `/personalization` | GET | pages/personalization.blade.php | Initial preference setup |
| `/discover` | GET | discover.blade.php | Browse movies by filters |
| `/top_charted` | GET | topcharted.blade.php | View highest-rated movies |
| `/movie/detail/{id}` | GET | pages/movie-detail.blade.php | Movie detail page |
| `/actor/{id}` | GET | pages/actor-detail.blade.php | Actor profile & filmography |
| `/collection/{id}` | GET | pages/collection-detail.blade.php | Collection/franchise page |
| `/search` | GET | pages/search-results.blade.php | Search results |
| `/watchlist` | GET | pages/watchlist.blade.php | User's watchlist |
| `/favorites` | GET | pages/favorites.blade.php | User's favorite movies |
| `/profile` | GET | profile/edit.blade.php | Edit user profile |
| `/profileUI` | GET | profile/index.blade.php | View profile dashboard |
| `/settings` | GET | profile/settings.blade.php | Account settings |
| `/profile/persona` | GET | profile/persona.blade.php | Manage preferences |

---

## 21. API/AJAX ENDPOINTS

| Route | Method | Purpose | Response |
|-------|--------|---------|----------|
| `/search/live` | GET | Auto-complete search | JSON: {movies[], actors[]} |
| `/persona-status` | GET | Check persona computation | JSON: {ready: boolean} |
| `/log-activity` | POST | Track user interactions | Redirect back |
| `/login` | POST | Authenticate user | Redirect to dashboard |
| `/register` | POST | Create account | Redirect to personalization |
| `/logout` | POST | End session | Redirect to landing |

---

## 22. KEY FEATURE CHECKLIST

### ✅ Fully Implemented & Functional
- [x] User authentication (register, login, email verification)
- [x] User profile management (edit, avatar, settings)
- [x] Personalization setup (genres + movie selection)
- [x] Recommendation system (persona computation + ML ranking)
- [x] Movie detail pages with comprehensive info
- [x] Watchlist management (add, remove, view)
- [x] Favorites management (add, remove, view)
- [x] Comments system with threading
- [x] Search (full + live/autocomplete)
- [x] Actor detail pages with similar actors
- [x] Collection/franchise pages
- [x] Discovery with genre/language filters
- [x] Top charted rankings
- [x] Activity logging & re-evaluation trigger
- [x] Caching system for performance

### ⚠️ Partially Implemented
- [x] Email password reset (MOCK - only logs)
- [x] Flask service integration (real service assumed external)

### ❌ Not Implemented / Not Found
- [ ] Social features (follow users, shared watchlists)
- [ ] Rating system (view ratings yes, submit ratings not found)
- [ ] Notifications (push/email alerts for recommendations)
- [ ] Analytics dashboard for admins
- [ ] Bulk import/export features
- [ ] Advanced filtering (IMDB score range, decade range, etc.)

---

## 23. DATABASE SCHEMA NOTES

### Key Relationships:
```
users
  ├── comments (1→many)
  ├── watchlists (1→many)
  ├── favorites (1→many)
  ├── user_genres (1→many) [with weights]
  └── user_taste (1→1) [computed profile]

movies
  ├── movie_genres (many→many)
  ├── movie_actors (many→many)
  ├── movie_directors (many→many)
  ├── collections (many→1)
  ├── comments (1→many)
  └── normalized_movie_data (1→1)

comments
  └── replies (self-referential many→many via reply_id)
```

---

## 24. CONFIGURATION & DEPENDENCIES

### External Services:
1. **Flask API** - Python ML service for:
   - Persona computation (`ComputeNewTaste`)
   - Recommendation generation (`computeRecommendation`)
   - Similarity scoring (`getSimilar`)

2. **TMDB API** - Data source (assumed populated via seeders/imports)

3. **Redis** - Cache backend & session storage

4. **Queue Worker** - Background job processing

### Laravel Services:
- Mail (mocked for password reset)
- Storage (public disk for avatars)
- Cache (Redis)
- Queue (for background jobs)
- Auth (session-based)

---

## 25. CRITICAL OBSERVATIONS

1. **Persona Status Fields:** Two flags control user flow:
   - `is_personalized`: Set immediately when preferences submitted
   - `persona_ready`: Set after ML computation completes
   - Dashboard checks BOTH before showing recommendations

2. **Cache Strategy:** Heavy use of Redis for:
   - User recommendations (7600 sec / 2.1 hrs)
   - Movie details (7600 sec)
   - Actor lists (7600 sec)
   - Collection details (7600 sec)
   - Discovery filters (604800 sec / 7 days)

3. **Activity Re-evaluation:** System automatically re-ranks recommendations after:
   - 5+ user interactions logged
   - Debounce prevents excessive recalculation

4. **Comment Threading:** Full tree support with recursive delete operations

5. **Watchlist vs Favorites vs Persona Favorites:**
   - `watchlist` table = movies to watch (separate table)
   - `favorites` table with `is_persona=0` = liked movies
   - `favorites` table with `is_persona=1` = personalization selection

---

## 26. RECOMMENDED MANUAL STRUCTURE

Based on the analysis above, here's a logical book structure:

### **Chapter 1: Getting Started**
- What is NextWatch?
- System Requirements
- Account Creation & Email Verification
- First Login

### **Chapter 2: Personalization Setup**
- Why Personalization Matters
- Selecting Preferred Genres (Step 1)
- Choosing Favorite Movies (Step 2)
- Confirming Your Preferences
- What Happens Next (Loading & Processing)

### **Chapter 3: Dashboard & Main Features**
- The Dashboard Layout
- "For You" Section (Recommendations)
- Collections & Suggested Content
- Top Actors
- Search Bar

### **Chapter 4: Discovering Movies**
- Using the Discover Page
- Filtering by Genre
- Filtering by Language
- View Top Charted Movies
- Search for Specific Titles

### **Chapter 5: Movie Details & Actions**
- Movie Detail Page Layout
- Reading Movie Information
- Viewing Similar Movies
- Adding to Watchlist
- Adding to Favorites
- Reading Comments
- Posting Comments & Replies

### **Chapter 6: Watchlist Management**
- Adding Movies to Watchlist
- Viewing Your Watchlist
- Removing from Watchlist
- Organizing Your Watch Later

### **Chapter 7: Favorites Management**
- Adding to Favorites
- Viewing Favorites List
- Difference: Favorites vs Watchlist
- Clearing Favorites

### **Chapter 8: Collections & Franchises**
- What Are Collections?
- Viewing Collection Pages
- Browsing Related Movies

### **Chapter 9: Exploring Actors**
- Clicking on Actor Links
- Viewing Actor Profiles
- Filmography
- Finding Similar Actors

### **Chapter 10: Profile Management**
- Viewing Your Profile
- Profile Statistics (Genre Preferences, Favorite Actors/Directors)
- Editing Profile Information
- Uploading Avatar
- Managing Account Settings
- Changing Password

### **Chapter 11: Recommendation System**
- How Recommendations Work
- Persona & Taste Profile
- Updating Your Preferences
- Re-evaluation Trigger
- Improving Recommendations Over Time

### **Chapter 12: Advanced Search**
- Basic Search
- Live Search (Auto-complete)
- Refining Search Results

### **Chapter 13: Account & Security**
- Password Reset
- Email Verification
- Account Settings
- Privacy Considerations

### **Chapter 14: Tips & Best Practices**
- Getting Better Recommendations
- Organizing Your Collections
- Using Comments Effectively
- Discovery Strategies

### **Appendix A: FAQ**
### **Appendix B: Troubleshooting**
### **Appendix C: Glossary**

---

**End of Audit Report**

*This report is based on source code analysis of the NextWatch Laravel application as of June 6, 2026. All features described are implemented in the current codebase. For questions about specific features, refer to the file paths and line numbers provided throughout this document.*
