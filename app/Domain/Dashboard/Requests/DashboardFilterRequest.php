<?php
namespace App\Domain\Dashboard\Requests;
use Illuminate\Foundation\Http\FormRequest;
class DashboardFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'low_stock_threshold' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'date_from' => [
                'sometimes',
                'nullable',
                'date',
            ],
            'date_to' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ];
    }
}
