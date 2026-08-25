<?php
namespace App\Domain\PaymentMonitoring\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentMonitoringSetting extends Model
{
    protected $table =
        'payment_monitoring_settings';
    protected $fillable = [
        'enabled',
        'check_time',
        'days_before_due',
        'last_checked_at',
    ];
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'days_before_due' => 'integer',
            'last_checked_at' => 'datetime',
        ];
    }
}