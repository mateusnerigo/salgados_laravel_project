<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Foundation\Auth\User as Authenticatable,
    Illuminate\Notifications\Notifiable,
    Illuminate\Database\Eloquent\Builder,
    Laravel\Sanctum\HasApiTokens,
    Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
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

	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 * @return mixed
	 */
	public function getJWTIdentifier() {
        return $this->getKey();
	}

	/**
	 * Return a key value array, containing any custom claims to be added to the JWT.
	 * @return array
	 */
	public function getJWTCustomClaims() {
        return [];
	}
}
