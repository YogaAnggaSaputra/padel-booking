<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocialAccount extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['token' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}