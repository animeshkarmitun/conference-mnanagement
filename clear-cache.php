<?php
/**
 * Clear Laravel Cache Script
 * Run this on your live server to clear all caches
 */

echo "<h2>🧹 Laravel Cache Clear Script</h2>";
echo "<p>Clearing all Laravel caches on your server...</p>";

// Check if we're in Laravel directory
if (!file_exists('artisan')) {
    die("<p style='color: red;'>❌ Error: Please upload this file to your Laravel root directory (where artisan file is located)</p>");
}

echo "<p>✅ Found Laravel installation</p>";

// Function to run artisan commands
function runArtisanCommand($command) {
    $output = [];
    $return_var = 0;
    exec("php artisan $command 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

echo "<h3>🧹 Clearing caches...</h3>";

// Clear configuration cache
echo "<p>Clearing configuration cache...</p>";
$result = runArtisanCommand('config:clear');
if ($result['return'] === 0) {
    echo "<p style='color: green;'>✅ Configuration cache cleared</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to clear config cache: " . implode('<br>', $result['output']) . "</p>";
}

// Clear application cache
echo "<p>Clearing application cache...</p>";
$result = runArtisanCommand('cache:clear');
if ($result['return'] === 0) {
    echo "<p style='color: green;'>✅ Application cache cleared</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to clear app cache: " . implode('<br>', $result['output']) . "</p>";
}

// Clear view cache
echo "<p>Clearing view cache...</p>";
$result = runArtisanCommand('view:clear');
if ($result['return'] === 0) {
    echo "<p style='color: green;'>✅ View cache cleared</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to clear view cache: " . implode('<br>', $result['output']) . "</p>";
}

// Clear route cache
echo "<p>Clearing route cache...</p>";
$result = runArtisanCommand('route:clear');
if ($result['return'] === 0) {
    echo "<p style='color: green;'>✅ Route cache cleared</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to clear route cache: " . implode('<br>', $result['output']) . "</p>";
}

// Clear compiled services
echo "<p>Clearing compiled services...</p>";
$result = runArtisanCommand('clear-compiled');
if ($result['return'] === 0) {
    echo "<p style='color: green;'>✅ Compiled services cleared</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to clear compiled services: " . implode('<br>', $result['output']) . "</p>";
}

// Manual cache file deletion
echo "<h3>🗑️ Manual cache file deletion...</h3>";

$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/routes.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<p style='color: green;'>✅ Deleted: $file</p>";
        } else {
            echo "<p style='color: red;'>❌ Could not delete: $file</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Not found: $file</p>";
    }
}

// Clear storage/framework/cache
echo "<p>Clearing storage/framework/cache...</p>";
$cacheDir = 'storage/framework/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "<p style='color: green;'>✅ Storage cache cleared</p>";
}

// Clear storage/framework/sessions (old sessions)
echo "<p>Clearing old session files...</p>";
$sessionDir = 'storage/framework/sessions';
if (is_dir($sessionDir)) {
    $files = glob($sessionDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitkeep') {
            unlink($file);
        }
    }
    echo "<p style='color: green;'>✅ Old sessions cleared</p>";
}

echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li>Delete this file (clear-cache.php) after running it</li>";
echo "<li>Clear your browser cache (Ctrl+F5)</li>";
echo "<li>Visit your main site: <a href='./' target='_blank'>Go to your site</a></li>";
echo "<li>If still having issues, run the server-fix.php script first</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>✅ Cache clearing completed!</strong></p>";
echo "<p><em>Generated at: " . date('Y-m-d H:i:s') . "</em></p>";
?>


