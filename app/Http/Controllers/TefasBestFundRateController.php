<?php

namespace App\Http\Controllers;

use App\Models\TefasBestFundRate;
use Illuminate\Http\Request;

class TefasBestFundRateController extends Controller
{
    /**
     * Verilen kategori ve dönem için en yüksek verimlilikte olan top 9 fonu getir
     * 
     * GET /api/tefas/best-fund-rates/category/{categoryId}/period/{periodId}
     * Sıralı: Verimliliğe göre azalan (en iyi önce)
     * @param int $categoryId - Kategori ID
     * @param int $periodId - Dönem ID
     * @return JSON - Top 9 fon ve bunların verimlilik verileri
     */
    // Kategori ve dönem için en iyi 9 fonu getir (verimliliğe göre sıralı)
    public function getTopByCategoryAndPeriod($categoryId, $periodId)
    {
        $latestDate = TefasBestFundRate::where('category_id', $categoryId)
            ->where('period_id', $periodId)
            ->max('fetched_at');

        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for this category and period'
            ], 404);
        }

        $data = TefasBestFundRate::with(['fund', 'category'])
            ->where('category_id', $categoryId)
            ->where('period_id', $periodId)
            ->where('fetched_at', $latestDate)
            ->orderBy('rate', 'desc')
            ->limit(9)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'fetched_at' => $latestDate
        ]);
    }
}
