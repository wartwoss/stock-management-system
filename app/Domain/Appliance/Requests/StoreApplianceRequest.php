<?php
namespace App\Domain\Appliance\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreApplianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'category' => [
                'required',
                'string',
                'max:100',
            ],
            'brand' => [
                'required',
                'string',
                'max:100',
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'date_added' => [
                'required',
                'date',
            ],
        ];
    }
}