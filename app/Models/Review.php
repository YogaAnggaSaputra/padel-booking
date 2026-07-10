<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = ['id'];
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function coach(): BelongsTo { return $this->belongsTo(Coach::class); }
}