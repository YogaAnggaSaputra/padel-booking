<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    protected $guarded = ['id'];

    public function courts(): HasMany { return $this->hasMany(Court::class); }
    public function members(): HasMany { return $this->hasMany(ClubMember::class); }
    public function coaches(): HasMany { return $this->hasMany(Coach::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function membershipPlans(): HasMany { return $this->hasMany(MembershipPlan::class); }
    public function equipment(): HasMany { return $this->hasMany(Equipment::class); }
    public function tournaments(): HasMany { return $this->hasMany(Tournament::class); }
    public function lessons(): HasMany { return $this->hasMany(Lesson::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function scopeActive($q) { $q->where('is_active', true); }
}