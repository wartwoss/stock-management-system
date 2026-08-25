<?php
namespace App\Domain\Notification\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ListNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'type' => [
                'sometimes',
                Rule::in([
                    'due_soon',
                    'due_today',
                    'overdue',
                ]),
            ],
            'is_read' => [
                'sometimes',
                'boolean',
            ],
            'active_only' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}