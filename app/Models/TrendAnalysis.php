<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendAnalysis extends Model
{
    protected $table = 'tefas_trend_analysis';
    
    protected $fillable = [
        'fund_code',
        'period_days',
        'change_percent',
        'last_price',
        'analysis_date'
    ];
    
    protected $casts = [
        'analysis_date'  => 'date',
        'change_percent' => 'float',
        'last_price'     => 'float',
        'period_days'    => 'integer',
    ];
    
    public $timestamps = false;
    
    /**
     * TefasFund ile ilişki
     */
    public function fund()
    {
        return $this->belongsTo(TefasFund::class, 'fund_code', 'code');
    }
}
