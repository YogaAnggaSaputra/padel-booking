<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['features' => 'array'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function memberships(): HasMany { return $this->hasMany(UserMembership::class); }
    public function scopeActive($q) { $q->where('is_active', true); }
}