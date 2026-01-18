<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes;

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if order can be modified
     */
    public function canBeModified(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if status transition is valid
     */
    public function canTransitionToStatus(string $newStatus): bool
    {
        $validTransitions = [
            self::STATUS_PENDING => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
            self::STATUS_PROCESSING => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_COMPLETED => [], // No transitions allowed from completed
            self::STATUS_CANCELLED => [], // No transitions allowed from cancelled
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }
}
