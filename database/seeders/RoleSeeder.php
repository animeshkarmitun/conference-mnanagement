<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Arr;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load permissions from config so seeder stays in sync
        $permissions = config('permissions', []);

        // Helper: expand wildcard permission patterns against the configured permissions map
        $expandPatterns = function (array $patterns) use ($permissions): array {
            $granted = [];
            foreach ($patterns as $pattern) {
                // Global wildcard
                if ($pattern === '*') {
                    // Return wildcard as-is, caller can store it
                    return ['*'];
                }
                // Module wildcard e.g. module.*
                if (str_ends_with($pattern, '.*')) {
                    $module = substr($pattern, 0, -2);
                    if (isset($permissions[$module]) && is_array($permissions[$module])) {
                        foreach ($permissions[$module] as $action) {
                            $granted[] = $module . '.' . $action;
                        }
                    }
                    continue;
                }
                // Exact permission
                $granted[] = $pattern;
            }
            // Deduplicate and return
            return array_values(array_unique($granted));
        };

        // Compose role matrices
        $roles = [
            [
                'name' => 'superadmin',
                'permissions' => ['*'],
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    // Full access to core modules
                    'conferences.*', 'participants.*', 'sessions.*', 'tasks.*', 'notifications.*',
                                        
                    // Admin features
                    'conference-docs.*', 'email-tracking.*', 'email-settings.*', 'backup.*',
                    // Data masters
                    'hotels.*', 'room-types.*', 'venues.*',
                    // Operational
                    'id-cards.*', 'participant-profiles.*', 'travel.*', 'gmail.*', 'bulk-email.*',
                    // Passwordless login admin
                    'passwordless-login.*',
                ],
            ],
            [
                'name' => 'organizer',
                'permissions' => [
                    // Core work areas for event operations
                    'conferences.view', 'conferences.create', 'conferences.edit',
                    'participants.view', 'participants.create', 'participants.edit', 'participants.comments.manage', 'participants.email.send',
                    'sessions.view', 'sessions.create', 'sessions.edit', 'sessions.publish',
                    'tasks.view', 'tasks.create', 'tasks.update',
                    'notifications.view', 'notifications.create',
                    // Docs and travel ops
                    'conference-docs.view', 'conference-docs.media.download',
                    'travel.room_allocations.view', 'travel.itineraries.view', 'travel.travel_conflicts.view',
                    // Reference data (read)
                    'venues.view', 'hotels.view', 'room-types.view',
                    // Passwordless login admin
                    'passwordless-login.*',
                ],
            ],
            [
                'name' => 'speaker',
                'permissions' => [
                    'sessions.view',
                    'participants.view',
                    'notifications.view',
                    'conference-docs.view'
                ],
            ],
            [
                'name' => 'attendee',
                'permissions' => [
                    'sessions.view',
                    'participants.view',
                    'notifications.view',
                    'conference-docs.view'
                ],
            ],
            [
                'name' => 'tasker',
                'permissions' => [
                    'tasks.view', 'tasks.update',
                    'notifications.view'
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $name = $roleData['name'];
            $patterns = $roleData['permissions'] ?? [];
            $finalPermissions = in_array('*', $patterns, true)
                ? ['*']
                : $expandPatterns($patterns);

            Role::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $roleData['description'] ?? null,
                    'permissions' => $finalPermissions,
                ]
            );
        }
    }
}
