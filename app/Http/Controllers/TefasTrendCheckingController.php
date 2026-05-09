<?php

namespace App\Http\Controllers;

use App\Models\TefasTrendChecking;
use Illuminate\Http\JsonResponse;

/**
 * Trend Kontrol Controller (Son 30 Gün)
 * 
 * Tablodaki unique key: (fund_code, period_days, analysis_date)
 * Desteklenen periyotlar: 3, 5, 7, 14, 21, 30 gün
 */
class TefasTrendCheckingController extends Controller
{
    // Geçerli trend kontrol periyotları
    const VALID_PERIODS = [3, 5, 7, 14, 21, 30];

    /**
     * Belirli bir periyot için tüm fonların en son trend kontrol verilerini getirir.
     * Yalnızca o periyottaki veritabanındaki en güncel tarihli analiz verilerini döndürür.
     * 
     * GET /api/tefas/trend-checks/{periodDays}
     * @param int $periodDays - Kontrol periyodu (3, 5, 7, 14, 21, 30)
     * @return JsonResponse
     */
    public function getLatestTrendChecks(int $periodDays): JsonResponse
    {
        // Geçersiz periyot kontrolü
        if (!in_array($periodDays, self::VALID_PERIODS)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz periyot. Geçerli değerler: ' . implode(', ', self::VALID_PERIODS),
                'valid_periods' => self::VALID_PERIODS,
                'data' => []
            ], 422);
        }

        // Bu periyottaki en son analiz tarihini al
        $latestDate = TefasTrendChecking::where('period_days', $periodDays)->max('analysis_date');
        
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => "Bu periyot ({$periodDays} gün) için trend kontrol verisi bulunamadı",
                'data' => []
            ], 404);
        }
        
        // Yalnızca en güncel tarihli ve bu periyoda ait analiz verilerini al
        $trendChecks = TefasTrendChecking::where('period_days', $periodDays)
            ->where('analysis_date', $latestDate)
            ->with(['fund' => function($q) {
                // Fon adını ve kategorisini de dahil et
                $q->select('code', 'name', 'category_id')->with(['category' => function($q2) {
                    $q2->select('id', 'name');
                }]);
            }])
            ->orderBy('fund_code', 'asc')
            ->get()
            ->map(function($trend) {
                return [
                    'fund_code'      => $trend->fund_code,
                    'fund_name'      => $trend->fund ? $trend->fund->name : null,
                    'category_name'  => ($trend->fund && $trend->fund->category) ? $trend->fund->category->name : 'Diğer',
                    'period_days'    => $trend->period_days,
                    'up_days_count'  => (int) $trend->up_days_count,
                    'down_days_count'=> (int) $trend->down_days_count,
                    'total_return'   => (float) $trend->total_return,
                ];
            });
        
        return response()->json([
            'success'       => true,
            'period_days'   => $periodDays,
            'analysis_date' => $latestDate,
            'total_funds'   => $trendChecks->count(),
            'data'          => $trendChecks->values()
        ]);
    }
}
