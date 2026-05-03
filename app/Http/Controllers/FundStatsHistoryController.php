<?php

namespace App\Http\Controllers;

use App\Models\FundStatsHistory;
use Illuminate\Http\Request;

class FundStatsHistoryController extends Controller
{
    /**
     * Verilen fon kodu için en yüksek tarihli istatistikleri getir
     * 
     * GET /api/tefas/fund-stats/{code}
     * Veri: Son gündeki istatistikler (fiyat, getiri, yıllık getiri vb.)
     * @param string $code - Fon kodu
     * @return JSON - Fon istatistiği (son gün)
     */
    // Fon kodu için en son istatistikleri getir
    public function getLatestByCode($code)
    {
        // Verilen fon kodu için en yeni tarihi al
        $latestDate = FundStatsHistory::where('code', $code)
            ->max('created_at');

        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No stats history found for this fund'
            ], 404);
        }

        $data = FundStatsHistory::where('code', $code)
            ->where('created_at', $latestDate)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
