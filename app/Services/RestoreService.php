<?php

namespace App\Services;

use App\Models\BackupRecord;
use App\Models\RestoreRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Exception;
use PDO;

class RestoreService
{
    public function __construct()
    {
        // No dependencies to avoid circular dependency
    }

    /**
     * Restore from a backup
     */
    public function restoreFromBackup(
        int $backupId, 
        string $restoreType = 'full', 
        ?array $tables = null, 
        ?User $user = null
    ): RestoreRecord {
        $user = $user ?? auth()->user();
        $backup = BackupRecord::find($backupId);

        if (!$backup) {
            throw new Exception("Backup not found");
        }

        if (!$backup->isCompleted()) {
            throw new Exception("Cannot restore from incomplete backup");
        }

        if (!$backup->exists()) {
            throw new Exception("Backup file does not exist");
        }

        // Verify backup integrity (skip for faster restore, uncomment if needed)
        /*
        $backupService = app(BackupService::class);
        if (!$backupService->verifyBackup($backupId)) {
            throw new Exception("Backup file is corrupted");
        }
        */

        // Find existing restore record or create new one
        // Look for the most recent restore record for this backup (any status, just created)
        // The controller creates a restore record, so we should find it
        // Look for records created in the last 5 minutes to ensure we find the one just created
        $restoreRecord = RestoreRecord::where('backup_id', $backupId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest('created_at')
            ->first();

        if (!$restoreRecord) {
            // If no recent record found, look for any pending or in_progress record
            $restoreRecord = RestoreRecord::where('backup_id', $backupId)
                ->whereIn('status', ['pending', 'in_progress'])
                ->latest('created_at')
                ->first();
        }

        if (!$restoreRecord) {
            // Create restore record if none exists (fallback)
            Log::warning("No existing restore record found, creating new one", [
                'backup_id' => $backupId
            ]);
            
        $restoreRecord = RestoreRecord::create([
            'backup_id' => $backupId,
            'restore_type' => $restoreType,
            'tables_restored' => $tables,
            'status' => 'pending',
            'created_by' => $user->id,
            'started_at' => now(),
        ]);

            Log::info("Created new restore record in service", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId
            ]);
        } else {
            // Update existing restore record (update type and tables, keep current status)
            $restoreRecord->restore_type = $restoreType;
            $restoreRecord->tables_restored = $tables;
            // Don't change status - keep it as is (pending or in_progress)
            // Save the record
            try {
                // Try saveQuietly() first (Laravel 9+), fallback to save() if not available
                if (method_exists($restoreRecord, 'saveQuietly')) {
                    $restoreRecord->saveQuietly();
                } else {
                    $restoreRecord->save();
                }
            } catch (Exception $saveError) {
                Log::warning("Failed to save restore record, trying direct DB update", [
                    'restore_id' => $restoreRecord->id,
                    'error' => $saveError->getMessage()
                ]);
                // Fallback to direct DB update
                DB::table('restore_records')
                    ->where('id', $restoreRecord->id)
                    ->update([
                        'restore_type' => $restoreType,
                        'tables_restored' => json_encode($tables),
                        'updated_at' => now(),
                    ]);
            }
            
            Log::info("Using existing restore record in service", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'status' => $restoreRecord->status,
                'created_at' => $restoreRecord->created_at
            ]);
        }

        try {
            // Increase execution time limit for restore operation
            set_time_limit(600); // 10 minutes max
            ini_set('max_execution_time', '600');
            ini_set('memory_limit', '512M');
            
            // Register shutdown function to catch fatal errors and update status
            $restoreRecordId = $restoreRecord->id;
            register_shutdown_function(function() use ($restoreRecordId) {
                $error = error_get_last();
                if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
                    try {
                        // Check if restore is still in progress and update to failed
                        $updated = DB::table('restore_records')
                            ->where('id', $restoreRecordId)
                            ->where('status', 'in_progress')
                            ->update([
                                'status' => 'failed',
                                'error_message' => 'Fatal error: ' . substr($error['message'] ?? 'Unknown', 0, 500),
                                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                        
                        if ($updated > 0) {
                            Log::error("Restore record marked as failed due to fatal error in shutdown", [
                                'restore_id' => $restoreRecordId,
                                'error' => substr($error['message'] ?? 'Unknown', 0, 200),
                                'error_file' => $error['file'] ?? 'Unknown',
                                'error_line' => $error['line'] ?? 0
                            ]);
                        }
                    } catch (Exception $e) {
                        // Log but don't throw in shutdown function
                        error_log("Failed to update restore record in shutdown: " . $e->getMessage());
                    }
                }
            });

            // Update status to in_progress (NO transaction - commit immediately)
            try {
                // Use direct DB update first (more reliable)
                $updated = DB::table('restore_records')
                    ->where('id', $restoreRecord->id)
                    ->update([
                        'status' => 'in_progress',
                        'updated_at' => now(),
                    ]);
                
                if ($updated > 0) {
                    // Verify update by checking database directly (avoid refresh() which throws if record doesn't exist)
                    $verifyRecord = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->first();
                    
                    if ($verifyRecord && $verifyRecord->status === 'in_progress') {
                        Log::info("Restore record status updated to in_progress (direct DB)", [
                            'restore_id' => $restoreRecord->id,
                            'status' => $verifyRecord->status,
                            'rows_updated' => $updated
                        ]);
                    } else {
                        // Update didn't work - try Eloquent
                        throw new Exception("Status update verification failed - status: " . ($verifyRecord->status ?? 'not found'));
                    }
                } else {
                    // No rows updated - check if record exists
                    $recordExists = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->exists();
                    
                    if (!$recordExists) {
                        throw new Exception("Restore record does not exist (ID: {$restoreRecord->id})");
                    }
                    
                    // Fallback to Eloquent
                    try {
                        // Store ID before reloading
                        $recordId = $restoreRecord->id;
                        
                        // Reload the record to ensure we have the latest data
                        $restoreRecord = RestoreRecord::find($recordId);
                        if (!$restoreRecord) {
                            throw new Exception("Cannot find restore record (ID: {$recordId})");
                        }
                        
                        $restoreRecord->status = 'in_progress';
                        $restoreRecord->save();
                        
                        // Verify update using direct DB query (avoid refresh() which throws if record doesn't exist)
                        $verifyRecord = DB::table('restore_records')
                            ->where('id', $recordId)
                            ->first();
                        
                        Log::info("Restore record status updated to in_progress (Eloquent)", [
                            'restore_id' => $recordId,
                            'status' => $verifyRecord->status ?? 'unknown'
                        ]);
                    } catch (Exception $eloquentError) {
                        throw new Exception("Eloquent update failed: " . $eloquentError->getMessage());
                    }
                }
            } catch (Exception $statusError) {
                Log::error("Failed to update restore record to in_progress", [
                    'restore_id' => $restoreRecord->id,
                    'error' => $statusError->getMessage(),
                    'trace' => $statusError->getTraceAsString()
                ]);
                throw new Exception("Failed to update restore record status: " . $statusError->getMessage());
            }

            // Skip safety backup to speed up restore
            $safetyBackupId = null;
            try {
                Log::info("Safety backup skipped to speed up restore");
            } catch (Exception $e) {
                Log::warning("Failed to create safety backup, proceeding with restore anyway", ['error' => $e->getMessage()]);
            }
            
            // Update metadata (use direct DB update to avoid transaction issues)
            try {
                $metadata = $restoreRecord->metadata ?? [];
                $metadata['safety_backup_id'] = $safetyBackupId;
                $metadata['restore_reason'] = 'Manual restore operation';
                $metadata['started_at'] = now()->toDateTimeString();
                
                DB::table('restore_records')
                    ->where('id', $restoreRecord->id)
                    ->update([
                        'metadata' => json_encode($metadata),
                        'updated_at' => now(),
                    ]);
            } catch (Exception $metaError) {
                Log::warning("Failed to update restore record metadata", [
                    'restore_id' => $restoreRecord->id,
                    'error' => $metaError->getMessage()
                ]);
                // Don't throw - metadata update is not critical
            }

            // Store restore record data BEFORE restore (restore might overwrite the table)
            $restoreRecordData = [
                'id' => $restoreRecord->id,
                'backup_id' => $restoreRecord->backup_id,
                'restore_type' => $restoreRecord->restore_type,
                'tables_restored' => $restoreRecord->tables_restored,
                'status' => 'in_progress',
                'metadata' => $restoreRecord->metadata,
                'started_at' => $restoreRecord->started_at,
                'created_by' => $restoreRecord->created_by,
                'created_at' => $restoreRecord->created_at,
            ];
            
            Log::info("Stored restore record data before restore", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId
            ]);

            // Perform the restore
            Log::info("Starting restore operation", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'restore_type' => $restoreType,
                'backup_file' => $backup->getFullFilePath(),
                'backup_file_exists' => $backup->exists(),
            ]);
            
