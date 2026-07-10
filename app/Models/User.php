<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $guarded = ['id'];
    protected $hidden = ['password_hash'];
    protected $casts = ['email_verified_at' => 'datetime', 'phone_verified_at' => 'datetime', 'date_of_birth' => 'date'];

    public function clubs(): HasMany { return $this->hasMany(ClubMember::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function memberships(): HasMany { return $this->hasMany(UserMembership::class); }
    public function credits(): HasMany { return $this->hasMany(UserCredit::class); }
    public function notifications(): HasMany { return $this->hasMany(Notification::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function scopeActive($q) { $q->where('status', 'active'); }
}