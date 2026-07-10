<?php
namespace App\Models;

use App\Models\Concerns\HasUuidColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasUuidColumn;
    protected $guarded = ['id'];
    protected $casts = [
        'registration_start' => 'datetime', 'registration_end' => 'datetime',
        'start_date' => 'date', 'end_date' => 'date',
        'rules' => 'array'
    ];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function matches(): HasMany { return $this->hasMany(TournamentMatch::class); }
    public function registrations(): HasMany { return $this->hasMany(TournamentRegistration::class); }
}
