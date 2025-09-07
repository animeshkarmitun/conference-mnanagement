<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the backup and restore system
    |
    */

    'storage_path' => env('BACKUP_STORAGE_PATH', 'backups'),
    'encryption_key' => env('BACKUP_ENCRYPTION_KEY'),
    'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Backup Scheduling
    |--------------------------------------------------------------------------
    |
    | Automatic backup scheduling configuration
    |
    */
    
    'schedule' => [
        'daily_time' => env('BACKUP_SCHEDULE_DAILY', '02:00'),
        'hourly_times' => explode(',', env('BACKUP_SCHEDULE_HOURLY', '08:00,14:00,20:00')),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cloud Storage (Optional)
    |--------------------------------------------------------------------------
    |
    | Configuration for cloud backup storage
    |
    */
    
    'cloud' => [
        'provider' => env('BACKUP_CLOUD_PROVIDER', 'aws'),
        'aws' => [
            'bucket' => env('BACKUP_S3_BUCKET'),
            'region' => env('BACKUP_S3_REGION', 'us-east-1'),
            'access_key' => env('BACKUP_S3_ACCESS_KEY'),
            'secret_key' => env('BACKUP_S3_SECRET_KEY'),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Backup Types
    |--------------------------------------------------------------------------
    |
    | Available backup types and their descriptions
    |
    */
    
    'types' => [
        'full' => [
            'name' => 'Full Backup',
            'description' => 'Complete database backup including all tables and data',
            'frequency' => 'daily',
        ],
        'incremental' => [
            'name' => 'Incremental Backup',
            'description' => 'Backup of changes since last backup',
            'frequency' => 'hourly',
        ],
        'differential' => [
            'name' => 'Differential Backup',
            'description' => 'Backup of changes since last full backup',
            'frequency' => 'every_6_hours',
        ],
        'emergency' => [
            'name' => 'Emergency Backup',
            'description' => 'Manual backup triggered for critical moments',
            'frequency' => 'manual',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security configuration for backup operations
    |
    */
    
    'security' => [
        'encrypt_backups' => env('BACKUP_ENCRYPT', true),
        'require_admin' => true,
        'log_all_operations' => true,
        'max_backup_size' => env('BACKUP_MAX_SIZE', '1GB'), // Maximum backup file size
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Email notification configuration for backup operations
    |
    */
    
    'notifications' => [
        'enabled' => env('BACKUP_NOTIFICATIONS', true),
        'email' => env('BACKUP_NOTIFICATION_EMAIL'),
        'on_success' => env('BACKUP_NOTIFY_SUCCESS', false),
        'on_failure' => env('BACKUP_NOTIFY_FAILURE', true),
        'on_cleanup' => env('BACKUP_NOTIFY_CLEANUP', false),
    ],
];
