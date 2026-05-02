<?php

namespace App\Http\Controllers;

use App\Models\TefasTrendChecking;
use Illuminate\Http\JsonResponse;

class TefasTrendCheckingController extends Controller
{
    /**
     * Tüm fonların son 30 gün içerisindeki yükseliş ve düşüş sayılarını getirir.
     * Sadece veritabanındaki en güncel tarihe sahip olan logları döndürür.
     * 
     * @return JsonResponse
     */
    public function getLatestTrendChecks(): JsonResponse
    {
        // Tüm veri setindeki en son analizin yapıldığı tarihi al
        $latestDate = TefasTrendChecking::max('analysis_date');
        
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'Trend kontrol verisi bulunamadı',
                'data' => []
            ], 404);
        }
        
        // Yalnızca en güncel tarihli analiz verilerini al
        $trendChecks = TefasTrendChecking::where('analysis_date', $latestDate)
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
                    'fund_code' => $trend->fund_code,
                    'fund_name' => $trend->fund ? $trend->fund->name : null,
                    'category_name' => ($trend->fund && $trend->fund->category) ? $trend->fund->category->name : 'Diğer',
                    'up_days_count' => (int) $trend->up_days_count,
                    'down_days_count' => (int) $trend->down_days_count,
                    'total_return' => (float) $trend->total_return,
                ];
            });
        
        return response()->json([
            'success' => true,
            'analysis_date' => $latestDate,
            'total_funds' => $trendChecks->count(),
            'data' => $trendChecks->values()
        ]);
    }
}
