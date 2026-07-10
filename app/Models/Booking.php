<?php
namespace App\Models;

use App\Models\Concerns\HasUuidColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasUuidColumn;
    protected $guarded = ['id'];
    protected $casts = [
        'start_time' => 'datetime', 'end_time' => 'datetime',
        'is_recurring' => 'bool', 'players' => 'array',
        'checked_in_at' => 'datetime', 'checked_out_at' => 'datetime',
    ];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function court(): BelongsTo { return $this->belongsTo(Court::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function participants(): HasMany { return $this->hasMany(BookingParticipant::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function equipmentRentals(): HasMany { return $this->hasMany(EquipmentRental::class); }
    public function scopeUpcoming($q) { $q->where('start_time', '>', now())->whereNotIn('status', ['cancelled', 'completed']); }
    public function scopeForDate($q, $date) { $q->whereDate('start_time', $date); }
    public function scopeOnCourt($q, $courtId) { $q->where('court_id', $courtId); }
    public function scopeOverlapping($q, $start, $end) { $q->where('start_time', '<', $end)->where('end_time', '>', $start)->whereNotIn('status', ['cancelled']); }
}
