<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fonların kategori ve dönem bazlı verimlilik oranları - Top 9 fon
 * 
 * Alanlar: Fon, kategori, dönem, verimlilik oranı
 * İlişkiler: Fon, Kategori, Dönem
 */
class TefasBestFundRate extends Model
{
    protected $table = 'tefas_best_fund_rates';
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'fund_id',
        'category_id',
        'period_id',
        'rate',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'date',
        'rate' => 'decimal:4',
    ];

    /**
     * Bu orana ait fon
     */
    // Fon ilişkisi
    public function fund(): BelongsTo
    {
        return $this->belongsTo(TefasFund::class, 'fund_id');
    }

    /**
     * Bu orana ait kategori
     */
    // Kategori ilişkisi
    public function category(): BelongsTo
    {
        return $this->belongsTo(TefasCategory::class, 'category_id');
    }

    /**
     * Bu orana ait dönem
     */
    // Dönem ilişkisi
    public function period(): BelongsTo
    {
        return $this->belongsTo(TefasPeriod::class, 'period_id');
    }
}
