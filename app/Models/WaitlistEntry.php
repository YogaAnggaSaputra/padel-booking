<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitlistEntry extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['desired_start_time' => 'datetime', 'desired_end_time' => 'datetime', 'expires_at' => 'datetime'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function court(): BelongsTo { return $this->belongsTo(Court::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeWaiting($q) { $q->where('status', 'waiting'); }
}