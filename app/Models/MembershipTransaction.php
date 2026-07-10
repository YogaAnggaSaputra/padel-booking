<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipTransaction extends Model
{
    protected $guarded = ['id'];
    public function membership(): BelongsTo { return $this->belongsTo(UserMembership::class); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
}