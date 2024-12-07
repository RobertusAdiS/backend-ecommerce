<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * invoices
     *
     * @return void
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * reviews
     *
     * @return void
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * getCreatedAtAttribute
     *
     * @param  mixed $date
     * @return void
     */
    public function getCreatedAtAttribute($date)
    {
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }
    
    /**
     * getJWTIdentifier
     *
     * @return void
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
