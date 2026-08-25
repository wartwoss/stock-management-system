<?php
namespace App\Domain\Customer\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateCustomerRequest extends FormRequest
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
            'phone_number' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique(
                    'customers',
                    'phone_number'
                )->ignore(
                    $this->route('customer')
                ),
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}
