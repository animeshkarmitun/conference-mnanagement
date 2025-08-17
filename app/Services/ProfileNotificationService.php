<?php

namespace App\Services;

use App\Events\ProfileEvent;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;

class ProfileNotificationService
{
    /**
     * Send notification when profile information is updated
     */
    public function notifyProfileUpdated(Participant $participant, array $changes): void
    {
        $changeDescriptions = [];
        
        // Describe what changed
        if (isset($changes['personal_info'])) {
            $changeDescriptions[] = 'personal information';
        }
        if (isset($changes['visa_status'])) {
            $changeDescriptions[] = 'visa status';
        }
        if (isset($changes['dietary_needs'])) {
            $changeDescriptions[] = 'dietary preferences';
        }
        if (isset($changes['organization'])) {
            $changeDescriptions[] = 'organization details';
        }
        if (isset($changes['profile_picture'])) {
            $changeDescriptions[] = 'profile picture';
        }
        if (isset($changes['resume'])) {
            $changeDescriptions[] = 'resume';
        }
        
        $changesText = implode(', ', $changeDescriptions);
        $message = "Profile updated by {$participant->user->first_name} {$participant->user->last_name} - Changes: {$changesText}";
        
        $this->triggerProfileEvent($participant, 'profile_updated', $message, $changes);
    }

    /**
     * Send notification when personal information is updated
     */
    public function notifyPersonalInfoUpdated(Participant $participant, array $oldData, array $newData): void
    {
        $changes = [];
        
        // Check for changes in personal information
        if (($oldData['first_name'] ?? '') !== ($newData['first_name'] ?? '')) {
            $changes[] = 'first name';
        }
        if (($oldData['last_name'] ?? '') !== ($newData['last_name'] ?? '')) {
            $changes[] = 'last name';
        }
        if (($oldData['email'] ?? '') !== ($newData['email'] ?? '')) {
            $changes[] = 'email address';
        }
        
        if (!empty($changes)) {
            $changesText = implode(', ', $changes);
            $message = "Personal information updated by {$participant->user->first_name} {$participant->user->last_name} - Changes: {$changesText}";
            
            $this->triggerProfileEvent($participant, 'personal_info_updated', $message, ['personal_info' => $changes]);
        }
    }

    /**
     * Send notification when visa status is updated
     */
    public function notifyVisaStatusUpdated(Participant $participant, string $oldStatus, string $newStatus): void
    {
        $message = "Visa status updated by {$participant->user->first_name} {$participant->user->last_name} - Changed from {$oldStatus} to {$newStatus}";
        
        $this->triggerProfileEvent($participant, 'visa_status_updated', $message, ['visa_status' => ['old' => $oldStatus, 'new' => $newStatus]]);
    }

    /**
     * Send notification when dietary needs are updated
     */
    public function notifyDietaryNeedsUpdated(Participant $participant, string $oldNeeds, string $newNeeds): void
    {
        $message = "Dietary preferences updated by {$participant->user->first_name} {$participant->user->last_name} - Changed from '{$oldNeeds}' to '{$newNeeds}'";
        
        $this->triggerProfileEvent($participant, 'dietary_needs_updated', $message, ['dietary_needs' => ['old' => $oldNeeds, 'new' => $newNeeds]]);
    }

    /**
     * Send notification when organization is updated
     */
    public function notifyOrganizationUpdated(Participant $participant, string $oldOrg, string $newOrg): void
    {
        $message = "Organization updated by {$participant->user->first_name} {$participant->user->last_name} - Changed from '{$oldOrg}' to '{$newOrg}'";
        
        $this->triggerProfileEvent($participant, 'organization_updated', $message, ['organization' => ['old' => $oldOrg, 'new' => $newOrg]]);
    }

    /**
     * Send notification when documents are uploaded
     */
    public function notifyDocumentUploaded(Participant $participant, string $documentType): void
    {
        $message = "{$documentType} uploaded by {$participant->user->first_name} {$participant->user->last_name}";
        
        $this->triggerProfileEvent($participant, 'document_uploaded', $message, ['document_type' => $documentType]);
    }

    /**
     * Trigger profile event
     */
    private function triggerProfileEvent(
        Participant $participant, 
        string $eventType, 
        string $message, 
        array $changes = []
    ): void {
        try {
            $event = new ProfileEvent($participant, $eventType, $message, $changes);
            event($event);
            
            Log::info('Profile event triggered', [
                'participant_id' => $participant->id,
                'event_type' => $eventType,
                'message' => $message,
                'changes' => $changes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to trigger profile event', [
                'error' => $e->getMessage(),
                'participant_id' => $participant->id,
                'event_type' => $eventType
            ]);
        }
    }
}
