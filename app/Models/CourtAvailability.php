<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourtAvailability extends Model
{
    protected $guarded = ['id'];
    public function court(): BelongsTo { return $this->belongsTo(Court::class); }
    public function scopeBlocked($q) { $q->where('exception_type', 'blocked'); }
}