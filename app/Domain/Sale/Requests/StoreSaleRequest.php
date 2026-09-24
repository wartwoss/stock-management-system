<?php
namespace App\Domain\Sale\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'appliance_id' => [
                'required',
                'integer',
                'exists:appliances,id',
            ],
            'storage_id' => [
                'required',
                'integer',
                'exists:storages,id',
            ],
            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
                'required_if:payment_type,credit',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'payment_type' => [
                'required',
                Rule::in([
                    'cash',
                    'credit',
                ]),
            ],
            'sale_date' => [
                'required',
                'date',
            ],
            'currency' => [
                'nullable',
                Rule::in(['USD', 'IQD']),
            ],
            'exchange_rate_per_100' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'warranty_months' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }
}