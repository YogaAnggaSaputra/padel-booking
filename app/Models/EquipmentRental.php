<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentRental extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['pickup_at' => 'datetime', 'return_at' => 'datetime', 'due_at' => 'datetime'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function equipment(): BelongsTo { return $this->belongsTo(Equipment::class); }
}