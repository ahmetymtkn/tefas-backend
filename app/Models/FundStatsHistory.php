<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fon istatistikleri geçmişi - Fonların tarihsel verisi (fiyat, getiri, yatırımcı sayısı vb.)
 * 
 * Composite Primary Key: code + created_at (Aynı fonun günlük verileri)
 * Alanlar: Fiyat, günlük/aylık/yıllık getiri, yatırımcı sayısı
 * İlişkiler: Fon
 */
class FundStatsHistory extends Model
{
    protected $table = 'fund_stats_history';
    
    public $timestamps = false;
    
    public $incrementing = false;
    
    protected $primaryKey = ['code', 'created_at'];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'code',
        'created_at',
        'last_price',
        'daily_return',
        'shares_outstanding',
        'total_value',
        'category',
        'category_rank',
        'investor_count',
        'market_share',
        'return_1m',
        'return_3m',
        'return_6m',
        'return_1y',
    ];

    protected $casts = [
        'created_at' => 'date',
        'last_price' => 'decimal:6',
        'daily_return' => 'decimal:4',
        'shares_outstanding' => 'decimal:2',
        'total_value' => 'decimal:2',
        'market_share' => 'decimal:4',
        'return_1m' => 'decimal:4',
        'return_3m' => 'decimal:4',
        'return_6m' => 'decimal:4',
        'return_1y' => 'decimal:4',
    ];

    /**
     * Bu istatistiklerin ait olduğu fon
     */
    // Fon ilişkisi
    public function fund(): BelongsTo
    {
        return $this->belongsTo(TefasFund::class, 'code', 'code');
    }

    /**
     * Composite primary key (code + created_at) için UPDATE query'si ayarla
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // Composite primary key için save sorgusunu ayarla
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Composite primary key'in değerini save query'i için elde et
     * 
     * @param mixed $keyName
     * @return mixed
     */
    // Primary key değerini döndür
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
