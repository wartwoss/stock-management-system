<?php
namespace App\Domain\PaymentMonitoring\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdatePaymentMonitoringSettingRequest
    extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'enabled' => [
                'sometimes',
                'boolean',
            ],
            'check_time' => [
                'sometimes',
                'required',
                'date_format:H:i',
            ],
            'days_before_due' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:30',
            ],
        ];
    }
}