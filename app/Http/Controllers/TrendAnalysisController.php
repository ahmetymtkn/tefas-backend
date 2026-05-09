<?php

namespace App\Http\Controllers;

use App\Models\TrendAnalysis;
use Illuminate\Http\JsonResponse;

/**
 * Trend Analizi Controller
 * 
 * Tablodaki unique key: (fund_code, period_days, analysis_date)
 * Desteklenen periyotlar: 2, 3, 5, 7, 14, 21 gün
 * Tabloda yalnızca yükselen fonlar tutulur.
 */
class TrendAnalysisController extends Controller
{
    // Geçerli trend analiz periyotları
    const VALID_PERIODS = [2, 3, 5, 7, 14, 21];

    /**
     * Belirli bir periyot için tüm fonların en son trend analiz verilerini getirir.
     * Sadece o periyottaki en güncel tarihe sahip verileri döndürür.
     * 
     * GET /api/tefas/trend-analysis/{periodDays}
     * @param int $periodDays - Analiz periyodu (2, 3, 5, 7, 14, 21)
     * @return JsonResponse
     */
    public function getLatestTrends(int $periodDays): JsonResponse
    {
        // Geçersiz periyot kontrolü
        if (!in_array($periodDays, self::VALID_PERIODS)) {
            return response()->json([
                'success'       => false,
                'message'       => 'Geçersiz periyot. Geçerli değerler: ' . implode(', ', self::VALID_PERIODS),
                'valid_periods' => self::VALID_PERIODS,
                'data'          => []
            ], 422);
        }

        // Bu periyottaki en son analiz tarihini al
        $latestDate = TrendAnalysis::where('period_days', $periodDays)->max('analysis_date');
        
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => "Bu periyot ({$periodDays} gün) için trend analiz verisi bulunamadı",
                'data'    => []
            ], 404);
        }
        
        // Sadece bu periyodun en son tarihli verilerini al
        $trends = TrendAnalysis::where('period_days', $periodDays)
            ->where('analysis_date', $latestDate)
            ->with(['fund' => function($q) {
                $q->select('code', 'name', 'category_id')->with(['category' => function($q2) {
                    $q2->select('id', 'name');
                }]);
            }])
            ->orderBy('change_percent', 'desc')
            ->select('fund_code', 'period_days', 'change_percent', 'last_price')
            ->get()
            ->map(function($trend) {
                return [
                    'fund_code'      => $trend->fund_code,
                    'fund_name'      => $trend->fund ? $trend->fund->name : null,
                    'category_id'    => $trend->fund ? $trend->fund->category_id : null,
                    'category_name'  => ($trend->fund && $trend->fund->category) ? $trend->fund->category->name : 'Diğer',
                    'period_days'    => $trend->period_days,
                    'change_percent' => (float) $trend->change_percent,
                    'last_price'     => (float) $trend->last_price
                ];
            });
        
        return response()->json([
            'success'       => true,
            'period_days'   => $periodDays,
            'analysis_date' => $latestDate,
            'total_funds'   => $trends->count(),
            'data'          => $trends->values()
        ]);
    }
}