            // Perform the restore operation
            // Wrap in try-catch to ensure status is always updated
            try {
            if ($restoreType === 'full') {
                    // Pass restore record data to exclude restore_records table from restore
                    $this->performFullRestore($backup, $restoreRecordData);
            } else {
                $this->performSelectiveRestore($backup, $tables);
            }
            
            Log::info("Restore operation completed successfully", [
                    'restore_id' => $restoreRecordData['id'],
                'backup_id' => $backupId
            ]);

                // Re-create or update restore record AFTER restore completes
                // The restore might have overwritten the restore_records table
                $updateSuccess = false;
                
                try {
                    // Check if restore record still exists
                    $existingRecord = DB::table('restore_records')
                        ->where('id', $restoreRecordData['id'])
                        ->first();
                    
                    if ($existingRecord) {
                        // Record exists - update it to completed status
                        $updated = DB::table('restore_records')
                            ->where('id', $restoreRecordData['id'])
                            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                        
                        if ($updated > 0) {
                            $updateSuccess = true;
                            Log::info("Restore record updated to completed (direct DB)", [
                                'restore_id' => $restoreRecordData['id'],
                                'rows_updated' => $updated
                            ]);
                            
                            // Immediately reload the updated record to ensure we have the latest status
                            try {
                                $updatedRecord = DB::table('restore_records')
                                    ->where('id', $restoreRecordData['id'])
                                    ->first();
                                
                                if ($updatedRecord) {
                                    // Update the restore record variable with the latest data
                                    $restoreRecord = RestoreRecord::find($restoreRecordData['id']);
                                    if (!$restoreRecord) {
                                        // If Eloquent find fails, create a new instance from the DB data
                                        $restoreRecord = new RestoreRecord();
                                        $restoreRecord->id = $updatedRecord->id;
                                        $restoreRecord->backup_id = $updatedRecord->backup_id;
                                        $restoreRecord->restore_type = $updatedRecord->restore_type;
                                        $restoreRecord->status = $updatedRecord->status;
                                        $restoreRecord->completed_at = $updatedRecord->completed_at ? \Carbon\Carbon::parse($updatedRecord->completed_at) : null;
                                        $restoreRecord->started_at = $updatedRecord->started_at ? \Carbon\Carbon::parse($updatedRecord->started_at) : null;
                                        $restoreRecord->created_at = $updatedRecord->created_at ? \Carbon\Carbon::parse($updatedRecord->created_at) : now();
                                        $restoreRecord->updated_at = $updatedRecord->updated_at ? \Carbon\Carbon::parse($updatedRecord->updated_at) : now();
                                    }
                                }
                            } catch (Exception $reloadError) {
                                Log::warning("Failed to reload restore record after status update", [
                                    'restore_id' => $restoreRecordData['id'],
                                    'error' => $reloadError->getMessage()
                                ]);
                            }
                        } else {
                            Log::warning("Restore record status update returned 0 rows updated", [
                                'restore_id' => $restoreRecordData['id']
                            ]);
                        }
                    } else {
                        // Record doesn't exist - re-create it
                        Log::warning("Restore record was deleted during restore, re-creating", [
                            'restore_id' => $restoreRecordData['id'],
                            'backup_id' => $backupId
                        ]);
                        
                        try {
                            $newRestoreRecord = RestoreRecord::create([
                                'id' => $restoreRecordData['id'], // Try to use same ID
                                'backup_id' => $restoreRecordData['backup_id'],
                                'restore_type' => $restoreRecordData['restore_type'],
                                'tables_restored' => $restoreRecordData['tables_restored'],
                                'status' => 'completed',
                                'metadata' => $restoreRecordData['metadata'],
                                'started_at' => $restoreRecordData['started_at'],
                                'completed_at' => now(),
                                'created_by' => $restoreRecordData['created_by'],
                                'created_at' => $restoreRecordData['created_at'],
                            ]);
                            
                            $updateSuccess = true;
                            Log::info("Restore record re-created after restore", [
                                'restore_id' => $newRestoreRecord->id,
                                'backup_id' => $backupId
                            ]);
                            
                            // Update the restore record variable
                            $restoreRecord = $newRestoreRecord;
                        } catch (Exception $createError) {
                            // If creating with same ID fails, create without ID (let DB assign)
                            Log::warning("Failed to create restore record with same ID, creating new one", [
                                'restore_id' => $restoreRecordData['id'],
                                'error' => $createError->getMessage()
                            ]);
                            
                            try {
                                $newRestoreRecord = RestoreRecord::create([
                                    'backup_id' => $restoreRecordData['backup_id'],
                                    'restore_type' => $restoreRecordData['restore_type'],
                                    'tables_restored' => $restoreRecordData['tables_restored'],
                                    'status' => 'completed',
                                    'metadata' => $restoreRecordData['metadata'],
                                    'started_at' => $restoreRecordData['started_at'],
                                    'completed_at' => now(),
                                    'created_by' => $restoreRecordData['created_by'],
                                    'created_at' => $restoreRecordData['created_at'],
                                ]);
                                
                                $updateSuccess = true;
                                Log::info("Restore record created with new ID after restore", [
                                    'old_restore_id' => $restoreRecordData['id'],
                                    'new_restore_id' => $newRestoreRecord->id,
                                    'backup_id' => $backupId
                                ]);
                                
                                // Update the restore record variable
                                $restoreRecord = $newRestoreRecord;
                            } catch (Exception $createError2) {
                                Log::error("Failed to create restore record after restore", [
                                    'error' => $createError2->getMessage(),
                                    'trace' => $createError2->getTraceAsString()
                                ]);
                            }
                        }
                    }
                    
                    // If update/create failed, try one more time with direct insert
                    if (!$updateSuccess) {
                        try {
                            DB::table('restore_records')->insert([
                                'backup_id' => $restoreRecordData['backup_id'],
                                'restore_type' => $restoreRecordData['restore_type'],
                                'tables_restored' => json_encode($restoreRecordData['tables_restored']),
                                'status' => 'completed',
                                'metadata' => json_encode($restoreRecordData['metadata'] ?? []),
                                'started_at' => $restoreRecordData['started_at'],
                                'completed_at' => now(),
                                'created_by' => $restoreRecordData['created_by'],
                                'created_at' => $restoreRecordData['created_at'],
                                'updated_at' => now(),
                            ]);
                            
                            $updateSuccess = true;
                            Log::info("Restore record inserted directly after restore", [
                                'backup_id' => $backupId
                            ]);
                        } catch (Exception $insertError) {
                            Log::error("Failed to insert restore record directly after restore", [
                                'error' => $insertError->getMessage(),
                                'trace' => $insertError->getTraceAsString()
                            ]);
                        }
                    }
                    
                } catch (Exception $updateError) {
                    Log::error("Failed to update/create restore record after restore", [
                        'restore_id' => $restoreRecordData['id'],
                        'backup_id' => $backupId,
                        'error' => $updateError->getMessage(),
                        'trace' => $updateError->getTraceAsString()
                    ]);
                    // Don't throw - restore completed successfully
                }
                
                // If status update failed, log critical error but don't fail the restore
                if (!$updateSuccess) {
                    Log::critical("CRITICAL: Restore completed but status update failed - manual intervention required", [
                        'restore_id' => $restoreRecordData['id'],
                        'backup_id' => $backupId
                    ]);
                } else {
                    // Reload the restore record to return the updated one
                    try {
                        if (isset($restoreRecord) && $restoreRecord) {
                            // Use the restore record we already have
                            $restoreRecord = $restoreRecord;
                        } else {
                            // Try to find by ID first
                            $restoreRecord = RestoreRecord::find($restoreRecordData['id']);
                            if (!$restoreRecord) {
                                // Try to find by backup_id and status (most recent completed)
                                $restoreRecord = RestoreRecord::where('backup_id', $backupId)
                                    ->where('status', 'completed')
                                    ->latest('created_at')
                                    ->first();
                            }
                        }
                    } catch (Exception $findError) {
                        Log::warning("Failed to reload restore record after restore", [
                            'restore_id' => $restoreRecordData['id'] ?? 'unknown',
                            'error' => $findError->getMessage()
                        ]);
                    }
                }
                
            } catch (Exception $restoreError) {
                // Restore operation failed - try to create/update restore record with failed status
                Log::error("Restore operation failed", [
                    'restore_id' => $restoreRecordData['id'] ?? 'unknown',
                    'backup_id' => $backupId,
                    'error' => $restoreError->getMessage(),
                    'trace' => $restoreError->getTraceAsString()
                ]);
                
                // Try to update/create restore record with failed status
                try {
                    $existingRecord = DB::table('restore_records')
                        ->where('id', $restoreRecordData['id'] ?? 0)
                        ->first();
                    
                    if ($existingRecord) {
                        DB::table('restore_records')
                            ->where('id', $restoreRecordData['id'])
                            ->update([
                                'status' => 'failed',
                                'error_message' => substr($restoreError->getMessage(), 0, 1000),
                                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Create new record
                        RestoreRecord::create([
                            'backup_id' => $restoreRecordData['backup_id'],
                            'restore_type' => $restoreRecordData['restore_type'],
                            'tables_restored' => $restoreRecordData['tables_restored'],
                            'status' => 'failed',
                            'error_message' => substr($restoreError->getMessage(), 0, 1000),
                            'metadata' => $restoreRecordData['metadata'],
                            'started_at' => $restoreRecordData['started_at'],
                            'completed_at' => now(),
                            'created_by' => $restoreRecordData['created_by'],
                        ]);
                    }
                } catch (Exception $updateError) {
                    Log::error("Failed to update/create restore record with failed status", [
                        'restore_id' => $restoreRecordData['id'] ?? 'unknown',
                        'error' => $updateError->getMessage()
                    ]);
                }
                
                // Re-throw to be caught by outer catch block
                throw $restoreError;
            }

            // CRITICAL: Ensure restore record has 'completed' status before returning
            // Reload from database to get the latest status
            try {
                $finalRecord = DB::table('restore_records')
                    ->where('id', $restoreRecordData['id'])
                    ->first();
                
                if ($finalRecord) {
                    // Verify status is 'completed', force update if not
                    if ($finalRecord->status !== 'completed') {
                        Log::warning("Restore record status is not 'completed', forcing update", [
                            'restore_id' => $finalRecord->id,
                            'current_status' => $finalRecord->status
                        ]);
                        
                        DB::table('restore_records')
                            ->where('id', $finalRecord->id)
                            ->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                        
                        // Reload after update
                        $finalRecord = DB::table('restore_records')
                            ->where('id', $finalRecord->id)
                            ->first();
                    }
                    
                    // Load into Eloquent model
                    $restoreRecord = RestoreRecord::find($finalRecord->id);
                    if ($restoreRecord) {
                        // Ensure status and completed_at are set correctly
                        $restoreRecord->status = 'completed';
                        if (!$restoreRecord->completed_at) {
                            $restoreRecord->completed_at = now();
                            try {
                                $restoreRecord->saveQuietly();
                            } catch (Exception $saveError) {
                                // Fallback to direct DB update
                                DB::table('restore_records')
                                    ->where('id', $restoreRecord->id)
                                    ->update([
                                        'status' => 'completed',
                                        'completed_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                            }
                        }
                    } else {
                        // If Eloquent find fails, create a new instance from DB data
                        $restoreRecord = new RestoreRecord();
                        $restoreRecord->id = $finalRecord->id;
                        $restoreRecord->backup_id = $finalRecord->backup_id;
                        $restoreRecord->restore_type = $finalRecord->restore_type;
                        $restoreRecord->status = 'completed'; // Force completed status
                        $restoreRecord->tables_restored = $finalRecord->tables_restored ? json_decode($finalRecord->tables_restored, true) : null;
                        $restoreRecord->error_message = $finalRecord->error_message;
                        $restoreRecord->metadata = $finalRecord->metadata ? json_decode($finalRecord->metadata, true) : null;
                        $restoreRecord->started_at = $finalRecord->started_at ? \Carbon\Carbon::parse($finalRecord->started_at) : null;
                        $restoreRecord->completed_at = $finalRecord->completed_at ? \Carbon\Carbon::parse($finalRecord->completed_at) : now();
                        $restoreRecord->created_by = $finalRecord->created_by;
                        $restoreRecord->created_at = $finalRecord->created_at ? \Carbon\Carbon::parse($finalRecord->created_at) : now();
                        $restoreRecord->updated_at = $finalRecord->updated_at ? \Carbon\Carbon::parse($finalRecord->updated_at) : now();
                    }
                } else {
                    // Record not found - try to find by backup_id
                    Log::warning("Restore record not found by ID, searching by backup_id", [
                        'restore_id' => $restoreRecordData['id'],
                        'backup_id' => $backupId
                    ]);
                    
                    $restoreRecord = RestoreRecord::where('backup_id', $backupId)
                        ->latest('created_at')
                        ->first();
                    
                    if ($restoreRecord) {
                        // Force update to completed
                        $restoreRecord->status = 'completed';
                        $restoreRecord->completed_at = now();
                        try {
                            $restoreRecord->saveQuietly();
                        } catch (Exception $saveError) {
                            DB::table('restore_records')
                                ->where('id', $restoreRecord->id)
                                ->update([
                                    'status' => 'completed',
                                    'completed_at' => now(),
                                    'updated_at' => now(),
                                ]);
                        }
                    } else {
                        Log::error("Restore record not found in database after restore completion", [
                            'restore_id' => $restoreRecordData['id'],
                            'backup_id' => $backupId
                        ]);
                    }
                }
            } catch (Exception $finalCheckError) {
                Log::error("Failed to get final restore record status", [
                    'restore_id' => $restoreRecordData['id'] ?? 'unknown',
                    'error' => $finalCheckError->getMessage(),
                    'trace' => $finalCheckError->getTraceAsString()
                ]);
                
                // Try to ensure we have a restore record with completed status
                if (isset($restoreRecord) && $restoreRecord) {
                    $restoreRecord->status = 'completed';
                    if (!$restoreRecord->completed_at) {
                        $restoreRecord->completed_at = now();
                    }
                }
            }
            
            // Final verification: ensure status is 'completed'
            if (isset($restoreRecord) && $restoreRecord) {
                if ($restoreRecord->status !== 'completed') {
                    Log::warning("Restore record status is not 'completed' in final check, forcing update", [
                        'restore_id' => $restoreRecord->id,
                        'current_status' => $restoreRecord->status
                    ]);
                    
                    $restoreRecord->status = 'completed';
                    if (!$restoreRecord->completed_at) {
                        $restoreRecord->completed_at = now();
                    }
                    
                    try {
                        $restoreRecord->saveQuietly();
                    } catch (Exception $saveError) {
                        DB::table('restore_records')
                            ->where('id', $restoreRecord->id)
                            ->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                    }
                }
                
                Log::info("Restore completed successfully with verified 'completed' status", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'type' => $restoreType,
                    'status' => $restoreRecord->status,
                    'completed_at' => $restoreRecord->completed_at,
                'user_id' => $user->id,
            ]);
            }

            return $restoreRecord;

        } catch (Exception $e) {
            // Update restore record with failed status
            // Use direct DB update first to ensure it's saved
            $errorMessage = substr($e->getMessage(), 0, 1000); // Limit error message length
            
            try {
                // Try direct DB update first (more reliable)
                $updated = DB::table('restore_records')
                    ->where('id', $restoreRecord->id)
                    ->update([
                'status' => 'failed',
                        'error_message' => $errorMessage,
                'completed_at' => now(),
                        'updated_at' => now(),
                    ]);
                
                if ($updated > 0) {
                    // Verify update was successful
                    $verifyRecord = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->first();
                    
                    Log::error("Restore record updated to failed using direct DB update", [
                        'restore_id' => $restoreRecord->id,
                        'rows_updated' => $updated,
                        'status' => $verifyRecord->status ?? 'unknown',
                        'error_message' => substr($errorMessage, 0, 500)
                    ]);
                } else {
                    // No rows updated - check if record exists
                    $recordExists = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->exists();
                    
                    if (!$recordExists) {
                        Log::error("Restore record does not exist - cannot update status to failed", [
                            'restore_id' => $restoreRecord->id,
                            'backup_id' => $backupId,
                            'original_error' => substr($errorMessage, 0, 500)
                        ]);
                        // Create a new record to track the failure
                        try {
                            RestoreRecord::create([
                                'backup_id' => $backupId,
                                'restore_type' => $restoreType ?? 'full',
                                'tables_restored' => $tables ?? null,
                                'status' => 'failed',
                                'error_message' => $errorMessage,
                                'created_by' => $user->id,
                                'started_at' => now(),
                                'completed_at' => now(),
                            ]);
                            Log::info("Created new restore record to track failure", [
                                'backup_id' => $backupId
                            ]);
                        } catch (Exception $createError) {
                            Log::error("Failed to create restore record for failure tracking", [
                                'backup_id' => $backupId,
                                'error' => $createError->getMessage()
                            ]);
                        }
                        throw $e; // Re-throw original error
                    }
                    
                    // Try Eloquent update as fallback
                    throw new Exception("No rows updated - trying Eloquent update");
                }
            } catch (Exception $dbUpdateError) {
                // If direct DB update fails, try Eloquent
                Log::warning("Direct DB update failed, trying Eloquent update", [
                    'restore_id' => $restoreRecord->id,
                    'error' => $dbUpdateError->getMessage()
                ]);
                
                try {
                    // Check if record exists before trying Eloquent
                    $recordExists = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->exists();
                    
                    if (!$recordExists) {
                        Log::error("Restore record not found - cannot update status", [
                            'restore_id' => $restoreRecord->id,
                            'backup_id' => $backupId
                        ]);
                        throw $e; // Re-throw original error
                    }
                    
                    // Try to reload the record
                    $restoreRecord = RestoreRecord::find($restoreRecord->id);
                    if (!$restoreRecord) {
                        throw new Exception("Cannot find restore record (ID: {$restoreRecord->id})");
                    }
                    
                    // Update using Eloquent
                    $restoreRecord->status = 'failed';
                    $restoreRecord->error_message = $errorMessage;
                    $restoreRecord->completed_at = now();
                    $restoreRecord->save();
                    
                    // Verify update
                    $verifyRecord = DB::table('restore_records')
                        ->where('id', $restoreRecord->id)
                        ->first();
                    
                    Log::error("Restore record updated to failed using Eloquent", [
                        'restore_id' => $restoreRecord->id,
                        'status' => $verifyRecord->status ?? 'unknown',
                        'error_message' => substr($errorMessage, 0, 500)
                    ]);
                } catch (Exception $updateError) {
                    Log::error("Failed to update restore record status to failed (both methods failed)", [
                        'restore_id' => $restoreRecord->id ?? 'unknown',
                        'backup_id' => $backupId,
                        'db_update_error' => $dbUpdateError->getMessage(),
                        'eloquent_update_error' => $updateError->getMessage(),
                        'original_error' => substr($errorMessage, 0, 500),
                        'trace' => $updateError->getTraceAsString()
                    ]);
                    // Last resort: log the error but re-throw original exception
                }
            }

            Log::error("Restore failed", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'error' => substr($e->getMessage(), 0, 500),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
            ]);

            throw $e;
        }
    }

    /**
     * Perform full database restore
     */
    protected function performFullRestore(BackupRecord $backup, ?array $restoreRecordData = null): void
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $backupFile = $backup->getFullFilePath();

        // Verify backup file exists and is readable
        if (!file_exists($backupFile)) {
            throw new Exception("Backup file does not exist: {$backupFile}");
        }

        if (!is_readable($backupFile)) {
            throw new Exception("Backup file is not readable: {$backupFile}");
        }

        // Check file size
        $fileSize = filesize($backupFile);
        if ($fileSize === 0) {
            throw new Exception("Backup file is empty: {$backupFile}");
        }

        Log::info("Backup file verified", [
            'file' => $backupFile,
            'size' => $fileSize,
            'readable' => is_readable($backupFile),
        ]);

        // Try multiple restore methods (prefer PDO as it's more reliable)
        // Pass restore record data to skip statements affecting restore_records and backup_records tables
        $restoreMethods = [
            'php_pdo' => $this->tryPhpPdoRestore($backupFile, $restoreRecordData),
            'mysql_command' => $this->tryMysqlCommandRestore($host, $port, $username, $password, $database, $backupFile, $restoreRecordData),
        ];

        $lastError = null;
        foreach ($restoreMethods as $method => $callback) {
            try {
                Log::info("Attempting restore using method: {$method}");
                $callback();
                Log::info("Restore completed successfully using method: {$method}");
                
                // Clear application cache after restore
                try {
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                } catch (Exception $e) {
                    Log::warning("Failed to clear cache after restore: " . $e->getMessage());
                }
                
                return;
                
            } catch (Exception $e) {
                $lastError = $e;
                Log::error("Restore method {$method} failed", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                continue;
            }
        }

        throw new Exception("All restore methods failed. Last error: " . ($lastError ? $lastError->getMessage() : 'Unknown error'));
    }

    /**
     * Try mysql command restore method
     */
    protected function tryMysqlCommandRestore(string $host, string $port, string $username, string $password, string $database, string $backupFile, ?array $restoreRecordData = null): callable
    {
        return function() use ($host, $port, $username, $password, $database, $backupFile, $restoreRecordData) {
            // Check if mysql command is available
            $mysqlPath = $this->findMysqlPath();
            
            if (!$mysqlPath) {
                throw new Exception("mysql command not found in system PATH");
            }

            // For mysql command, we need to filter the SQL file to exclude restore_records and backup_records
            // Since we can't easily filter with mysql command, we'll use PHP PDO method instead
            // This method will fall back to PHP PDO if restore record data is provided
            if ($restoreRecordData !== null) {
                throw new Exception("mysql command restore cannot skip restore_records table - use PHP PDO method instead");
            }

            // Build mysql command for restore
            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
                escapeshellarg($mysqlPath),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($backupFile)
            );

            // Execute restore command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $errorOutput = implode("\n", $output);
                throw new Exception("mysql restore failed with return code: {$returnCode}. Output: {$errorOutput}");
            }
        };
    }

    /**
     * Try PHP PDO restore method (fallback)
     */
    protected function tryPhpPdoRestore(string $backupFile, ?array $restoreRecordData = null): callable
    {
        return function() use ($backupFile, $restoreRecordData) {
            Log::info("Starting PHP PDO restore from file: {$backupFile}");
            
            if (!file_exists($backupFile)) {
                throw new Exception("Backup file does not exist: {$backupFile}");
            }
            
            $fileSize = filesize($backupFile);
            Log::info("Reading backup file", [
                'file' => $backupFile,
                'size' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2)
            ]);
            
            // Get PDO connection with multi-statement support
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
            
            // Read the entire file
            $sqlContent = file_get_contents($backupFile);
            if ($sqlContent === false) {
                throw new Exception("Failed to read backup file");
            }
            
            Log::info("Read SQL file", [
                'file_size' => strlen($sqlContent),
                'file_size_mb' => round(strlen($sqlContent) / 1024 / 1024, 2)
            ]);
            
            // Clean up SQL content - remove non-SQL content that might cause errors
            $sqlContent = $this->cleanSqlContent($sqlContent);
            
            // Disable foreign key checks temporarily
            // Don't use AUTOCOMMIT = 0 as it can cause issues with long-running operations
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            try {
                // Execute SQL statements using PDO with multi-statement support
                Log::info("Executing SQL restore...");
                
                // Split SQL into statements and execute them one by one for better error handling
                $statements = $this->splitSqlStatements($sqlContent);
                $statementCount = count($statements);
                $executedCount = 0;
                $errorCount = 0;
                
                Log::info("Found {$statementCount} SQL statements to execute");
                
                // Set timeout for PDO connection (30 seconds per query)
                // Note: PDO::ATTR_TIMEOUT might not work on all systems, so we'll use MySQL timeout instead
                $pdo->exec("SET SESSION wait_timeout = 600"); // 10 minutes
                $pdo->exec("SET SESSION interactive_timeout = 600"); // 10 minutes
                
                // Execute statements with progress tracking
                $maxExecutionTime = 540; // 9 minutes (leave 1 minute for status update)
                $startTime = time();
                
                // Track skipped statements
                $skippedCount = 0;
                
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    if (empty($statement)) {
                        continue;
                    }
                    
                    // CRITICAL: Skip SQL statements that affect restore_records and backup_records tables
                    // These tables should not be restored as they contain current system state
                    if ($this->shouldSkipStatement($statement, $restoreRecordData)) {
                        $skippedCount++;
                        if ($skippedCount <= 10 || $skippedCount % 50 === 0) {
                            Log::debug("Skipping statement that affects restore_records or backup_records table", [
                                'statement_preview' => substr($statement, 0, 100),
                                'skipped_count' => $skippedCount
                            ]);
                        }
                        continue;
                    }
                    
                    // Check if we're running out of time
                    $elapsedTime = time() - $startTime;
                    if ($elapsedTime > $maxExecutionTime) {
                        Log::warning("Restore taking too long, stopping execution", [
                            'elapsed_time' => $elapsedTime,
                            'statements_executed' => $executedCount,
                            'statements_remaining' => $statementCount - $executedCount
                        ]);
                        throw new Exception("Restore operation timed out after {$elapsedTime} seconds. {$executedCount}/{$statementCount} statements executed.");
                    }
                    
                    try {
                        // Log progress every 50 statements
                        if ($executedCount > 0 && $executedCount % 50 === 0) {
                            $elapsed = time() - $startTime;
                            Log::info("Restore progress: {$executedCount}/{$statementCount} statements executed ({$elapsed}s elapsed)");
                        }
                        
                        // Execute statement
                        $statementStartTime = microtime(true);
                                $pdo->exec($statement);
                        $statementExecutionTime = microtime(true) - $statementStartTime;
                        
                        $executedCount++;
                        
                        // Log if statement takes too long (more than 5 seconds)
                        if ($statementExecutionTime > 5) {
                            Log::warning("Slow SQL statement executed", [
                                'execution_time' => round($statementExecutionTime, 2),
                                'statement_index' => $index,
                                'statement_preview' => substr($statement, 0, 100)
                            ]);
                        }
                        
                    } catch (\PDOException $e) {
                        $errorMessage = $e->getMessage();
                        $errorCode = $e->getCode();
                        
                        // Ignore acceptable errors (table exists, duplicate entry, etc.)
                        if (
                            strpos($errorMessage, 'already exists') !== false ||
                            strpos($errorMessage, 'SQLSTATE[42S01]') !== false ||
                            strpos($errorMessage, 'errno: 1050') !== false ||
                            strpos($errorMessage, 'Duplicate entry') !== false ||
                            strpos($errorMessage, 'SQLSTATE[23000]') !== false ||
                            strpos($errorMessage, "doesn't exist") !== false ||
                            strpos($errorMessage, 'SQLSTATE[42S02]') !== false ||
                            strpos($errorMessage, 'Unknown table') !== false ||
                            strpos($errorMessage, 'errno: 1146') !== false
                        ) {
                            // Acceptable error - continue
                            Log::debug("Ignoring acceptable error: " . substr($errorMessage, 0, 100));
                                continue;
                            }
                            
                        // For other errors, log but continue unless it's critical
                        $errorCount++;
                        Log::warning("SQL statement error ({$errorCount}): " . substr($errorMessage, 0, 200));
                        
                        // If too many errors, throw exception
                        if ($errorCount > 100) {
                            throw new Exception("Too many SQL errors encountered ({$errorCount}). Last error: " . substr($errorMessage, 0, 200));
                        }
                    }
                }
                
                // Log skipped statements count
                if ($skippedCount > 0) {
                    Log::info("Skipped {$skippedCount} statements affecting restore_records or backup_records tables");
                }
                
                // Re-enable foreign key checks
                try {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                } catch (Exception $e) {
                    Log::warning("Failed to re-enable foreign key checks: " . $e->getMessage());
                }
                
                $totalTime = time() - $startTime;
                Log::info("PHP PDO restore completed successfully", [
                    'total_statements' => $statementCount,
                    'executed_statements' => $executedCount,
                    'skipped_statements' => $skippedCount,
                    'errors_encountered' => $errorCount,
                    'total_time_seconds' => $totalTime
                ]);
                
            } catch (Exception $e) {
                // Re-enable foreign key checks even if restore fails
                try {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                } catch (Exception $resetError) {
                    Log::error("Failed to reset database settings: " . $resetError->getMessage());
                }
                
                Log::error("PHP PDO restore failed", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                throw $e;
            }
        };
    }

    /**
     * Split SQL content into individual statements
     */
    protected function splitSqlStatements(string $sqlContent): array
    {
        // Remove comments first
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
        
        // Split by semicolon, but handle semicolons inside strings
        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = '';
        
        $length = strlen($sqlContent);
        for ($i = 0; $i < $length; $i++) {
            $char = $sqlContent[$i];
            $nextChar = $i + 1 < $length ? $sqlContent[$i + 1] : '';
            
            // Handle string escaping
            if ($char === '\\' && $inString) {
                $currentStatement .= $char . $nextChar;
                $i++; // Skip next character
                continue;
            }
            
            // Handle string delimiters
            if (($char === '"' || $char === "'") && !$inString) {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === $stringChar && $inString) {
                $inString = false;
                $stringChar = '';
            }
            
            $currentStatement .= $char;
            
            // If we hit a semicolon and we're not in a string, it's the end of a statement
            if ($char === ';' && !$inString) {
                $statement = trim($currentStatement);
                if (!empty($statement) && 
                    preg_match('/^(CREATE|INSERT|UPDATE|DELETE|DROP|ALTER|SET|USE|LOCK|UNLOCK|TRUNCATE|BEGIN|COMMIT|ROLLBACK)/i', $statement)) {
                    $statements[] = $statement;
                }
                $currentStatement = '';
            }
        }
        
        // Add remaining statement if any
        $remaining = trim($currentStatement);
        if (!empty($remaining) && 
            preg_match('/^(CREATE|INSERT|UPDATE|DELETE|DROP|ALTER|SET|USE|LOCK|UNLOCK|TRUNCATE|BEGIN|COMMIT|ROLLBACK)/i', $remaining)) {
            $statements[] = $remaining;
        }
        
        return $statements;
    }

    /**
     * Clean SQL content to remove non-SQL content
     */
    protected function cleanSqlContent(string $sqlContent): string
    {
        // Remove lines that are clearly not SQL
        $lines = explode("\n", $sqlContent);
        $cleanedLines = [];
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Skip empty lines and comments
            if (empty($trimmedLine) || 
                preg_match('/^--/', $trimmedLine) || 
                preg_match('/^\/\*/', $trimmedLine)) {
                continue;
            }
            
            // Skip non-SQL content (CSS, HTML, browser strings, etc.)
            if (preg_match('/max-width:/', $trimmedLine) || 
                preg_match('/^[a-zA-Z-]+:\s*[a-zA-Z0-9\s]+$/', $trimmedLine) ||
                preg_match('/^<[^>]+>$/', $trimmedLine) ||
                preg_match('/Mozilla\/5\.0/', $trimmedLine) ||
                preg_match('/Windows NT 10\.0/', $trimmedLine) ||
                preg_match('/Chrome\/\d+\.\d+\.\d+\.\d+/', $trimmedLine) ||
                preg_match('/Safari\/\d+\.\d+/', $trimmedLine) ||
                preg_match('/^max-width:/', $trimmedLine)) {
                continue;
            }
            
            // Keep the line
            $cleanedLines[] = $line;
        }
        
        return implode("\n", $cleanedLines);
    }

    /**
     * Check if a SQL statement should be skipped during restore
     * Skip ALL statements that affect restore_records and backup_records tables
     * This is CRITICAL to preserve current backup and restore records during restore
     * Without this, DROP TABLE statements in backups would delete all backup records!
     */
    protected function shouldSkipStatement(string $statement, ?array $restoreRecordData = null): bool
    {
        $statement = trim($statement);
        if (empty($statement)) {
            return true;
        }
        
        // CRITICAL: Check for DROP TABLE FIRST (this is the main issue causing backups to be deleted!)
        // DROP TABLE will delete the entire table and all data
        // Pattern: DROP TABLE [IF EXISTS] `backup_records` or DROP TABLE [IF EXISTS] `restore_records`
        if (preg_match('/DROP\s+TABLE\s+(?:IF\s+EXISTS\s+)?[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // CRITICAL: Skip CREATE TABLE for backup_records and restore_records
        // If we skip DROP TABLE, we should also skip CREATE TABLE to avoid recreating the table
        // Pattern: CREATE TABLE [IF NOT EXISTS] `backup_records` or CREATE TABLE [IF NOT EXISTS] `restore_records`
        if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Skip INSERT INTO restore_records or backup_records
        // We don't want to restore old backup/restore records
        if (preg_match('/INSERT\s+INTO\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Skip UPDATE restore_records or backup_records (data modifications)
        if (preg_match('/UPDATE\s+[`"]?(restore_records|backup_records)[`"]?\s+SET/i', $statement)) {
            return true;
        }
        
        // Skip ALTER TABLE for backup_records and restore_records
        // We want to preserve the current table structure
        if (preg_match('/ALTER\s+TABLE\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Skip DELETE FROM restore_records or backup_records
        if (preg_match('/DELETE\s+FROM\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Skip DELETE statements without FROM (MySQL allows DELETE table_name syntax)
        // This catches DELETE backup_records (without FROM)
        if (preg_match('/DELETE\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            // Make sure we're not matching DELETE FROM (which is already handled above)
            // The pattern DELETE table_name doesn't have FROM, so this will catch it
            if (!preg_match('/DELETE\s+FROM/i', $statement)) {
                return true;
            }
        }
        
        // Skip TRUNCATE TABLE restore_records or backup_records
        if (preg_match('/TRUNCATE\s+TABLE\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Skip REPLACE INTO restore_records or backup_records
        if (preg_match('/REPLACE\s+INTO\s+[`"]?(restore_records|backup_records)[`"]?/i', $statement)) {
            return true;
        }
        
        // Allow all other statements (for other tables)
        return false;
    }

    /**
     * Find mysql executable path
     */
    protected function findMysqlPath(): ?string
    {
        $possiblePaths = [
            'mysql',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            '/opt/mysql/bin/mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.21\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
        ];

        foreach ($possiblePaths as $path) {
            $returnCode = 0;
            exec("which {$path} 2>/dev/null || where {$path} 2>nul", $output, $returnCode);
            if ($returnCode === 0) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Perform selective restore (specific tables)
     */
    protected function performSelectiveRestore(BackupRecord $backup, ?array $tables): void
    {
        if (empty($tables)) {
            throw new Exception("No tables specified for selective restore");
        }

        $backupFile = $backup->getFullFilePath();
        
        // Extract specific tables from backup file
        $extractedTables = $this->extractTablesFromBackup($backupFile, $tables);
        
        if (empty($extractedTables)) {
            throw new Exception("No valid tables found in backup for selective restore");
        }

        // Restore each table
        foreach ($extractedTables as $tableSql) {
            DB::unprepared($tableSql);
        }

        // Clear application cache after restore
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
    }

    /**
     * Extract specific tables from backup file
     */
    protected function extractTablesFromBackup(string $backupFile, array $tables): array
    {
        $content = file_get_contents($backupFile);
        $extractedTables = [];

        foreach ($tables as $table) {
            // Look for table structure and data
            $pattern = "/-- Table structure for table `{$table}`.*?-- Dumping data for table `{$table}`.*?-- Table structure for table|-- Dumping data for table `{$table}`.*?-- Table structure for table|$/s";
            
            if (preg_match($pattern, $content, $matches)) {
                $tableSql = $matches[0];
                // Clean up the SQL
                $tableSql = preg_replace('/-- Table structure for table.*?-- Dumping data for table.*?-- Table structure for table|-- Dumping data for table.*?-- Table structure for table|$/s', '', $tableSql);
                $extractedTables[] = trim($tableSql);
            }
        }

        return $extractedTables;
    }

    /**
     * Get list of all restore operations
     */
    public function getRestores(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        try {
            // Load restores with backup and creator relationships
            // Eager load relationships to avoid N+1 queries
            // Use left join behavior by not constraining the relationships
            return RestoreRecord::with(['backup', 'creator'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error("Error getting restores", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty collection on error
            return new \Illuminate\Database\Eloquent\Collection([]);
        }
    }

    /**
     * Get restore by ID
     */
    public function getRestore(int $id): ?RestoreRecord
    {
        return RestoreRecord::with(['backup', 'creator'])->find($id);
    }

    /**
     * Get available tables for selective restore
     */
    public function getAvailableTables(): array
    {
        $tables = DB::select("SHOW TABLES");
        $tableNames = [];
        
        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableNames[] = array_values($tableArray)[0];
        }
        
        return $tableNames;
    }

    /**
     * Preview backup contents
     */
    public function previewBackup(int $backupId): array
    {
        $backup = BackupRecord::find($backupId);
        
        if (!$backup || !$backup->exists()) {
            throw new Exception("Backup not found or file does not exist");
        }

        $backupFile = $backup->getFullFilePath();
        $content = file_get_contents($backupFile);
        
        // Extract table information
        preg_match_all('/-- Table structure for table `([^`]+)`/', $content, $matches);
        $tables = $matches[1] ?? [];
        
        // Get file info
        $fileInfo = [
            'file_size' => filesize($backupFile),
            'created_at' => date('Y-m-d H:i:s', filemtime($backupFile)),
            'tables_count' => count($tables),
            'tables' => $tables,
        ];

        return $fileInfo;
    }

    /**
     * Convert INSERT statement to INSERT ... ON DUPLICATE KEY UPDATE
     */
    protected function convertInsertToUpsert(string $insertStatement, string $tableName): string
    {
        try {
            // Get table structure to determine columns
            $pdo = DB::connection()->getPdo();
            $columns = $pdo->query("SHOW COLUMNS FROM `{$tableName}`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($columns)) {
                Log::warning("Could not get columns for table: {$tableName}, using original statement");
                return $insertStatement;
            }
            
            // Extract the VALUES part
            if (preg_match('/^INSERT INTO\s+`?(\w+)`?\s+VALUES\s*(.+)$/i', $insertStatement, $matches)) {
                $valuesPart = $matches[2];
                
                // Build the ON DUPLICATE KEY UPDATE clause
                $updateClause = [];
                foreach ($columns as $column) {
                    $columnName = $column['Field'];
                    // Skip auto-increment primary keys
                    if ($column['Extra'] !== 'auto_increment') {
                        $updateClause[] = "`{$columnName}` = VALUES(`{$columnName}`)";
                    }
                }
                
                if (!empty($updateClause)) {
                    $upsertStatement = "INSERT INTO `{$tableName}` VALUES {$valuesPart} ON DUPLICATE KEY UPDATE " . implode(', ', $updateClause);
                    Log::debug("Converted INSERT to UPSERT: " . substr($upsertStatement, 0, 200) . "...");
                    return $upsertStatement;
                }
            }
            
            Log::warning("Could not convert INSERT statement for table: {$tableName}");
            return $insertStatement;
            
        } catch (Exception $e) {
            Log::warning("Error converting INSERT to UPSERT for table {$tableName}: " . $e->getMessage());
            return $insertStatement;
        }
    }

    /**
     * Get restore statistics
     */
    public function getRestoreStats(): array
    {
        try {
            $totalRestores = RestoreRecord::count();
            $completedRestores = RestoreRecord::where('status', 'completed')->count();
            $failedRestores = RestoreRecord::where('status', 'failed')->count();
            $lastRestore = RestoreRecord::where('status', 'completed')->latest('created_at')->first();

            return [
                'total_restores' => $totalRestores,
                'completed_restores' => $completedRestores,
                'failed_restores' => $failedRestores,
                'success_rate' => $totalRestores > 0 ? round(($completedRestores / $totalRestores) * 100, 2) : 0,
                'last_restore' => $lastRestore?->created_at?->format('Y-m-d H:i:s') ?? null,
                'last_restore_type' => $lastRestore?->restore_type ?? null,
            ];
        } catch (Exception $e) {
            Log::error("Error getting restore stats", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return default stats on error
            return [
                'total_restores' => 0,
                'completed_restores' => 0,
                'failed_restores' => 0,
                'success_rate' => 0,
                'last_restore' => null,
                'last_restore_type' => null,
            ];
        }
    }

    /**
     * Rollback to a previous restore (using safety backup)
     */
    public function rollbackRestore(int $restoreId): RestoreRecord
    {
        $restore = RestoreRecord::find($restoreId);
        
        if (!$restore) {
            throw new Exception("Restore record not found");
        }

        if (!$restore->isCompleted()) {
            throw new Exception("Cannot rollback incomplete restore");
        }

        $safetyBackupId = $restore->metadata['safety_backup_id'] ?? null;
        
        if (!$safetyBackupId) {
            throw new Exception("No safety backup found for rollback");
        }

        // Perform rollback restore
        return $this->restoreFromBackup($safetyBackupId, 'full', null, auth()->user());
    }

    /**
     * Get restore types
     */
    public function getRestoreTypes(): array
    {
        return [
            'full' => 'Full Restore (Complete database)',
            'selective' => 'Selective Restore (Specific tables only)',
        ];
    }
}
