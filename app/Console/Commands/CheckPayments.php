<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Domain\PaymentMonitoring\Services\PaymentMonitoringService;
class CheckPayments extends Command
{
    protected $signature =
        'payments:check';
    protected $description =
        'Check upcoming and overdue customer payments';
    public function handle(
        PaymentMonitoringService $service
    ): int {
        $result =
            $service->runScheduledCheck();
        if ($result === null) {
            $this->info(
                'Payment check skipped.'
            );
            return self::SUCCESS;
        }
        $this->info(
            'Payment check completed.'
        );
        $this->info(
            'Due soon: '
            . $result['due_soon_count']
        );
        $this->info(
            'Due today: '
            . $result['due_today_count']
        );
        $this->info(
            'Overdue: '
            . $result['overdue_count']
        );
        return self::SUCCESS;
    }
}