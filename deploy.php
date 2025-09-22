<?php
/**
 * Simple deployment script for shared hosting
 * Run this script after uploading files to set up the application
 */

echo "🚀 Conference Management System - Deployment Script\n";
echo "================================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("❌ Error: artisan file not found. Please run this script from the Laravel root directory.\n");
}

// Check if .env exists
if (!file_exists('.env')) {
    echo "⚠️  Warning: .env file not found. Please create it from production.env template.\n";
    echo "   Copy production.env to .env and update the database credentials.\n\n";
} else {
    echo "✅ .env file found\n";
}

// Check storage directory permissions
$storageDir = 'storage';
if (!is_writable($storageDir)) {
    echo "⚠️  Warning: storage directory is not writable. Please set permissions:\n";
    echo "   chmod -R 755 storage/\n";
    echo "   chmod -R 755 bootstrap/cache/\n\n";
} else {
    echo "✅ storage directory is writable\n";
}

// Check if vendor directory exists
if (!is_dir('vendor')) {
    echo "⚠️  Warning: vendor directory not found. Please upload it or run 'composer install'\n\n";
} else {
    echo "✅ vendor directory found\n";
}

// Check if CSS file exists
if (!file_exists('public/css/app.css')) {
    echo "⚠️  Warning: public/css/app.css not found. Please build assets:\n";
    echo "   npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify\n\n";
} else {
    echo "✅ CSS file found\n";
}

echo "\n📋 Next Steps:\n";
echo "1. Update .env file with your database credentials\n";
echo "2. Set proper permissions: chmod -R 755 storage/ bootstrap/cache/\n";
echo "3. Run migrations: php artisan migrate --force\n";
echo "4. Run seeders: php artisan db:seed --force\n";
echo "5. Test your application at your domain\n\n";

echo "🎉 Deployment script completed!\n";
echo "For detailed instructions, see SHARED_HOSTING_DEPLOYMENT.md\n";
?>


