<?php
namespace App\Domain\Storage\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreStorageRequest extends FormRequest
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
            'location' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}