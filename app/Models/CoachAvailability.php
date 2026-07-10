<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachAvailability extends Model
{
    protected $guarded = ['id'];
    public function coach(): BelongsTo { return $this->belongsTo(Coach::class); }
}