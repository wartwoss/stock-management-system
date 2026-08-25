<?php
namespace App\Domain\Notification\Controllers;
use App\Http\Controllers\Controller;
use App\Domain\Notification\Requests\ListNotificationRequest;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Http\JsonResponse;
class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }
    public function index(
        ListNotificationRequest $request
    ): JsonResponse {
        return response()->json(
            $this->notificationService->getAll(
                $request->validated()
            )
        );
    }
    public function show(
        int $notification
    ): JsonResponse {
        return response()->json(
            $this->notificationService
                ->findById($notification)
        );
    }
    public function unread(): JsonResponse
    {
        return response()->json(
            $this->notificationService
                ->getUnread()
        );
    }
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' =>
                $this->notificationService
                    ->getUnreadCount(),
        ]);
    }
    public function markAsRead(
        int $notification
    ): JsonResponse {
        return response()->json(
            $this->notificationService
                ->markAsRead($notification)
        );
    }
    public function markAllAsRead(): JsonResponse
    {
        $count =
            $this->notificationService
                ->markAllAsRead();
        return response()->json([
            'message' =>
                'Notifications marked as read.',
            'updated' => $count,
        ]);
    }
    public function destroy(
        int $notification
    ): JsonResponse {
        $this->notificationService
            ->delete($notification);
        return response()->json([
            'message' =>
                'Notification deleted successfully.',
        ]);
    }
}