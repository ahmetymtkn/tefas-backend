<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Verimlilik karsilastirma dönemleri - 1 ay, 3 ay, 6 ay, 1 yıl vb
 * 
 * İlişkiler: Verimlilik oranları, karşılaştırmalar
 */
class TefasPeriod extends Model
{
    protected $table = 'tefas_periods';
    
    public $timestamps = false;
    
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'period_name',
    ];

    /**
     * Bu dönemdeki kategori verimlilik oranları
     */
    // Kategori oranları
    public function bestCategoryRates(): HasMany
    {
        return $this->hasMany(TefasBestCategoryRate::class, 'period_id');
    }

    /**
     * Bu dönemdeki en iyi fon oranları
     */
    // Fon oranları
    public function bestFundRates(): HasMany
    {
        return $this->hasMany(TefasBestFundRate::class, 'period_id');
    }

    /**
     * Bu dönemdeki karşılaştırma verileri
     */
    // Karşılaştırma geçmişi
    public function comparisonHistories(): HasMany
    {
        return $this->hasMany(TefasComparisonHistory::class, 'period_id');
    }
}
