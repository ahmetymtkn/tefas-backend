<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendAnalysis extends Model
{
    protected $table = 'tefas_trend_analysis';
    
    protected $fillable = [
        'fund_code',
        'up_streak',
        'down_streak',
        'change_percent',
        'last_price',
        'analysis_date'
    ];
    
    protected $casts = [
        'analysis_date' => 'date',
        'change_percent' => 'float',
        'last_price' => 'float',
        'up_streak' => 'integer',
        'down_streak' => 'integer'
    ];
    
    public $timestamps = false;
    
    /**
     * Relationship with TefasFund
     */
    public function fund()
    {
        return $this->belongsTo(TefasFund::class, 'fund_code', 'code');
    }
}
