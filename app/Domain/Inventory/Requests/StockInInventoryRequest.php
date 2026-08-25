
<?php
namespace App\Domain\Inventory\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StockInInventoryRequest extends FormRequest
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
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}