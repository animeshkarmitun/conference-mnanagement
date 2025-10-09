<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Email;
use App\Models\PasswordlessLogin;

$email = Email::where('email_type', 'passwordless_login')->latest()->first();

if ($email) {
    echo "Email ID: " . $email->id . "\n";
    echo "Related model type: " . $email->related_model_type . "\n";
    echo "Related model ID: " . $email->related_model_id . "\n";
    
    // Test if the class exists and can be instantiated
    $className = $email->related_model_type;
    echo "Class name: " . $className . "\n";
    echo "Class exists: " . (class_exists($className) ? 'Yes' : 'No') . "\n";
    
    if (class_exists($className)) {
        // Try to find the model directly
        $model = $className::find($email->related_model_id);
        if ($model) {
            echo "Model found directly: " . get_class($model) . " (ID: " . $model->id . ")\n";
        } else {
            echo "Model not found directly\n";
        }
    }
    
    // Test the morphTo relationship by checking the actual query
    try {
        $query = $email->relatedModel();
        echo "Query built successfully\n";
        
        // Try to execute the query
        $result = $query->first();
        if ($result) {
            echo "Query result: " . get_class($result) . " (ID: " . $result->id . ")\n";
        } else {
            echo "Query returned null\n";
        }
    } catch (Exception $e) {
        echo "Error with morphTo query: " . $e->getMessage() . "\n";
    }
} else {
    echo "No passwordless login emails found\n";
}


