# Conference Management System - Shared Hosting Deployment Guide

## Prerequisites

Before deploying, ensure your shared hosting provider supports:
- ✅ PHP 8.1 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ Composer (or ability to upload vendor folder)
- ✅ File uploads and directory permissions
- ✅ .htaccess support
- ✅ SSL certificate (recommended)

## Step 1: Prepare Your Application

### 1.1 Generate Application Key
```bash
php artisan key:generate
```

### 1.2 Build Assets (if needed)
```bash
# If you have npm/node available locally
npm run build

# Or manually compile CSS
npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify
```

### 1.3 Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

## Step 2: Database Setup

### 2.1 Create Database
1. Log into your hosting control panel (cPanel, Plesk, etc.)
2. Create a new MySQL database
3. Create a database user with full privileges
4. Note down the database credentials

### 2.2 Update Environment Configuration
1. Copy `production.env` to `.env` on your server
2. Update the following values in `.env`:

```env
APP_NAME="Conference Management System"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_actual_database_name
DB_USERNAME=your_actual_database_username
DB_PASSWORD=your_actual_database_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Conference Management System"
```

## Step 3: Upload Files

### 3.1 File Structure on Server
Upload all files to your `public_html` directory:

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── css/
│   │   └── app.css
│   ├── index.php
│   └── .htaccess
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── composer.lock
```

### 3.2 Important Files to Upload
- ✅ All application files (app/, config/, etc.)
- ✅ `public/` folder contents
- ✅ `vendor/` folder (if Composer not available on server)
- ✅ `storage/` folder with proper permissions
- ✅ `.env` file with production settings

## Step 4: Set Permissions

### 4.1 Directory Permissions
Set the following permissions on your server:
```bash
# Storage directories (755 or 775)
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Make sure web server can write to these directories
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 4.2 File Permissions
```bash
# Application files
chmod 644 .env
chmod 755 artisan
chmod 644 composer.json
```

## Step 5: Database Migration

### 5.1 Run Migrations
If you have SSH access:
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5.2 Alternative: Manual Database Setup
If no SSH access, you can:
1. Export your local database
2. Import it via phpMyAdmin or similar tool
3. Run the SQL files from `database/migrations/` folder

## Step 6: Configure Web Server

### 6.1 .htaccess Configuration
Ensure your `public_html/.htaccess` file contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6.2 Document Root
Make sure your domain points to the `public_html` directory (not a subdirectory).

## Step 7: Test Your Application

### 7.1 Basic Tests
1. Visit your domain: `https://yourdomain.com`
2. Check if the homepage loads
3. Test user registration
4. Test login functionality
5. Check if all pages load correctly

### 7.2 Common Issues & Solutions

**Issue: 500 Internal Server Error**
- Check `.env` file exists and has correct permissions
- Verify database credentials
- Check error logs in hosting control panel

**Issue: Styles not loading**
- Ensure `public/css/app.css` exists
- Check file permissions on CSS files
- Clear browser cache

**Issue: Database connection error**
- Verify database credentials in `.env`
- Check if database server is accessible
- Ensure database user has proper privileges

**Issue: File upload errors**
- Check `storage/` directory permissions
- Verify upload limits in PHP settings
- Check available disk space

## Step 8: Security & Optimization

### 8.1 Security Checklist
- ✅ Set `APP_DEBUG=false` in production
- ✅ Use strong database passwords
- ✅ Enable SSL/HTTPS
- ✅ Set proper file permissions
- ✅ Keep application updated

### 8.2 Performance Optimization
- ✅ Enable gzip compression (if available)
- ✅ Set up caching (if supported)
- ✅ Optimize images
- ✅ Use CDN for static assets (optional)

## Step 9: Backup Strategy

### 9.1 Regular Backups
1. **Database**: Export via phpMyAdmin or hosting control panel
2. **Files**: Download entire `public_html` directory
3. **Configuration**: Backup `.env` file separately

### 9.2 Automated Backups
If your hosting supports cron jobs, set up automated backups:
```bash
# Daily database backup
0 2 * * * mysqldump -u username -p password database_name > backup_$(date +\%Y\%m\%d).sql
```

## Troubleshooting

### Common Error Messages

**"Class 'App\\User' not found"**
- Run: `composer dump-autoload`

**"No application encryption key has been specified"**
- Generate key: `php artisan key:generate`

**"SQLSTATE[HY000] [2002] Connection refused"**
- Check database credentials in `.env`

**"The stream or file could not be opened"**
- Check storage directory permissions

### Getting Help

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check hosting error logs
3. Verify all requirements are met
4. Test with a simple PHP file first

## Post-Deployment

After successful deployment:

1. **Test all features** thoroughly
2. **Set up monitoring** (if available)
3. **Configure email** settings
4. **Set up regular backups**
5. **Monitor performance** and optimize as needed

---

## Quick Deployment Checklist

- [ ] Generate application key
- [ ] Update `.env` with production settings
- [ ] Upload all files to `public_html`
- [ ] Set proper permissions on `storage/` and `bootstrap/cache/`
- [ ] Create and configure database
- [ ] Run migrations and seeders
- [ ] Test application functionality
- [ ] Configure email settings
- [ ] Set up SSL certificate
- [ ] Create backup strategy

Your Conference Management System should now be live on your shared hosting! 🚀


