<?php
namespace App\Domain\Credit\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateCreditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'number_of_payments' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'first_due_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'next_due_date' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }
}