<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}