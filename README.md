# 🎓 Course Management System

A simple and powerful Laravel application for creating online courses with nested modules and content. 

## ✨ What It Does

Create complete online courses with:
- **Multiple modules** inside each course
- **Multiple content items** in each module (videos, text, images)
- **Video uploads** or embed from YouTube/Vimeo
- **Beautiful dark theme** interface
- **Add/remove** modules and content on the fly

## 🛠️ Built With

- **Laravel 10** - Backend framework
- **PHP 8.2** - Programming language
- **MySQL** - Database
- **Bootstrap 5.3** - UI framework
- **jQuery 3.7** - Dynamic interactions
- **Font Awesome** - Icons

## 📋 What You Need

Before starting, make sure you have:
- PHP 8.2 or higher
- Composer
- MySQL 8+
- Node.js 18+ & NPM
- A code editor (VS Code recommended)

## 🚀 Getting Started

### 1. Download the Project

```bash
git clone https://github.com/rad129ratul/laravel-course-management.git
cd laravel-course-management
```

### 2. Install Everything

```bash
# Install PHP packages
composer install

# Install JavaScript packages
npm install
```

### 3. Set Up Environment

```bash
# Copy environment file
cp .env.example .env

# Generate security key
php artisan key:generate
```

### 4. Configure Database

Open `.env` file and update these lines:

```env
DB_DATABASE=course_management
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

Create the database in MySQL:
```sql
CREATE DATABASE course_management;
```

### 5. Set Up Database Tables

```bash
# Create tables
php artisan migrate

# Link storage for file uploads
php artisan storage:link
```

### 6. Build Frontend

```bash
# For development
npm run dev

# OR for production
npm run build
```

### 7. Start the App

```bash
php artisan serve
```

Open your browser and go to: **http://localhost:8000**

## 📖 How to Use

### Creating Your First Course

1. Click **"Create New Course"** button
2. Fill in the course information:
   - **Title** - Name of your course
   - **Description** - What the course is about
   - **Category** - Select from dropdown
   - **Feature Video** - Upload a video (max 50MB)

3. Add your first module:
   - Click **"Add Module +"**
   - Enter module title

4. Add content to the module:
   - Click **"Add Content +"** inside the module
   - Enter content title
   - Choose video source:
     - **YouTube/Vimeo** - Paste URL
     - **Upload** - Choose video file
   - Add text description (optional)
   - Upload an image (optional, max 2MB)

5. Click **"Save Course"** when done!

### Managing Courses

- **View All Courses** - See your course list on the homepage
- **View Details** - Click on a course to see all modules and content
- **Edit Course** - Update course information anytime
- **Delete Course** - Remove courses you don't need

### Tips

- You can add as many modules as you want
- Each module can have unlimited content items
- Delete any module or content by clicking the **red X** button
- The system automatically saves the order of your modules

## 🎨 Customizing

### Change Theme Colors

Edit `resources/css/app.css`:

```css
body {
    background-color: #1a2332;  /* Change background */
}

.btn-primary {
    background-color: #0d6efd;  /* Change button color */
}
```

### Add New Categories

Edit `app/Http/Controllers/CourseController.php`:

```php
$categories = [
    'Programming', 
    'Design', 
    'Business', 
    'Marketing',
    'Your New Category'  // Add here
];
```

### Adjust File Size Limits

Edit `.env` file:

```env
MAX_VIDEO_SIZE=51200     # Videos: 50MB
MAX_IMAGE_SIZE=2048      # Images: 2MB
MAX_DOCUMENT_SIZE=10240  # Documents: 10MB
```

## 🚢 Deploying to Railway

This project is ready for Railway deployment:

1. Push your code to GitHub
2. Connect to Railway
3. Add MySQL database service
4. Set environment variables from `.env.example`
5. Railway automatically deploys!

**Live Demo:** https://laravel-course-management-production.up.railway.app

## 🔒 Security

Built with security in mind:
- ✅ CSRF protection on all forms
- ✅ File type validation
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Secure file uploads

## 📊 Database

The app uses three simple tables:

**courses** - Stores course information
**modules** - Stores modules inside courses
**contents** - Stores content inside modules

Everything is connected with proper relationships.

## 🔧 Troubleshooting

### ⚠️ Common Issues

#### 1. "This site can't be reached" or "ERR_CONNECTION_CLOSED"

**Problem:** You're trying to access the site using HTTPS instead of HTTP.

**Solution:** Make sure you're using `http://` (not `https://`) in your browser:

```
✅ Correct:   http://localhost:8000
❌ Incorrect: https://localhost:8000
```

**Why this happens:**
- `php artisan serve` only supports HTTP, not HTTPS
- Your browser might auto-upgrade to HTTPS after visiting the Railway deployment
- Chrome/Edge browsers often force HTTPS for security

**Additional fixes if needed:**

1. **Clear browser cache:**
   - Chrome: Press `Ctrl + Shift + Delete`
   - Select "Cached images and files"
   - Clear data

2. **Force HTTP in Chrome:**
   - Visit `chrome://net-internals/#hsts`
   - Under "Delete domain security policies"
   - Enter: `localhost`
   - Click "Delete"

3. **Use a different port:**
   ```bash
   php artisan serve --port=8080
   ```
   Then access: `http://localhost:8080`

4. **Use Incognito/Private browsing mode** to avoid cached settings

#### 2. Files Not Uploading

**Problem:** Video or image uploads failing.

**Solution:**
- Check `php.ini` file upload limits:
  ```ini
  upload_max_filesize = 100M
  post_max_size = 100M
  max_execution_time = 300
  ```
- Restart PHP server after changing `php.ini`
- Ensure `storage/app/public` has write permissions
- Run `php artisan storage:link` if files don't appear

#### 3. Database Connection Error

**Problem:** "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**
- Make sure MySQL is running
- Check database credentials in `.env` file
- Create the database: `CREATE DATABASE course_management;`
- Test connection: `php artisan migrate:status`

#### 4. 404 Not Found on Routes

**Problem:** All pages show 404 error.

**Solution:**
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

#### 5. Styles Not Loading

**Problem:** Page looks broken, no colors/styling.

**Solution:**
```bash
npm install
npm run build
php artisan config:clear
```

### 📝 Checking Logs

If you encounter errors, check:
```bash
# Laravel error log
tail -f storage/logs/laravel.log

# PHP error log (location varies)
# Windows XAMPP: C:\xampp\apache\logs\error.log
# Mac: /usr/local/var/log/php-fpm.log
```

### 🆘 Still Stuck?

1. Check the Laravel documentation: https://laravel.com/docs
2. Search your error message on Stack Overflow
3. Check the terminal where `php artisan serve` is running for error messages
4. Enable debug mode temporarily: Set `APP_DEBUG=true` in `.env`

## 🤝 Need Help?

If you run into issues:
1. Check the troubleshooting section above
2. Look in `storage/logs/laravel.log` for details
3. Search the error on Google or Stack Overflow
4. Check Laravel documentation

## 📧 Contact

**Email:** ratulrs29@gmail.com  
**GitHub:** https://github.com/rad129ratul

---

**Note:** Always use `http://localhost:8000` for local development, not `https://`