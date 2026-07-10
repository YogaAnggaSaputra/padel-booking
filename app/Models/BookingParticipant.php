<?php
namespace App\Models;

class BookingParticipant extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
}