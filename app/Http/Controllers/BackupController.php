<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use App\Services\RestoreService;
use App\Models\BackupRecord;
use App\Models\RestoreRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BackupController extends Controller
{
    protected $backupService;
    protected $restoreService;

    public function __construct(BackupService $backupService, RestoreService $restoreService)
    {
        $this->backupService = $backupService;
        $this->restoreService = $restoreService;
        
        // Ensure only admin users can access backup functionality
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || (!$user->hasRole('admin') && !$user->hasRole('superadmin'))) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Display the backup management dashboard
     */
    public function index(): View
    {
        $backups = $this->backupService->getBackups(20);
        $stats = $this->backupService->getBackupStats();
        $storageInfo = $this->backupService->getStorageInfo();
        $backupTypes = $this->backupService->getBackupTypes();

        return view('admin.backup.index', compact('backups', 'stats', 'storageInfo', 'backupTypes'));
    }

    /**
     * Create a new backup
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:full,incremental,differential,emergency'
        ]);

        try {
            $backup = $this->backupService->createBackup($request->type, auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup' => [
                    'id' => $backup->id,
                    'type' => $backup->backup_type,
                    'status' => $backup->status,
                    'file_size' => $backup->formatted_file_size,
                    'created_at' => $backup->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $backup = $this->backupService->getBackup($id);
            
            if (!$backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'backup' => [
                    'id' => $backup->id,
                    'type' => $backup->backup_type,
                    'status' => $backup->status,
                    'file_name' => $backup->file_name,
                    'file_size' => $backup->formatted_file_size,
                    'checksum' => $backup->checksum,
                    'created_at' => $backup->created_at->format('Y-m-d H:i:s'),
                    'completed_at' => $backup->completed_at?->format('Y-m-d H:i:s'),
                    'duration' => $backup->duration,
                    'creator' => $backup->creator ? $backup->creator->first_name . ' ' . $backup->creator->last_name : 'Unknown',
                    'metadata' => $backup->metadata,
                    'restore_records' => $backup->restoreRecords->map(function ($restore) {
                        return [
                            'id' => $restore->id,
                            'type' => $restore->restore_type,
                            'status' => $restore->status,
                            'created_at' => $restore->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                            'creator' => $restore->creator ? $restore->creator->first_name . ' ' . $restore->creator->last_name : 'Unknown',
                        ];
                    }),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get backup details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->backupService->deleteBackup($id);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup not found'
                ], 404);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore from a backup
     */
    public function restore(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:full,selective',
            'tables' => 'nullable|array',
            'tables.*' => 'string'
        ]);

        try {
            $backup = BackupRecord::find($id);
            
            if (!$backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup not found'
                ], 404);
            }

            if (!$backup->isCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot restore from incomplete backup'
                ], 400);
            }

            if (!$backup->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file does not exist'
                ], 404);
            }

            // Create restore record first
            $user = auth()->user();
            $restoreRecordId = null;
            
            try {
                // Create restore record
                $restoreRecord = \App\Models\RestoreRecord::create([
                    'backup_id' => $id,
                    'restore_type' => $request->type,
                    'tables_restored' => $request->tables,
                    'status' => 'pending',
                    'created_by' => $user->id,
                    'started_at' => now(),
                ]);
                
                $restoreRecordId = $restoreRecord->id;
                
                Log::info("Created restore record in controller", [
                    'restore_id' => $restoreRecordId,
                    'backup_id' => $id
                ]);
            } catch (Exception $createError) {
                Log::error('Failed to create restore record in controller', [
                    'backup_id' => $id,
                    'error' => $createError->getMessage()
                ]);
                throw new Exception("Failed to create restore record: " . $createError->getMessage());
            }

            // Run restore synchronously with increased timeout
            // This is more reliable than background execution on Windows
            try {
                // Increase PHP execution time limit
                set_time_limit(600); // 10 minutes
                ini_set('max_execution_time', '600');
                ini_set('memory_limit', '512M');

                // Execute restore (service will find and use the restore record we created)
                $restore = $this->restoreService->restoreFromBackup(
                    $id,
                    $request->type,
                    $request->tables,
                    $user
                );

                // Use the restore record returned by the service (it's the updated one)
                // Get latest status from database directly (avoid refresh() which throws if record doesn't exist)
                try {
                    // Get restore ID from the service result or use the stored ID
                    $restoreId = isset($restore) && $restore && $restore->id ? $restore->id : $restoreRecordId;
                    
                    if (!$restoreId) {
                        throw new Exception("Restore record ID not found");
                    }
                    
                    $finalRecord = DB::table('restore_records')
                        ->where('id', $restoreId)
                        ->first();
                    
                    if (!$finalRecord && $restoreRecordId && $restoreRecordId != $restoreId) {
                        // Try using the ID we stored (fallback)
                        $finalRecord = DB::table('restore_records')
                            ->where('id', $restoreRecordId)
                            ->first();
                    }
                    
                    if ($finalRecord) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Restore completed successfully!',
                            'restore' => [
                                'id' => $finalRecord->id,
                                'type' => $finalRecord->restore_type,
                                'status' => $finalRecord->status,
                                'completed_at' => $finalRecord->completed_at ? date('Y-m-d H:i:s', strtotime($finalRecord->completed_at)) : null,
                                'created_at' => $finalRecord->created_at ? date('Y-m-d H:i:s', strtotime($finalRecord->created_at)) : null,
                            ]
                        ]);
                    } else {
                        // Record not found - use the restore record from service if available
                        $restoreId = isset($restore) && $restore && $restore->id ? $restore->id : $restoreRecordId;
                        
                        Log::warning("Restore record not found in database after restore", [
                            'restore_id' => $restoreId,
                            'backup_id' => $id
                        ]);
                        
                        // Return success using the restore record from service if available
                        if (isset($restore) && $restore) {
                            return response()->json([
                                'success' => true,
                                'message' => 'Restore completed successfully!',
                                'restore' => [
                                    'id' => $restore->id,
                                    'type' => $restore->restore_type,
                                    'status' => $restore->status,
                                    'completed_at' => $restore->completed_at?->format('Y-m-d H:i:s'),
                                    'created_at' => $restore->created_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
                                ]
                            ]);
                        } else {
                            // Fallback: return success with stored ID
                            return response()->json([
                                'success' => true,
                                'message' => 'Restore completed successfully!',
                                'restore' => [
                                    'id' => $restoreId,
                                    'type' => $request->type,
                                    'status' => 'completed',
                                    'completed_at' => now()->format('Y-m-d H:i:s'),
                                    'created_at' => now()->format('Y-m-d H:i:s'),
                                ]
                            ]);
                        }
                    }
                } catch (Exception $finalError) {
                    $restoreId = isset($restore) && $restore && $restore->id ? $restore->id : $restoreRecordId;
                    
                    Log::warning("Failed to get final restore record status", [
                        'restore_id' => $restoreId,
                        'error' => $finalError->getMessage()
                    ]);
                    
                    // Return success using the restore record from service if available
                    if (isset($restore) && $restore) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Restore completed successfully!',
                            'restore' => [
                                'id' => $restore->id,
                                'type' => $restore->restore_type,
                                'status' => $restore->status,
                                'completed_at' => $restore->completed_at?->format('Y-m-d H:i:s'),
                                'created_at' => $restore->created_at->format('Y-m-d H:i:s'),
                            ]
                        ]);
                    } else {
                        // Fallback: return success with stored ID
                        return response()->json([
                            'success' => true,
                            'message' => 'Restore completed successfully!',
                            'restore' => [
                                'id' => $restoreId,
                                'type' => $request->type,
                                'status' => 'completed',
                                'completed_at' => now()->format('Y-m-d H:i:s'),
                                'created_at' => now()->format('Y-m-d H:i:s'),
                            ]
                        ]);
                    }
                }

            } catch (Exception $e) {
                // Update restore record with error using direct DB update (avoid refresh())
                $errorMessage = substr($e->getMessage(), 0, 1000);
                // Get restore ID from the stored ID or from the restore object if it exists
                $restoreId = $restoreRecordId ?? (isset($restore) && $restore ? $restore->id : null);
                
                if ($restoreId) {
                    try {
                        // Try direct DB update first (more reliable)
                        $updated = DB::table('restore_records')
                            ->where('id', $restoreId)
                            ->update([
                                'status' => 'failed',
                                'error_message' => $errorMessage,
                                'completed_at' => now(),
                                'updated_at' => now(),
                            ]);
                        
                        if ($updated > 0) {
                            Log::error('Restore record updated to failed (direct DB)', [
                                'restore_id' => $restoreId,
                                'rows_updated' => $updated
                            ]);
                        } else {
                            // Try Eloquent as fallback
                            try {
                                $restoreRecord = \App\Models\RestoreRecord::find($restoreId);
                                if ($restoreRecord) {
                                    $restoreRecord->status = 'failed';
                                    $restoreRecord->error_message = $errorMessage;
                                    $restoreRecord->completed_at = now();
                                    $restoreRecord->save();
                                    Log::error('Restore record updated to failed (Eloquent)', [
                                        'restore_id' => $restoreId
                                    ]);
                                }
                            } catch (Exception $eloquentError) {
                                Log::error('Failed to update restore record with error (both methods failed)', [
                                    'restore_id' => $restoreId,
                                    'db_error' => 'No rows updated',
                                    'eloquent_error' => $eloquentError->getMessage()
                                ]);
                            }
                        }
                    } catch (Exception $updateError) {
                        Log::error('Failed to update restore record with error', [
                            'restore_id' => $restoreId,
                            'error' => $updateError->getMessage()
                        ]);
                    }
                }

                Log::error('Restore failed', [
                    'backup_id' => $id,
                    'restore_id' => $restoreId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Restore failed: ' . $e->getMessage()
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Restore failed', [
                'backup_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup stuck restore records (restores stuck in progress for more than 30 minutes)
     */
    public function cleanupStuckRestores(): JsonResponse
    {
        try {
            $timeoutMinutes = 30;
            $stuckRestores = \App\Models\RestoreRecord::where('status', 'in_progress')
                ->where('started_at', '<', now()->subMinutes($timeoutMinutes))
                ->get();

            $cleaned = 0;
            foreach ($stuckRestores as $restore) {
                $restore->update([
                    'status' => 'failed',
                    'error_message' => 'Restore operation timed out after ' . $timeoutMinutes . ' minutes',
                    'completed_at' => now(),
                ]);
                $cleaned++;
            }

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$cleaned} stuck restore record(s)",
                'cleaned' => $cleaned
            ]);

        } catch (Exception $e) {
            Log::error('Failed to cleanup stuck restores', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup stuck restores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview backup contents
     */
    public function preview(int $id): JsonResponse
    {
        try {
            $preview = $this->restoreService->previewBackup($id);

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to preview backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get restore history
     */
    public function restoreHistory(): JsonResponse
    {
        Log::info("Restore history endpoint called");
        
        try {
            // Get restores and stats separately to handle errors individually
            $restores = null;
            try {
                Log::info("Calling getRestores(20)");
                $restores = $this->restoreService->getRestores(20);
                Log::info("getRestores returned", ['count' => $restores ? $restores->count() : 0]);
            } catch (Exception $restoreError) {
                Log::error("Error getting restores in restoreHistory", [
                    'error' => $restoreError->getMessage(),
                    'trace' => $restoreError->getTraceAsString()
                ]);
                $restores = new \Illuminate\Database\Eloquent\Collection([]);
            }
            
            $stats = null;
            try {
                Log::info("Calling getRestoreStats()");
                $stats = $this->restoreService->getRestoreStats();
                Log::info("getRestoreStats returned", ['stats' => $stats]);
            } catch (Exception $statsError) {
                Log::error("Error getting restore stats in restoreHistory", [
                    'error' => $statsError->getMessage(),
                    'trace' => $statsError->getTraceAsString()
                ]);
                $stats = [
                    'total_restores' => 0,
                    'completed_restores' => 0,
                    'failed_restores' => 0,
                    'success_rate' => 0,
                    'last_restore' => null,
                    'last_restore_type' => null,
                ];
            }

            // Map restores to array format safely
            $restoresArray = [];
            Log::info("Starting to map restores", ['restores_count' => $restores ? $restores->count() : 0]);
            
            if ($restores && is_iterable($restores)) {
                foreach ($restores as $index => $restore) {
                    // Skip null restores
                    if (!$restore || !isset($restore->id)) {
                        Log::warning("Skipping null restore at index", ['index' => $index]);
                        continue;
                    }
                    
                    try {
                        Log::debug("Processing restore", ['restore_id' => $restore->id, 'index' => $index]);
                        
                        // Safely get tables_restored_list
                        $tablesRestored = 'All tables';
                        if (!empty($restore->tables_restored)) {
                            if (is_array($restore->tables_restored)) {
                                $tables = array_filter($restore->tables_restored, function($table) {
                                    return !empty($table) && is_string($table);
                                });
                                $tablesRestored = !empty($tables) ? implode(', ', $tables) : 'All tables';
                            } else {
                                $tablesRestored = (string) $restore->tables_restored;
                            }
                        }
                        
                        // Safely get creator name
                        $creatorName = 'Unknown';
                        if ($restore->creator) {
                            $firstName = $restore->creator->first_name ?? '';
                            $lastName = $restore->creator->last_name ?? '';
                            $creatorName = trim($firstName . ' ' . $lastName);
                            if (empty($creatorName)) {
                                $creatorName = 'Unknown';
                            }
                        }
                        
                        // Safely get backup date
                        $backupDate = 'N/A';
                        if ($restore->backup && $restore->backup->created_at) {
                            try {
                                $backupDate = $restore->backup->created_at->format('Y-m-d H:i:s');
                            } catch (Exception $e) {
                                Log::warning("Error formatting backup date", [
                                    'restore_id' => $restore->id,
                                    'error' => $e->getMessage()
                                ]);
                                $backupDate = 'N/A';
                            }
                        }
                        
                        // Safely get backup name
                        $backupName = 'Backup not found';
                        if ($restore->backup) {
                            $backupName = $restore->backup->file_name ?? 'Backup not found';
                        }
                        
                        // Safely get created_at
                        $createdAt = 'N/A';
                        if ($restore->created_at) {
                            try {
                                $createdAt = $restore->created_at->format('Y-m-d H:i:s');
                            } catch (Exception $e) {
                                Log::warning("Error formatting created_at", [
                                    'restore_id' => $restore->id,
                                    'error' => $e->getMessage()
                                ]);
                                $createdAt = 'N/A';
                            }
                        }
                        
                        // Safely get completed_at
                        $completedAt = null;
                        if ($restore->completed_at) {
                            try {
                                $completedAt = $restore->completed_at->format('Y-m-d H:i:s');
                            } catch (Exception $e) {
                                Log::warning("Error formatting completed_at", [
                                    'restore_id' => $restore->id,
                                    'error' => $e->getMessage()
                                ]);
                                $completedAt = null;
                            }
                        }
                        
                        // Safely get duration
                        $duration = null;
                        if ($restore->started_at && $restore->completed_at) {
                            try {
                                $duration = $restore->started_at->diffForHumans($restore->completed_at, true);
                            } catch (Exception $e) {
                                Log::warning("Error calculating duration", [
                                    'restore_id' => $restore->id,
                                    'error' => $e->getMessage()
                                ]);
                                $duration = null;
                            }
                        }
                        
                        // Safely get restore properties
                        $restoresArray[] = [
                            'id' => $restore->id ?? null,
                            'backup_id' => $restore->backup_id ?? null,
                            'backup_date' => $backupDate,
                            'backup_name' => $backupName,
                            'type' => $restore->restore_type ?? 'full',
                            'status' => $restore->status ?? 'unknown',
                            'tables_restored' => $tablesRestored,
                            'created_at' => $createdAt,
                            'duration' => $duration,
                            'creator' => $creatorName,
                            'error_message' => $restore->error_message ?? null,
                            'completed_at' => $completedAt,
                        ];
                        
                        Log::debug("Successfully processed restore", ['restore_id' => $restore->id]);
                    } catch (Exception $restoreError) {
                        // Log error for this specific restore but continue processing others
                        Log::warning("Error processing restore record", [
                            'restore_id' => $restore->id ?? 'unknown',
                            'index' => $index,
                            'error' => $restoreError->getMessage(),
                            'trace' => $restoreError->getTraceAsString()
                        ]);
                        
                        // Skip this restore record instead of adding an error entry
                        continue;
                    }
                }
            }
            
            Log::info("Restore history mapping completed", [
                'total_restores' => count($restoresArray),
                'stats' => $stats
            ]);
            
            return response()->json([
                'success' => true,
                'restores' => $restoresArray,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            Log::error("Failed to get restore history", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading restore history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get restore status by ID
     */
    public function restoreStatus(int $id): JsonResponse
    {
        try {
            $restore = $this->restoreService->getRestore($id);
            
            if (!$restore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restore not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'restore' => [
                    'id' => $restore->id,
                    'backup_id' => $restore->backup_id,
                    'type' => $restore->restore_type,
                    'status' => $restore->status,
                    'error_message' => $restore->error_message,
                    'created_at' => $restore->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    'completed_at' => $restore->completed_at?->format('Y-m-d H:i:s'),
                    'duration' => $restore->duration,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get restore status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available tables for selective restore
     */
    public function getTables(): JsonResponse
    {
        try {
            $tables = $this->restoreService->getAvailableTables();

            return response()->json([
                'success' => true,
                'tables' => $tables
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get tables: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $backupStats = $this->backupService->getBackupStats();
            $restoreStats = $this->restoreService->getRestoreStats();
            $storageInfo = $this->backupService->getStorageInfo();

            return response()->json([
                'success' => true,
                'backup_stats' => $backupStats,
                'restore_stats' => $restoreStats,
                'storage_info' => $storageInfo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview cleanup operation
     */
    public function cleanupPreview(Request $request): JsonResponse
    {
        $request->validate([
            'cleanup_types' => 'required|array|min:1',
            'cleanup_types.*' => 'in:files,database',
            'days' => 'required|integer|min:1|max:365',
            'cleanup_failed' => 'boolean',
            'cleanup_in_progress' => 'boolean'
        ]);

        try {
            $preview = $this->backupService->previewCleanup(
                $request->cleanup_types,
                $request->days,
                $request->boolean('cleanup_failed', false),
                $request->boolean('cleanup_in_progress', false)
            );

            return response()->json([
                'success' => true,
                'backup_count' => $preview['count'],
                'total_size' => $preview['size'],
                'message' => "Found {$preview['count']} backup(s) matching cleanup criteria"
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up old backups
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'cleanup_types' => 'required|array|min:1',
            'cleanup_types.*' => 'in:files,database',
            'days' => 'required|integer|min:1|max:365',
            'cleanup_failed' => 'boolean',
            'cleanup_in_progress' => 'boolean',
            'create_safety_backup' => 'boolean'
        ]);

        try {
            // Create safety backup if requested
            if ($request->boolean('create_safety_backup', true)) {
                $this->backupService->createBackup('safety', auth()->user());
            }

            $result = $this->backupService->cleanupBackups(
                $request->cleanup_types,
                $request->days,
                $request->boolean('cleanup_failed', false),
                $request->boolean('cleanup_in_progress', false)
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'deleted_count' => $result['deleted_count'],
                'freed_space' => $result['freed_space']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test backup system connection and configuration
     */
    public function testConnection(): JsonResponse
    {
        try {
            $results = [];
            
            // Test database connection
            try {
                DB::connection()->getPdo();
                $results['database_connection'] = 'OK';
            } catch (Exception $e) {
                $results['database_connection'] = 'FAILED: ' . $e->getMessage();
            }

            // Test mysqldump availability
            $mysqldumpPath = $this->findMysqldumpPath();
            $results['mysqldump_available'] = $mysqldumpPath ? "OK: {$mysqldumpPath}" : 'NOT FOUND';

            // Test backup directory
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $results['backup_directory'] = is_writable($backupDir) ? 'OK' : 'NOT WRITABLE';

            // Test database configuration
            $dbConfig = config('database.connections.mysql');
            $results['database_config'] = [
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'database' => $dbConfig['database'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'] ? '[SET]' : '[EMPTY]',
            ];

            return response()->json([
                'success' => true,
                'results' => $results
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find mysqldump executable path (helper method)
     */
    protected function findMysqldumpPath(): ?string
    {
        $possiblePaths = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
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
     * Test simple backup creation
     */
    public function testSimpleBackup(): JsonResponse
    {
        try {
            // Test basic file operations
            $testDir = storage_path('app/backups');
            if (!is_dir($testDir)) {
                mkdir($testDir, 0755, true);
            }
            
            $testFile = $testDir . '/test_backup.sql';
            $testContent = "-- Test backup file\n-- Created at: " . now() . "\n";
            
            $bytesWritten = file_put_contents($testFile, $testContent);
            
            if ($bytesWritten === false) {
                throw new Exception("Failed to write test file");
            }
            
            // Test database connection
            $pdo = DB::connection()->getPdo();
            $tables = DB::select("SHOW TABLES");
            
            // Clean up test file
            unlink($testFile);
            
            return response()->json([
                'success' => true,
                'message' => 'Simple backup test passed',
                'results' => [
                    'file_write_test' => 'PASSED',
                    'database_connection' => 'PASSED',
                    'table_count' => count($tables),
                    'backup_directory' => is_writable($testDir) ? 'WRITABLE' : 'NOT WRITABLE'
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Simple backup test failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Fix backup file paths
     */
    public function fixBackupPaths(): JsonResponse
    {
        try {
            $fixedCount = $this->backupService->fixBackupPaths();
            
            return response()->json([
                'success' => true,
                'message' => "Fixed {$fixedCount} backup path(s)",
                'fixed_count' => $fixedCount
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fix backup paths: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test backup details endpoint
     */
    public function testBackupDetails(int $id): JsonResponse
    {
        try {
            $backup = $this->backupService->getBackup($id);
            
            if (!$backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup not found',
                    'backup_id' => $id
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Backup details retrieved successfully',
                'backup' => [
                    'id' => $backup->id,
                    'type' => $backup->backup_type,
                    'status' => $backup->status,
                    'file_name' => $backup->file_name,
                    'file_size' => $backup->formatted_file_size,
                    'checksum' => $backup->checksum,
                    'created_at' => $backup->created_at->format('Y-m-d H:i:s'),
                    'completed_at' => $backup->completed_at ? $backup->completed_at->format('Y-m-d H:i:s') : null,
                    'duration' => $backup->duration,
                    'creator' => $backup->creator ? $backup->creator->first_name . ' ' . $backup->creator->last_name : 'Unknown',
                    'file_path' => $backup->file_path,
                    'exists' => $backup->exists(),
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get backup details: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Download a backup file
     */
    public function download(int $id)
    {
        try {
            $backup = $this->backupService->getBackup($id);
            
            if (!$backup) {
                abort(404, 'Backup not found');
            }

            if (!$backup->isCompleted()) {
                abort(400, 'Cannot download incomplete backup');
            }

            $filePath = $backup->getFullFilePath();
            
            if (!file_exists($filePath)) {
                abort(404, 'Backup file not found');
            }

            return response()->download($filePath, $backup->file_name, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $backup->file_name . '"',
            ]);

        } catch (Exception $e) {
            Log::error('Failed to download backup', [
                'backup_id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            abort(500, 'Failed to download backup: ' . $e->getMessage());
        }
    }
}
