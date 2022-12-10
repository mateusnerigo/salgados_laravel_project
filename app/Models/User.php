<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Foundation\Auth\User as Authenticatable,
    Illuminate\Notifications\Notifiable,
    Laravel\Sanctum\HasApiTokens,
    Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'idUsers';
    public $incrementing  = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'userName',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Auxiliary function to return a user by email
     * @param Builder $query
     * @param string  $email
     */
    public function scopeWhereEmail(Builder $query, string $email) {
        return $query->where('email', $email);
    }

    /**
     * Auxiliary function to return a user by userName
     * @param Builder $query
     * @param string  $userName
     */
    public function scopeWhereUserName(Builder $query, string $userName) {
        return $query->where('userName', $userName);
    }
}
