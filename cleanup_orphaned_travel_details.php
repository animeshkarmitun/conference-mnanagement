<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Cleaning up orphaned travel details...\n";
echo "=====================================\n\n";

// Find travel details with non-existent participants
$orphanedTravelDetails = \App\Models\TravelDetail::whereNotNull('participant_id')
    ->whereDoesntHave('participant')
    ->get();

echo "Found " . $orphanedTravelDetails->count() . " orphaned travel detail records:\n";

foreach ($orphanedTravelDetails as $detail) {
    echo "ID: {$detail->id}, participant_id: {$detail->participant_id}\n";
}

if ($orphanedTravelDetails->count() > 0) {
    echo "\nDo you want to delete these orphaned records? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 'y') {
        $deletedCount = 0;
        foreach ($orphanedTravelDetails as $detail) {
            $detail->delete();
            $deletedCount++;
            echo "Deleted travel detail ID: {$detail->id}\n";
        }
        echo "\nSuccessfully deleted {$deletedCount} orphaned travel detail records.\n";
    } else {
        echo "Cleanup cancelled.\n";
    }
} else {
    echo "No orphaned travel detail records found.\n";
}

echo "\nCleanup completed.\n";






