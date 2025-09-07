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
                            'created_at' => $restore->created_at->format('Y-m-d H:i:s'),
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

            $restore = $this->restoreService->restoreFromBackup(
                $id,
                $request->type,
                $request->tables,
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Restore operation started successfully',
                'restore' => [
                    'id' => $restore->id,
                    'type' => $restore->restore_type,
                    'status' => $restore->status,
                    'created_at' => $restore->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
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
        try {
            $restores = $this->restoreService->getRestores(20);
            $stats = $this->restoreService->getRestoreStats();

            return response()->json([
                'success' => true,
                'restores' => $restores->map(function ($restore) {
                    return [
                        'id' => $restore->id,
                        'backup_id' => $restore->backup_id,
                        'backup_date' => $restore->backup->created_at->format('Y-m-d H:i:s'),
                        'type' => $restore->restore_type,
                        'status' => $restore->status,
                        'tables_restored' => $restore->tables_restored_list,
                        'created_at' => $restore->created_at->format('Y-m-d H:i:s'),
                        'duration' => $restore->duration,
                        'creator' => $restore->creator ? $restore->creator->first_name . ' ' . $restore->creator->last_name : 'Unknown',
                    ];
                }),
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get restore history: ' . $e->getMessage()
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
     * Clean up old backups
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $deletedCount = $this->backupService->cleanupOldBackups($request->days);

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} old backup(s)",
                'deleted_count' => $deletedCount
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
}
