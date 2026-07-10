<?php
namespace App\Models;

class MatchParticipant extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
    public function match() { return $this->belongsTo(Match::class); }
    public function user() { return $this->belongsTo(User::class); }
}