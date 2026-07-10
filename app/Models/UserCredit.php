<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
}