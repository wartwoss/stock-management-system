<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domain\Credit\Models\Credit;
use App\Domain\Payment\Models\Payment;
use App\Domain\Notification\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            'payment.status.detected',
            function (
                Credit $credit,
                string $type
            ): void {
                app(NotificationService::class)
                    ->createFromPaymentStatus(
                        $credit,
                        $type
                    );
            }
        );


        Payment::created(
            function (Payment $payment): void {
                app(NotificationService::class)
                    ->resolveForCredit(
                        $payment->credit_id
                    );
            }
        );
    }
}
