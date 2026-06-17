<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;

class NotificationService
{
    public static function notifyUser(User $user, string $title, string $message, string $category = 'info', ?string $entityType = null, ?int $entityId = null): Notification
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);

        NotificationRecipient::create([
            'notification_id' => $notification->id,
            'user_id' => $user->id,
        ]);

        return $notification;
    }

    public static function notifyUsers(array $userIds, string $title, string $message, string $category = 'info', ?string $entityType = null, ?int $entityId = null): Notification
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);

        foreach ($userIds as $userId) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ]);
        }

        return $notification;
    }

    public static function createAlert(string $title, string $message, string $severity = 'info', ?string $entityType = null, ?int $entityId = null, ?int $createdBy = null): Alert
    {
        return Alert::create([
            'title' => $title,
            'message' => $message,
            'severity' => $severity,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'created_by' => $createdBy,
        ]);
    }

    public static function notifyActivityAssigned(int $activityId, string $activityTitle, int $assigneeId): void
    {
        $assignee = User::find($assigneeId);
        if ($assignee) {
            self::notifyUser(
                $assignee,
                'Activity Assigned',
                "You have been assigned to activity: {$activityTitle}",
                'info',
                'Activity',
                $activityId
            );
        }
    }

    public static function notifyActivityReassigned(int $activityId, string $activityTitle, int $previousOwnerId, int $newOwnerId): void
    {
        $users = User::whereIn('id', [$previousOwnerId, $newOwnerId])->get();
        foreach ($users as $user) {
            self::notifyUser(
                $user,
                'Activity Reassigned',
                "Activity '{$activityTitle}' has been reassigned.",
                'info',
                'Activity',
                $activityId
            );
        }
    }

    public static function notifyIncidentCreated(int $incidentId, string $incidentTitle, int $ownerId): void
    {
        $owner = User::find($ownerId);
        if ($owner) {
            self::notifyUser(
                $owner,
                'Incident Created',
                "New incident assigned to you: {$incidentTitle}",
                'warning',
                'Incident',
                $incidentId
            );
        }
    }

    public static function notifyEscalationCreated(int $escalationId, int $targetTeamId, string $reason): void
    {
        $teamMemberIds = \App\Models\Personnel::where('team_id', $targetTeamId)
            ->where('status', 'active')
            ->pluck('user_id')
            ->filter()
            ->toArray();

        if (! empty($teamMemberIds)) {
            self::notifyUsers(
                $teamMemberIds,
                'Escalation Assigned',
                "New escalation assigned to your team: {$reason}",
                'warning',
                'Escalation',
                $escalationId
            );
        }
    }

    public static function notifyServiceCritical(int $serviceId, string $serviceName): void
    {
        $admins = User::where('status', 'active')->get();
        foreach ($admins as $admin) {
            self::notifyUser(
                $admin,
                'Service Critical',
                "Service '{$serviceName}' status changed to CRITICAL",
                'critical',
                'Service',
                $serviceId
            );
        }
    }
}
