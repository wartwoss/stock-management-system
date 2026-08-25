<?php
namespace App\Domain\Notification\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Credit\Models\Credit;
class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'credit_id',
        'type',
        'title',
        'message',
        'is_read',
        'resolved_at',
    ];
    protected function casts(): array
    {
        return [
            'credit_id' => 'integer',
            'is_read' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }
    public function credit(): BelongsTo
    {
        return $this->belongsTo(
            Credit::class,
            'credit_id'
        );
    }
}