<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TefasTrendChecking extends Model
{
    protected $table = 'tefas_trend_checking';
    
    // Tabloda created_at ve updated_at kolonları bulunmadığı için false yapıyoruz
    public $timestamps = false;

    protected $fillable = [
        'fund_code',
        'period_days',
        'analysis_date',
        'up_days_count',
        'down_days_count',
        'total_return'
    ];

    protected $casts = [
        'period_days' => 'integer',
        'up_days_count' => 'integer',
        'down_days_count' => 'integer',
        'total_return' => 'float',
    ];

    /**
     * İlgili fonun detayları ile bağlantı
     */
    public function fund()
    {
        return $this->belongsTo(TefasFund::class, 'fund_code', 'code');
    }
}
