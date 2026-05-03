<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fonların karşılaştırılma geçmişi - Bir fonun farklı dönemlerde diğer fonlarla karşılaştırılması
 * 
 * Alanlar: Fon kodu, dönem, karşılaştırma isimleri (JSON), değerleri (JSON)
 * İlişkiler: Fon, Dönem
 */
class TefasComparisonHistory extends Model
{
    protected $table = 'tefas_comparison_history';
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'fund_code',
        'period_id',
        'comparison_names',
        'comparison_values',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'date',
    ];

    /**
     * Bu karşılaştırmanın ait olduğu fon
     */
    // Fon ilişkisi
    public function fund(): BelongsTo
    {
        return $this->belongsTo(TefasFund::class, 'fund_code', 'code');
    }

    /**
     * Bu karşılaştırmanın ait olduğu dönem
     */
    // Dönem ilişkisi
    public function period(): BelongsTo
    {
        return $this->belongsTo(TefasPeriod::class, 'period_id');
    }

    /**
     * Karşılaştırma isimlerini array'e çevir (JSON decode)
     */
    // Karşılaştırma isimleri için dizi erişimcisi (Accessor)
    public function getComparisonNamesArrayAttribute()
    {
        return $this->comparison_names ? json_decode($this->comparison_names, true) : [];
    }

    /**
     * Karşılaştırma değerlerini array'e çevir (JSON decode)
     */
    // Karşılaştırma değerleri için dizi erişimcisi (Accessor)
    public function getComparisonValuesArrayAttribute()
    {
        return $this->comparison_values ? json_decode($this->comparison_values, true) : [];
    }

    /**
     * Array'den karşılaştırma isimlerini JSON'a çevir (mutator)
     */
    // Karşılaştırma isimlerini JSON formatına çevir (Mutator)
    public function setComparisonNamesAttribute($value)
    {
        $this->attributes['comparison_names'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Array'den karşılaştırma değerlerini JSON'a çevir (mutator)
     */
    // Karşılaştırma değerlerini JSON formatına çevir (Mutator)
    public function setComparisonValuesAttribute($value)
    {
        $this->attributes['comparison_values'] = is_array($value) ? json_encode($value) : $value;
    }
}
