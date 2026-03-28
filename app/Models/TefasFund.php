<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Türkçe yatırım fonları (TEFAS ı Bursa)
 * 
 * Alanlar: Fon kodu, adı, kategori, isin, komisyon, varlık dağılımı
 * İlişkiler: Kategori, istatistikler, fiyat geçmişi, karşılaştırmalar
 * 
 * Not: Timestamps yok (statik veri), sadece creation_at kullanılır
 */
class TefasFund extends Model
{
    protected $table = 'tefas_funds';
    
    public $timestamps = false;
    
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;
    
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'isin_code',
        'platform_status',
        'start_time',
        'end_time',
        'buy_valor',
        'sell_valor',
        'min_buy_amount',
        'min_sell_amount',
        'max_buy_amount',
        'max_sell_amount',
        'entry_commission',
        'exit_commission',
        'interest_content',
        'risk_value',
        'fon_varlık_dagılım_list',
        'fon_varlık_dagılım_degerler',
    ];

    protected $casts = [
        'min_buy_amount' => 'decimal:4',
        'min_sell_amount' => 'decimal:4',
        'max_buy_amount' => 'decimal:4',
        'max_sell_amount' => 'decimal:4',
        'entry_commission' => 'decimal:4',
        'exit_commission' => 'decimal:4',
    ];

    /**
     * Bu fon hangi kategoriye ait
     */
    // Kategoriye ilişki
    public function category(): BelongsTo
    {
        return $this->belongsTo(TefasCategory::class, 'category_id');
    }

    /**
     * Bu fonun istatistik geçmişi (tarihsel veriler)
     */
    // Fon istatistikleri
    public function statsHistory(): HasMany
    {
        return $this->hasMany(FundStatsHistory::class, 'code', 'code');
    }

    /**
     * Tüm fon detayları ve fiyat geçmişi
     */
    // Fon fiyat geçmişi ve detayları
    public function fundDetails(): HasMany
    {
        return $this->hasMany(TefasFundDetail::class, 'code', 'code');
    }

    /**
     * Bu fonun sıralaması (en iyi 9 fon listesindeki başarısı)
     */
    // En iyi fon oranları
    public function bestFundRates(): HasMany
    {
        return $this->hasMany(TefasBestFundRate::class, 'fund_id');
    }

    /**
     * Bu fonun diğer fonlarla karşılaştırılma geçmişi
     */
    // Karşılaştırma geçmişi
    public function comparisonHistories(): HasMany
    {
        return $this->hasMany(TefasComparisonHistory::class, 'fund_code', 'code');
    }
}
