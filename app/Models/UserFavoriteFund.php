<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kullanıcı tarafından favorilenen fonlar - Pivot tablosu
 * 
 * İlişkiler: User, TefasFund
 */
class UserFavoriteFund extends Model
{
    protected $table = 'user_favorite_funds';

    protected $fillable = [
        'user_id',
        'fund_code',
    ];

    /**
     * Hangi kullanıcı bu fonu favoriledi
     */
    // Kullanıcıya ilişki
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Favorilenen fon bilgileri
     */
    // Fon bilgisine ilişki
    public function fund(): BelongsTo
    {
        return $this->belongsTo(TefasFund::class, 'fund_code', 'code');
    }
}
