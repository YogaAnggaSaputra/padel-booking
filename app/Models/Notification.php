<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['payload' => 'array', 'scheduled_at' => 'datetime', 'sent_at' => 'datetime', 'delivered_at' => 'datetime', 'read_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function scopePending($q) { $q->where('status', 'pending'); }
    public function scopeUnread($q) { $q->whereNull('read_at'); }
}