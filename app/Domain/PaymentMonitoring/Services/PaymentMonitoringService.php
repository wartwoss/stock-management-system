<?php
namespace App\Domain\PaymentMonitoring\Services;
use App\Domain\Credit\Models\Credit;
use App\Domain\PaymentMonitoring\Models\PaymentMonitoringSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
class PaymentMonitoringService
{
    public function getSettings(): PaymentMonitoringSetting
    {
        return PaymentMonitoringSetting::firstOrCreate(
            ['id' => 1],
            [
                'enabled' => true,
                'check_time' => '20:00',
                'days_before_due' => 3,
            ]
        );
    }
    public function updateSettings(
        array $data
    ): PaymentMonitoringSetting {
        $settings = $this->getSettings();
        $settings->update($data);
        return $settings->refresh();
    }
    public function shouldRunNow(): bool
    {
        $settings = $this->getSettings();
        if (!$settings->enabled) {
            return false;
        }
        /*
         * Already checked today?
         */
        if (
            $settings->last_checked_at
            && $settings->last_checked_at->isToday()
        ) {
            return false;
        }
        /*
         * Today's selected check time.
         */
        $scheduledTime = Carbon::parse(
            now()->toDateString()
            . ' '
            . $settings->check_time
        );
        return now()->greaterThanOrEqualTo(
            $scheduledTime
        );
    }
    public function runScheduledCheck(): ?array
    {
        if (!$this->shouldRunNow()) {
            return null;
        }
        return $this->runCheck();
    }
    public function runCheck(): array
    {
        $settings = $this->getSettings();
        $today = today()->startOfDay();
        $dueSoonLimit = $today
            ->copy()
            ->addDays(
                $settings->days_before_due
            );
        $credits = Credit::with([
            'customer',
            'sale.appliance',
        ])
            ->where(
                'remaining_debt',
                '>',
                0
            )
            ->whereNotNull(
                'next_due_date'
            )
            ->get();
        $result = [
            'due_soon' => [],
            'due_today' => [],
            'overdue' => [],
        ];
        foreach ($credits as $credit) {
            $dueDate = Carbon::parse(
                $credit->next_due_date
            )->startOfDay();
            $type = null;
            /*
             * OVERDUE
             */
            if ($dueDate->lt($today)) {
                $type = 'overdue';
                if (
                    $credit->status !== 'overdue'
                ) {
                    $credit->status = 'overdue';
                    $credit->save();
                }
            }
            /*
             * DUE TODAY
             */
            elseif (
                $dueDate->isSameDay($today)
            ) {
                $type = 'due_today';
                if (
                    $credit->status === 'overdue'
                ) {
                    $credit->status = 'active';
                    $credit->save();
                }
            }
            /*
             * DUE SOON
             */
            elseif (
                $settings->days_before_due > 0
                && $dueDate->lte($dueSoonLimit)
            ) {
                $type = 'due_soon';
                if (
                    $credit->status === 'overdue'
                ) {
                    $credit->status = 'active';
                    $credit->save();
                }
            }
            if (!$type) {
                continue;
            }
            $credit->refresh();
            $result[$type][] = $credit;
            /*
             * Notification module will listen
             * to this event later.
             */
            Event::dispatch(
                'payment.status.detected',
                [
                    $credit,
                    $type,
                ]
            );
        }
        $settings->last_checked_at = now();
        $settings->save();
        return [
            'checked_at' =>
                now()->toDateTimeString(),
            'due_soon_count' =>
                count($result['due_soon']),
            'due_today_count' =>
                count($result['due_today']),
            'overdue_count' =>
                count($result['overdue']),
            'due_soon' =>
                $result['due_soon'],
            'due_today' =>
                $result['due_today'],
            'overdue' =>
                $result['overdue'],
        ];
    }
    public function getStatus(): array
    {
        $settings = $this->getSettings();
        return [
            'enabled' =>
                $settings->enabled,
            'check_time' =>
                $settings->check_time,
            'days_before_due' =>
                $settings->days_before_due,
            'last_checked_at' =>
                $settings->last_checked_at,
            'should_run_now' =>
                $this->shouldRunNow(),
        ];
    }
}
