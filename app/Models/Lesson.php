<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function coach(): BelongsTo { return $this->belongsTo(Coach::class); }
    public function court(): BelongsTo { return $this->belongsTo(Court::class); }
    public function enrollments(): HasMany { return $this->hasMany(LessonEnrollment::class); }
}