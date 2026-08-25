
<?php
namespace App\Domain\Credit\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreCreditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'sale_id' => [
                'required',
                'integer',
                'exists:sales,id',
                'unique:credits,sale_id',
            ],
            'down_payment' => [
                'required',
                'numeric',
                'min:0',
            ],
            'number_of_payments' => [
                'required',
                'integer',
                'min:1',
            ],
            'first_due_date' => [
                'required',
                'date',
            ],
        ];
    }
}
