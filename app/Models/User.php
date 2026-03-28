<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Uygulama kullanıcıları - Giriş, kayıt ve profil yönetimi
 * 
 * İlişkiler:
 * - hasMany('UserFavoriteFund'): Kullanıcının favori fonları
 * 
 * Kimlik Doğrulama: Laravel Sanctum (API token)
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Kullanıcı tarafından eklenen favori fonların listesi
     * 
     * @return HasMany
     */
    // Kullanıcının favori fonları
    public function favoriteFunds(): HasMany
    {
        return $this->hasMany(UserFavoriteFund::class);
    }
}
