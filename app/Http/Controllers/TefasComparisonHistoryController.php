<?php

namespace App\Http\Controllers;

use App\Models\TefasComparisonHistory;
use Illuminate\Http\Request;

class TefasComparisonHistoryController extends Controller
{
    /**
     * Verilen fon ve dönem için en yeni karşılaştırma geçmişini getir
     * 
     * GET /api/tefas/comparison/{code}/period/{periodId}
     * Kullanım: Fon performansını başka fonlarla karşılaştırmak
     * @param string $code - Fon kodu
     * @param int $periodId - Dönem ID
     * @return JSON - Karşılaştırma verileri (isimler ve değerler)
     */
    // Fon ve dönem çın son karşılaştırma verilerini getir
    public function getLatestByCodeAndPeriod($code, $periodId)
    {
        // Fon ve dönem için en yeni karşılaştırma verilerinin tarihini al
        $latestDate = TefasComparisonHistory::where('fund_code', $code)
            ->where('period_id', $periodId)
            ->max('fetched_at');

        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No comparison history found for this fund and period'
            ], 404);
        }

        $data = TefasComparisonHistory::with(['fund', 'period'])
            ->where('fund_code', $code)
            ->where('period_id', $periodId)
            ->where('fetched_at', $latestDate)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
