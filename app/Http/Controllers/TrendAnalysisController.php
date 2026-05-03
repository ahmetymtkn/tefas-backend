<?php

namespace App\Http\Controllers;

use App\Models\TrendAnalysis;
use Illuminate\Http\JsonResponse;

class TrendAnalysisController extends Controller
{
    /**
     * Tüm fonlar için en son trend analiz verilerini getirir
     * Sadece tüm fonlar genelindeki en güncel analiz tarihine sahip verileri döndürür
     * En güncel tarihe ait analizi olmayan fonları filtreler
     * 
     * @return JsonResponse
     */
    public function getLatestTrends(): JsonResponse
    {
        // Tüm veri setinden en büyük (en son) analiz tarihini al
        $latestDate = TrendAnalysis::max('analysis_date');
        
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No trend analysis data available',
                'data' => []
            ], 404);
        }
        
        // Sadece en son tarihli tüm trendleri al
        // Bu, en son tarihte analizi olmayan fonları otomatik olarak filtreler
        $trends = TrendAnalysis::where('analysis_date', $latestDate)
            ->with(['fund' => function($q) {
                $q->select('code', 'name', 'category_id')->with(['category' => function($q2) {
                    $q2->select('id', 'name');
                }]);
            }])
            ->orderBy('fund_code', 'asc')
            ->select('fund_code', 'up_streak', 'down_streak', 'change_percent', 'last_price')
            ->get()
            ->map(function($trend) {
                // Seri yönünü belirle: yükseliş için pozitif, düşüş için negatif
                $streak = $trend->up_streak > 0 ? $trend->up_streak : -$trend->down_streak;
                
                return [
                    'fund_code' => $trend->fund_code,
                    'fund_name' => $trend->fund ? $trend->fund->name : null,
                    'category_id' => $trend->fund ? $trend->fund->category_id : null,
                    'category_name' => ($trend->fund && $trend->fund->category) ? $trend->fund->category->name : 'Diğer',
                    'streak_days' => $streak,  // Positive = up days, Negative = down days
                    'change_percent' => (float) $trend->change_percent,
                    'last_price' => (float) $trend->last_price
                ];
            });
        
        return response()->json([
            'success' => true,
            'analysis_date' => $latestDate,  // Already a string from max()
            'total_funds' => $trends->count(),
            'data' => $trends->values()
        ]);
    }
}
