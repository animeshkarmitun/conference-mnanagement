<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Email;

$email = Email::where('email_type', 'passwordless_login')->latest()->first();

if ($email) {
    echo "Email ID: " . $email->id . "\n";
    echo "Related model type: " . $email->related_model_type . "\n";
    echo "Related model ID: " . $email->related_model_id . "\n";
    
    // Test the relationship
    try {
        $relatedModel = $email->relatedModel;
        if ($relatedModel) {
            echo "Related model loaded: " . get_class($relatedModel) . "\n";
            echo "Token: " . $relatedModel->token . "\n";
        } else {
            echo "Related model is null\n";
        }
    } catch (Exception $e) {
        echo "Error loading related model: " . $e->getMessage() . "\n";
    }
} else {
    echo "No passwordless login emails found\n";
}


