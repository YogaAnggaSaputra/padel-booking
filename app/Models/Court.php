<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Court extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_available' => 'bool', 'is_premium' => 'bool'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function pricing(): HasMany { return $this->hasMany(CourtPricing::class); }
    public function availability(): HasMany { return $this->hasMany(CourtAvailability::class); }
    public function scopeAvailable($q) { $q->where('is_available', true); }
}