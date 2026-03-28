<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fon detayları ve portfolyo bilgileri - Fonların varlık dağılımı
 * 
 * Composite Primary Key: code + tarih (Her fonın tarih bazlı portfolyo)
 * Alanlar: 50+ portfolyo tipi (Hisse senedi %, Tahvil % vb), fiyat
 * İlişkiler: Fon (via code)
 */
class TefasFundDetail extends Model
{
    protected $table = 'tefas_fund_details';
    
    public $timestamps = false;
    
    public $incrementing = false;
    
    protected $primaryKey = ['code', 'tarih'];
    
    protected $fillable = [
        'code',
        'tarih',
        'BB',
        'BPP',
        'BYF',
        'D',
        'DB',
        'DT',
        'DÖT',
        'EUT',
        'FB',
        'FKB',
        'GAS',
        'GSYKB',
        'GSYY',
        'GYKB',
        'GYY',
        'HB',
        'HS',
        'KBA',
        'KH',
        'KHAU',
        'KHD',
        'KHTL',
        'KKS',
        'KKSD',
        'KKSTL',
        'KKSYD',
        'KM',
        'KMBYF',
        'KMKBA',
        'KMKKS',
        'KİBD',
        'OSKS',
        'OST',
        'R',
        'T',
        'TPP',
        'TR',
        'VDM',
        'VM',
        'VMAU',
        'VMD',
        'VMTL',
        'VİNT',
        'YBA',
        'YBKB',
        'YBOSB',
        'YBYF',
        'YHS',
        'YMK',
        'YYF',
        'ÖKSYD',
        'ÖSDB',
        'BilFiyat',
        'FIYAT',
        'TEDPAYSAYISI',
        'KISISAYISI',
        'PORTFOYBUYUKLUK',
        'BORSABULTENFIYAT',
    ];

    protected $casts = [
        'tarih' => 'date',
        'BB' => 'decimal:4',
        'BPP' => 'decimal:4',
        'BYF' => 'decimal:4',
        'D' => 'decimal:4',
        'DB' => 'decimal:4',
        'DT' => 'decimal:4',
        'DÖT' => 'decimal:4',
        'EUT' => 'decimal:4',
        'FB' => 'decimal:4',
        'FKB' => 'decimal:4',
        'GAS' => 'decimal:4',
        'GSYKB' => 'decimal:4',
        'GSYY' => 'decimal:4',
        'GYKB' => 'decimal:4',
        'GYY' => 'decimal:4',
        'HB' => 'decimal:4',
        'HS' => 'decimal:4',
        'KBA' => 'decimal:4',
        'KH' => 'decimal:4',
        'KHAU' => 'decimal:4',
        'KHD' => 'decimal:4',
        'KHTL' => 'decimal:4',
        'KKS' => 'decimal:4',
        'KKSD' => 'decimal:4',
        'KKSTL' => 'decimal:4',
        'KKSYD' => 'decimal:4',
        'KM' => 'decimal:4',
        'KMBYF' => 'decimal:4',
        'KMKBA' => 'decimal:4',
        'KMKKS' => 'decimal:4',
        'KİBD' => 'decimal:4',
        'OSKS' => 'decimal:4',
        'OST' => 'decimal:4',
        'R' => 'decimal:4',
        'T' => 'decimal:4',
        'TPP' => 'decimal:4',
        'TR' => 'decimal:4',
        'VDM' => 'decimal:4',
        'VM' => 'decimal:4',
        'VMAU' => 'decimal:4',
        'VMD' => 'decimal:4',
        'VMTL' => 'decimal:4',
        'VİNT' => 'decimal:4',
        'YBA' => 'decimal:4',
        'YBKB' => 'decimal:4',
        'YBOSB' => 'decimal:4',
        'YBYF' => 'decimal:4',
        'YHS' => 'decimal:4',
        'YMK' => 'decimal:4',
        'YYF' => 'decimal:4',
        'ÖKSYD' => 'decimal:4',
        'ÖSDB' => 'decimal:4',
        'BilFiyat' => 'decimal:2',
        'FIYAT' => 'decimal:6',
        'TEDPAYSAYISI' => 'decimal:2',
        'PORTFOYBUYUKLUK' => 'decimal:2',
    ];

    /**
     * Get the fund that owns the detail
     */
    public function fund(): BelongsTo
    {
        return $this->belongsTo(TefasFund::class, 'code', 'code');
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
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
