<?php
namespace App\Domain\Appliance\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateApplianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'category' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'brand' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'purchase_price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],
            'date_added' => [
                'sometimes',
                'required',
                'date',
            ],
        ];
    }
}