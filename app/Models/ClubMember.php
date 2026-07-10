<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubMember extends Pivot
{
    protected $guarded = ['id'];
    public $timestamps = true;

    public function club() { return $this->belongsTo(Club::class); }
    public function user() { return $this->belongsTo(User::class); }
}