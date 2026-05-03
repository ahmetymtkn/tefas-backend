<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kategorilerin dönem bazlı en iyi verimlilik oranları
 * 
 * Alanlar: Getiri %, pazar büyüklüğü, çekiliş tarihi
 * İlişkiler: Kategori, Dönem
 */
class TefasBestCategoryRate extends Model
{
    protected $table = 'tefas_best_category_rates';
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'category_id',
        'period_id',
        'getiri',
        'pazarbuyukluk',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'date',
        'getiri' => 'decimal:4',
        'pazarbuyukluk' => 'decimal:4',
    ];

    protected $appends = ['rate'];

    /**
     * Verimlilik oranı (getiri) üzerinden erişim için accessor
     */
    // Getiri oranı erişimcisi (Accessor)
    public function getRateAttribute()
    {
        return $this->getiri;
    }

    /**
     * Bu oranın ait olduğu kategori
     */
    // Kategori ilişkisi
    public function category(): BelongsTo
    {
        return $this->belongsTo(TefasCategory::class, 'category_id');
    }

    /**
     * Bu oranın ait olduğu dönem
     */
    // Dönem ilişkisi
    public function period(): BelongsTo
    {
        return $this->belongsTo(TefasPeriod::class, 'period_id');
    }
}
