<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'player1_score' => 'array', 'player2_score' => 'array',
        'scheduled_at' => 'datetime', 'completed_at' => 'datetime',
    ];

    public function tournament(): BelongsTo { return $this->belongsTo(Tournament::class); }
    public function player1(): BelongsTo { return $this->belongsTo(User::class, 'player1_id'); }
    public function player2(): BelongsTo { return $this->belongsTo(User::class, 'player2_id'); }
    public function winner(): BelongsTo { return $this->belongsTo(User::class, 'winner_id'); }
}