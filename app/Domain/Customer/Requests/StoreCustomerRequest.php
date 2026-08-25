<?php
namespace App\Domain\Customer\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreCustomerRequest extends FormRequest
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
            'phone_number' => [
                'required',
                'string',
                'max:30',
                'unique:customers,phone_number',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}
