<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $guarded = ['id'];
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function rentals(): HasMany { return $this->hasMany(EquipmentRental::class); }
    public function scopeAvailable($q) { $q->where('quantity_available', '>', 0)->where('is_active', true); }
}