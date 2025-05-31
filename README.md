# Social Media Management System

A Laravel-based social media management system that allows users to create, schedule, and publish posts across multiple platforms (Twitter, Instagram, LinkedIn, Facebook).

## 🚀 Features

- **Multi-platform posting** - Support for Twitter, Instagram, LinkedIn, and Facebook
- **Post scheduling** - Schedule posts for future publication
- **Content validation** - Platform-specific character limits and validation rules
- **User authentication** - Secure authentication using Laravel Sanctum
- **Queue-based publishing** - Background job processing for reliable post publishing
- **Platform management** - Users can toggle active platforms
- **Comprehensive API** - RESTful API endpoints for all functionality

## 📋 Requirements

- PHP 8.1+
- Composer
- Laravel 12
- PHPFilament
- MySQL 8.0+
- Node.js & NPM (for asset compilation)

## 🛠 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/social-media-management.git
cd social-media-management
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit your `.env` file with your database and Redis configuration:


### 5. Database Setup

```bash
# Create database

# Run migrations
php artisan migrate --seed

# Seed the database with platforms and sample data
php artisan db:seed
```

### 6. Queue Worker Setup

```bash
# Start the queue worker (in a separate terminal)
php artisan queue:work

# Or use supervisor for production (recommended)
```

### 7. Scheduler Setup

For post scheduling to work, add this to your crontab:

```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```


To enable scheduled tasks like **automatic post publishing**, Laravel relies on the `php artisan schedule:run` command to be executed every minute.

Since Windows doesn't use `crontab`, follow the steps below to set up a **Windows Task Scheduler** job:

---

### 🪟 Step-by-Step: Configure Task Scheduler on Windows

1. **Open Task Scheduler**
   - Press `Win + S`, search for **"Task Scheduler"**, and open it.

2. **Create a New Task**
   - Click **"Create Task"** (not "Create Basic Task").

3. **General Tab**
   - **Name**: `Laravel Scheduler`
   - ✅ Check: **Run whether user is logged on or not**
   - ✅ Check: **Run with highest privileges**
   - **Configure for**: Your Windows version

4. **Triggers Tab**
   - Click **"New"**
   - **Begin the task**: On a schedule
   - **Settings**: Daily
   - **Repeat task every**: `1 minute`
   - **For a duration of**: `1 day`
   - ✅ Enabled

5. **Actions Tab**
   - Click **"New"**
   - **Action**: Start a program
   - **Program/script**: Path to your `php.exe` (e.g., `C:\xampp\php\php.exe`)
   - **Add arguments**:
     ```
     artisan schedule:run
     ```
   - **Start in (optional)**: Full path to your Laravel project (e.g., `C:\projects\laravel-app`)

6. **Conditions Tab**
   - Uncheck “Start the task only if the computer is on AC power” if you're on a laptop and want it to run on battery.

7. **Settings Tab**
   - ✅ Allow task to be run on demand
   - ✅ If the task fails, restart every 1 minute

8. Click **OK**, enter your admin credentials if prompted.

---

### ✅ Result

The Laravel scheduler will now run **every minute** in the background via Windows Task Scheduler, ensuring that scheduled posts or other queued events are executed on time.

> 📝 You can test it by adding a scheduled log to `app/Console/Kernel.php` and checking `storage/logs/laravel.log` after a few minutes.

---


### 8. Start the Development Server

```bash
composer run dev
```

Your application will be available at `http://localhost:8000`

## 📚 API Documentation

## 📘 API Documentation

This project provides a RESTful API for user authentication, managing posts, and platform configurations.

All protected routes require authentication via Laravel Sanctum.

---

### 🔐 Authentication Endpoints

| Method | Endpoint        | Description              |
|--------|------------------|--------------------------|
| POST   | `/api/register`  | Register a new user      |
| POST   | `/api/login`     | Login user and receive token |
| POST   | `/api/logout`    | Logout user (invalidate token) |
| GET    | `/api/user`      | Get authenticated user profile |
| PUT    | `/api/user`      | Update user profile      |

---

### 📝 Post Endpoints

| Method | Endpoint            | Description                         |
|--------|---------------------|-------------------------------------|
| GET    | `/api/posts`        | Get authenticated user's posts with optional filters |
| POST   | `/api/posts`        | Create a new post (rate-limited)    |
| GET    | `/api/posts/{id}`   | Get details of a specific post      |
| PUT    | `/api/posts/{id}`   | Update a specific post              |
| DELETE | `/api/posts/{id}`   | Delete a specific post              |

> 🛡️ `POST /api/posts` is protected by a custom `PostRateLimit` middleware.

---

### ⚙️ Platform Endpoints

| Method | Endpoint                        | Description                          |
|--------|----------------------------------|--------------------------------------|
| GET    | `/api/platforms`                | List all available platforms         |
| POST   | `/api/platforms/{id}/toggle`    | Toggle the active status of a platform |

---

> 🔒 All routes (except `/login` and `/register`) require authentication using Laravel Sanctum.



## 🎯 Approach and Design Decisions

### Architecture Approach


**1. Queue-Based Processing**
- Used Laravel Jobs for background post publishing
- Prevents timeouts and provides better user experience

- Scalable for high-volume posting

**2. Flexible Platform System**
- Platforms are stored in database with JSON validation rules
- Easy to add new platforms without code changes
- Platform-specific validation logic is extensible
- Users can toggle platforms on/off individually

### Key Trade-offs


**1. Character Limits in Database vs Hard-coded**
- **Decision**: Store validation rules as JSON in platform table
- **Reason**: Flexibility to adjust limits without deployments
- **Trade-off**: Slightly more complex validation logic
- **Benefit**: Platform limits can be updated by admins


**2. Scheduled vs Immediate Publishing**
- **Decision**: Support both scheduled and immediate posting
- **Reason**: Common requirement for social media management
- **Trade-off**: More complex status management (draft/scheduled/published)
- **Benefit**: Full-featured scheduling system

### Security Measures

- Laravel Sanctum for API authentication
- Authorization policies for resource access
- Mass assignment protection with fillable arrays


### Error Handling Strategy

- Graceful failure handling in publishing service
- Detailed error messages stored in pivot table
- HTTP status codes follow REST conventions
- Validation errors returned in consistent format

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage


```
