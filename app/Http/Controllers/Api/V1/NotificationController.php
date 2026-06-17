<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAlertRequest;
use App\Http\Resources\Api\V1\AlertResource;
use App\Http\Resources\Api\V1\NotificationRecipientResource;
use App\Models\Alert;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        $query = NotificationRecipient::with(['notification.alert'])
            ->where('user_id', $request->user()->id)
            ->whereHas('notification', function ($builder) use ($request) {
                if ($request->filled('category')) {
                    $builder->where('category', $request->input('category'));
                }

                if ($request->filled('search')) {
                    $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
                    $search = '%'.$request->input('search').'%';

                    $builder->where(function ($searchBuilder) use ($operator, $search) {
                        $searchBuilder->where('title', $operator, $search)
                            ->orWhere('message', $operator, $search);
                    });
                }
            });

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'read_at'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $recipients = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => NotificationRecipientResource::collection($recipients)->resolve(),
            'meta' => [
                'current_page' => $recipients->currentPage(),
                'per_page' => $recipients->perPage(),
                'total' => $recipients->total(),
            ],
        ]);
    }

    public function alerts(Request $request): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('view_dashboard'), 403);

        $query = Alert::with('createdBy')->withCount('notifications');

        foreach (['severity', 'status', 'entity_type', 'entity_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $search = '%'.$request->input('search').'%';

            $query->where(function ($builder) use ($operator, $search) {
                $builder->where('title', $operator, $search)
                    ->orWhere('message', $operator, $search);
            });
        }

        $sort = in_array($request->input('sort'), ['created_at', 'updated_at', 'severity', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $alerts = $query->orderBy($sort, $direction)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AlertResource::collection($alerts)->resolve(),
            'meta' => [
                'current_page' => $alerts->currentPage(),
                'per_page' => $alerts->perPage(),
                'total' => $alerts->total(),
            ],
        ]);
    }

    public function storeAlert(StoreAlertRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $recipientIds = $validated['recipient_ids'] ?? [];
        unset($validated['recipient_ids']);

        $alert = DB::transaction(function () use ($request, $validated, $recipientIds) {
            $alert = Alert::create([
                ...$validated,
                'status' => 'active',
                'created_by' => $request->user()->id,
            ]);

            if ($recipientIds !== []) {
                $notification = Notification::create([
                    'alert_id' => $alert->id,
                    'created_by' => $request->user()->id,
                    'title' => $alert->title,
                    'message' => $alert->message,
                    'category' => $alert->severity,
                    'entity_type' => $alert->entity_type,
                    'entity_id' => $alert->entity_id,
                ]);

                foreach (array_unique($recipientIds) as $recipientId) {
                    NotificationRecipient::create([
                        'notification_id' => $notification->id,
                        'user_id' => $recipientId,
                        'created_by' => $request->user()->id,
                    ]);
                }
            }

            return $alert;
        });

        return $this->success([
            'alert' => AlertResource::make($alert->fresh(['createdBy'])->loadCount('notifications')),
        ], 'Alert created successfully.', 201);
    }

    public function markAsRead(Request $request, NotificationRecipient $recipient): JsonResponse
    {
        abort_unless($recipient->user_id === $request->user()->id, 403);

        $recipient->update([
            'is_read' => true,
            'read_at' => now(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'notification' => NotificationRecipientResource::make($recipient->fresh(['notification.alert'])),
        ], 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = NotificationRecipient::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_by' => $request->user()->id,
            ]);

        return $this->success([
            'updated_count' => $count,
        ], 'Notifications marked as read.');
    }

    public function acknowledgeAlert(Request $request, Alert $alert): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_services'), 403);

        $alert->update([
            'status' => 'acknowledged',
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'alert' => AlertResource::make($alert->fresh(['createdBy'])->loadCount('notifications')),
        ], 'Alert acknowledged successfully.');
    }

    public function resolveAlert(Request $request, Alert $alert): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_services'), 403);

        $alert->update([
            'status' => 'resolved',
            'updated_by' => $request->user()->id,
        ]);

        return $this->success([
            'alert' => AlertResource::make($alert->fresh(['createdBy'])->loadCount('notifications')),
        ], 'Alert resolved successfully.');
    }

    public function destroyAlert(Request $request, Alert $alert): JsonResponse
    {
        abort_unless($request->user()?->hasPermission('manage_services'), 403);

        $alert->delete();

        return $this->success(message: 'Alert archived successfully.');
    }
}
