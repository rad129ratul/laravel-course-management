# 🎓 Course Management System

A comprehensive Laravel-based course creation platform with nested modules, dynamic content management, and file upload capabilities.

## ✨ Features

- **Course Management**: Create, read, update, and delete courses with categories
- **Nested Structure**: Unlimited modules and content items per course
- **File Uploads**: Support for videos (feature + content), images, and documents
- **Dynamic Forms**: Add/remove modules and contents on-the-fly with jQuery
- **Video Integration**: YouTube, Vimeo URLs, or direct video uploads
- **Dark Theme UI**: Modern Bootstrap 5 interface with consistent styling
- **Validation**: Comprehensive frontend and backend validation
- **Repository Pattern**: Clean architecture with separation of concerns
- **Responsive Design**: Mobile-friendly interface

## 🛠️ Technology Stack

**Backend:**
- Laravel 10+ (PHP 8.1+)
- MySQL
- Repository Pattern Architecture
- Form Request Validation

**Frontend:**
- Bootstrap 5.3.2
- jQuery 3.6.4
- Font Awesome 6.4.0
- Custom Dark Theme CSS

**Storage:**
- Local file storage with symbolic links
- Support for videos, images, documents

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 18+ & NPM
- MySQL 8+
- Git

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/rad129ratul/laravel-course-management.git
cd course-management-system
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=course_management
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

### 5. Database Setup

```bash
php artisan migrate
php artisan storage:link
```

### 6. Compile Assets

```bash
npm run dev
```

### 7. Start Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📖 Usage Guide

### Creating a Course

1. Navigate to "Create New Course" button
2. Fill in course details:
   - Title (required)
   - Description (required)
   - Category (required)
   - Feature Video (required, max 50MB)
3. Add modules using "Add Module +" button
4. For each module, add content items with "Add Content +" button
5. Configure content:
   - Video source (YouTube/Vimeo URL or upload)
   - Text content
   - Images (optional, max 2MB)
   - Column positioning
6. Click "Save Course"

### Managing Courses

- **View**: See all course details, modules, and content
- **Edit**: Modify existing courses (preserves existing files)
- **Delete**: Remove courses with confirmation

### Dynamic Features

- Add unlimited modules per course
- Add unlimited content items per module
- Delete modules/content with validation (minimum 1 required)
- Real-time form indexing for nested arrays
- Video source type switching (URL vs Upload)

## 🗄️ Database Schema

**courses**
- id, title, description, category, feature_video_path, timestamps

**modules**
- id, course_id (FK), title, order, timestamps

**contents**
- id, module_id (FK), title, type, content_text, video_url, video_source_type, video_length, video_path, image_path, document_path, column_position, order, timestamps

## 🔒 Security Features

- CSRF protection on all forms
- File type validation (MIME types)
- File size limits enforcement
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Input sanitization on backend

## 🎨 Customization

### Changing Theme Colors

Edit `resources/css/app.css`:

```css
:root {
    --bg-primary: #1a2332;
    --bg-secondary: #2d3748;
    --primary: #0d6efd;
    --danger: #dc3545;
    --success: #198754;
}
```

### Adding Categories

Edit `CourseController.php`:

```php
$categories = ['Programming', 'Design', 'Business', 'Your Category'];
```

### File Upload Limits

Edit `.env`:

```env
MAX_VIDEO_SIZE=51200
MAX_IMAGE_SIZE=2048
MAX_DOCUMENT_SIZE=10240
```

## 🧪 Testing

Run local tests:

```bash
php artisan migrate:fresh
php artisan db:seed CategorySeeder
```

Create test course through UI to verify:
- File uploads working
- Dynamic module/content addition
- Form validation
- Database relationships

## 🐛 Troubleshooting

**Storage symlink error:**
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**File upload fails:**
- Check `php.ini` settings: `upload_max_filesize`, `post_max_size`
- Verify storage permissions
- Check disk space

**JavaScript not working:**
- Clear browser cache
- Check console for errors
- Verify jQuery loaded before custom scripts

**Database connection error:**
- Verify `.env` credentials
- Check database server running
- Test connection with `php artisan migrate`

## 📝 API Endpoints (Optional)

If REST API needed:

```
GET    /api/courses              - List courses
POST   /api/courses              - Create course
GET    /api/courses/{id}         - Show course
PUT    /api/courses/{id}         - Update course
DELETE /api/courses/{id}         - Delete course
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 👤 Contact

ratulrs29@gmail.com

https://github.com/rad129ratul