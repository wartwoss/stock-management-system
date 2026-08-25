<?php
namespace App\Domain\Notification\Services;
use App\Domain\Credit\Models\Credit;
use App\Domain\Notification\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
class NotificationService
{
    public function getAll(
        array $filters = []
    ): Collection {
        $query = Notification::with([
            'credit.customer',
            'credit.sale.appliance',
        ]);
        if (isset($filters['type'])) {
            $query->where(
                'type',
                $filters['type']
            );
        }
        if (array_key_exists('is_read', $filters)) {
            $query->where(
                'is_read',
                $filters['is_read']
            );
        }
        if ($filters['active_only'] ?? false) {
            $query->whereNull('resolved_at');
        }
        return $query
            ->orderByDesc('id')
            ->get();
    }
    public function findById(
        int $id
    ): Notification {
        return Notification::with([
            'credit.customer',
            'credit.sale.appliance',
        ])->findOrFail($id);
    }
    public function getUnread(): Collection
    {
        return Notification::with([
            'credit.customer',
            'credit.sale.appliance',
        ])
            ->where('is_read', false)
            ->whereNull('resolved_at')
            ->orderByDesc('id')
            ->get();
    }
    public function getUnreadCount(): int
    {
        return Notification::query()
            ->where('is_read', false)
            ->whereNull('resolved_at')
            ->count();
    }
    public function createFromPaymentStatus(
        Credit $credit,
        string $type
    ): Notification {
        if (!in_array(
            $type,
            [
                'due_soon',
                'due_today',
                'overdue',
            ],
            true
        )) {
            throw new InvalidArgumentException(
                'Invalid notification type.'
            );
        }
        $credit->loadMissing([
            'customer',
            'sale.appliance',
        ]);
        /*
         * Avoid duplicate active notifications.
         */
        $existing = Notification::query()
            ->where('credit_id', $credit->id)
            ->where('type', $type)
            ->whereNull('resolved_at')
            ->first();
        if ($existing) {
            return $existing;
        }
        /*
         * If status becomes more serious,
         * resolve the older warning.
         */
        if ($type === 'due_today') {
            $this->resolveTypes(
                $credit->id,
                ['due_soon']
            );
        }
        if ($type === 'overdue') {
            $this->resolveTypes(
                $credit->id,
                [
                    'due_soon',
                    'due_today',
                ]
            );
        }
        $customerName =
            $credit->customer?->name
            ?? 'Customer';
        $applianceName =
            $credit->sale?->appliance?->name
            ?? 'appliance';
        $dueDate = Carbon::parse(
            $credit->next_due_date
        );
        $amount = min(
            (float) $credit->installment_amount,
            (float) $credit->remaining_debt
        );
        $formattedAmount =
            number_format($amount, 2);
        [$title, $message] = match ($type) {
            'due_soon' => [
                'Payment Due Soon',
                $customerName
                . "'s payment of $"
                . $formattedAmount
                . ' for '
                . $applianceName
                . ' is due on '
                . $dueDate->format('Y-m-d')
                . '.',
            ],
            'due_today' => [
                'Payment Due Today',
                $customerName
                . "'s payment of $"
                . $formattedAmount
                . ' for '
                . $applianceName
                . ' is due today.',
            ],
            'overdue' => [
                'Payment Overdue',
                $customerName
                . "'s payment of $"
                . $formattedAmount
                . ' for '
                . $applianceName
                . ' was due on '
                . $dueDate->format('Y-m-d')
                . '.',
            ],
        };
        return Notification::create([
            'credit_id' => $credit->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
            'resolved_at' => null,
        ]);
    }
    public function markAsRead(
        int $id
    ): Notification {
        $notification =
            Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        return $notification->refresh();
    }
    public function markAllAsRead(): int
    {
        return Notification::query()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);
    }
    public function resolveForCredit(
        int $creditId
    ): void {
        Notification::query()
            ->where('credit_id', $creditId)
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => now(),
            ]);
    }
    private function resolveTypes(
        int $creditId,
        array $types
    ): void {
        Notification::query()
            ->where('credit_id', $creditId)
            ->whereIn('type', $types)
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => now(),
            ]);
    }
    public function delete(
        int $id
    ): void {
        $notification =
            Notification::findOrFail($id);
        $notification->delete();
    }
}
