<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['certifications' => 'array', 'specialties' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function lessons(): HasMany { return $this->hasMany(Lesson::class); }
    public function availability(): HasMany { return $this->hasMany(CoachAvailability::class); }
}