<?php
/**
 * Server Permissions Fix Script
 * Run this on your live server to fix storage directory permissions
 */

echo "🔧 Fixing Laravel Storage Permissions on Live Server\n";
echo "================================================\n\n";

// Check if we're in Laravel directory
if (!file_exists('artisan')) {
    die("❌ Error: Please run this script from your Laravel root directory on the server.\n");
}

// Create storage directories if they don't exist
$directories = [
    'storage/framework/sessions',
    'storage/framework/views', 
    'storage/framework/cache',
    'storage/logs',
    'storage/app/public',
    'bootstrap/cache'
];

echo "📁 Creating storage directories...\n";
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created: $dir\n";
        } else {
            echo "❌ Failed to create: $dir\n";
        }
    } else {
        echo "✅ Exists: $dir\n";
    }
}

// Set permissions
echo "\n🔐 Setting permissions...\n";
$permissionDirs = [
    'storage',
    'bootstrap/cache'
];

foreach ($permissionDirs as $dir) {
    if (is_dir($dir)) {
        // Try to set permissions (this might not work on all shared hosting)
        if (chmod($dir, 0755)) {
            echo "✅ Set permissions for: $dir\n";
        } else {
            echo "⚠️  Could not set permissions for: $dir (this is normal on some shared hosting)\n";
        }
    }
}

// Create .gitkeep files to ensure directories are tracked
echo "\n📄 Creating .gitkeep files...\n";
$gitkeepDirs = [
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs',
    'storage/app/public'
];

foreach ($gitkeepDirs as $dir) {
    $gitkeepFile = $dir . '/.gitkeep';
    if (!file_exists($gitkeepFile)) {
        if (file_put_contents($gitkeepFile, '') !== false) {
            echo "✅ Created: $gitkeepFile\n";
        }
    }
}

// Test if we can write to storage
echo "\n🧪 Testing storage write permissions...\n";
$testFile = 'storage/framework/sessions/test.txt';
if (file_put_contents($testFile, 'test') !== false) {
    echo "✅ Can write to storage/framework/sessions\n";
    unlink($testFile); // Clean up test file
} else {
    echo "❌ Cannot write to storage/framework/sessions\n";
    echo "   This might be a hosting permission issue.\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. If you still get errors, contact your hosting provider about storage permissions\n";
echo "2. Make sure your .env file has correct database credentials\n";
echo "3. Run: php artisan config:clear\n";
echo "4. Run: php artisan cache:clear\n";
echo "5. Test your application\n\n";

echo "✅ Server permissions fix completed!\n";
?>


