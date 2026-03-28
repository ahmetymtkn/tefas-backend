<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fon kategorileri - Yatirım türleri (Hısse Senedi, Sabit Getirili vb)
 * 
 * İlişkiler: Çok fon, verimlilik oranları
 */
class TefasCategory extends Model
{
    protected $table = 'tefas_category';
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'name',
    ];

    /**
     * Bu kategoriye ait tüm fonlar
     */
    // Fon listesi
    public function funds(): HasMany
    {
        return $this->hasMany(TefasFund::class, 'category_id');
    }

    /**
     * En iyi kategori verimlilik oranları
     */
    // Verimlilik oranları
    public function bestCategoryRates(): HasMany
    {
        return $this->hasMany(TefasBestCategoryRate::class, 'category_id');
    }

    /**
     * Bu kategorideki en iyi 9 fon
     */
    // En iyi fonlar
    public function bestFundRates(): HasMany
    {
        return $this->hasMany(TefasBestFundRate::class, 'category_id');
    }
}
