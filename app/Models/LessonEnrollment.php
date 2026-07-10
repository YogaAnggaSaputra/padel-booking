<?php
namespace App\Models;

class LessonEnrollment extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];
    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
}