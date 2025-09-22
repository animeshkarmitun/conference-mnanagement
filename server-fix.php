<?php
/**
 * EMERGENCY SERVER FIX
 * Upload this file to your server and run it via browser
 * This will fix the storage directory issue
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Laravel Storage Directory Fix</h2>";
echo "<p>Fixing storage permissions for your Conference Management System...</p>";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("<p style='color: red;'>❌ Error: Please upload this file to your Laravel root directory (where artisan file is located)</p>");
}

echo "<p>✅ Found Laravel installation</p>";

// Create all required directories
$directories = [
    'storage',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs',
    'storage/app',
    'storage/app/public',
    'bootstrap',
    'bootstrap/cache'
];

echo "<h3>📁 Creating directories...</h3>";
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p style='color: green;'>✅ Created: $dir</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create: $dir</p>";
        }
    } else {
        echo "<p style='color: blue;'>✅ Already exists: $dir</p>";
    }
}

// Create .gitkeep files
echo "<h3>📄 Creating .gitkeep files...</h3>";
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
            echo "<p style='color: green;'>✅ Created: $gitkeepFile</p>";
        }
    }
}

// Test write permissions
echo "<h3>🧪 Testing write permissions...</h3>";
$testFile = 'storage/framework/sessions/test.txt';
if (file_put_contents($testFile, 'test') !== false) {
    echo "<p style='color: green;'>✅ Can write to storage/framework/sessions</p>";
    unlink($testFile); // Clean up
} else {
    echo "<p style='color: red;'>❌ Cannot write to storage/framework/sessions</p>";
    echo "<p style='color: orange;'>⚠️ You may need to contact your hosting provider about file permissions</p>";
}

// Try to set permissions (may not work on all shared hosting)
echo "<h3>🔐 Setting permissions...</h3>";
$permissionDirs = ['storage', 'bootstrap/cache'];
foreach ($permissionDirs as $dir) {
    if (is_dir($dir)) {
        if (chmod($dir, 0755)) {
            echo "<p style='color: green;'>✅ Set permissions for: $dir</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Could not set permissions for: $dir (normal on some shared hosting)</p>";
        }
    }
}

echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li>Delete this file (server-fix.php) after running it</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Visit your main site: <a href='./' target='_blank'>Go to your site</a></li>";
echo "<li>If still having issues, try the database session fix below</li>";
echo "</ol>";

echo "<h3>🔄 Alternative: Database Sessions</h3>";
echo "<p>If file permissions still don't work, you can use database sessions instead:</p>";
echo "<ol>";
echo "<li>Add this to your .env file: <code>SESSION_DRIVER=database</code></li>";
echo "<li>Run: <code>php artisan session:table</code></li>";
echo "<li>Run: <code>php artisan migrate</code></li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>✅ Fix completed!</strong> Your Laravel application should now work properly.</p>";
echo "<p><em>Generated at: " . date('Y-m-d H:i:s') . "</em></p>";
?>


