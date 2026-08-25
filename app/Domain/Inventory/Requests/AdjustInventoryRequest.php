
<?php
namespace App\Domain\Inventory\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'adjustment' => [
                'required',
                'integer',
                'not_in:0',
            ],
        ];
    }
}
