<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['starts_at' => 'datetime', 'expires_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function plan(): BelongsTo { return $this->belongsTo(MembershipPlan::class); }
    public function scopeActive($q) { $q->where('status', 'active')->where('expires_at', '>', now()); }
}