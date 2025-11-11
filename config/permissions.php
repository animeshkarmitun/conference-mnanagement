<?php

return [
    'users' => ['view', 'create', 'edit', 'delete', 'activate', 'deactivate', 'assign-roles'],
    'roles' => ['view', 'create', 'edit', 'delete', 'assign_users'],

    'conferences' => ['view', 'create', 'edit', 'delete', 'export', 'conflicts.manage'],
    'participants' => ['view', 'create', 'edit', 'delete', 'comments.manage', 'email.send', 'import.view', 'import.process', 'import.sample'],
    'sessions' => ['view', 'create', 'edit', 'delete', 'publish', 'export', 'resend_email'],
    //'tasks' => ['view', 'create', 'update', 'delete', 'export', 'update_status'],
    'notifications' => ['view', 'create', 'manage', 'mark_read'],

    'conference-docs' => ['view', 'create', 'edit', 'delete', 'media.upload', 'media.download'],

    'email-tracking' => ['view', 'stats', 'emails', 'show', 'resend', 'delete', 'export', 'cleanup', 'participants', 'conversations', 'thread', 'search'],
    'email-settings' => ['view', 'edit', 'update', 'preview', 'test', 'reset'],
    'backup' => ['view', 'create', 'restore', 'delete', 'cleanup', 'cleanup.preview', 'tables', 'stats', 'preview', 'fix_paths', 'test.connection', 'test.simple', 'test.details'],

    // 'hotels' => ['view', 'create', 'edit', 'delete', 'rooms.view', 'rooms.update'],
    // 'room-types' => ['view', 'create', 'edit', 'delete'],
    // 'venues' => ['view', 'create', 'edit', 'delete'],

    //'id-cards' => ['view', 'generate', 'toggle_status', 'generate_for_user', 'generate_for_participant', 'generate_for_conference', 'generate_my_card'],
    'participant-profiles' => ['view', 'create', 'edit', 'set_primary', 'archive', 'restore', 'delete', 'conflicts.view', 'conflicts.resolve'],
    'travel' => ['itineraries.view', 'export_itinerary'],

    'gmail' => ['view', 'disconnect', 'reply', 'send_reply', 'participants'],
    'bulk-email' => ['view', 'send', 'participants'],
    'passwordless-login' => ['admin.view', 'generate', 'generate_bulk', 'participants', 'user.links', 'user.revoke', 'cleanup', 'participants.by_type', 'participant_types', 'delete'],
];



