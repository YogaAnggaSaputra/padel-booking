<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Match extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function court(): BelongsTo { return $this->belongsTo(Court::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function participants(): HasMany { return $this->hasMany(MatchParticipant::class); }
    public function scopeOpen($q) { $q->where('status', 'open'); }
}