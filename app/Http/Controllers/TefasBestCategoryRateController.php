<?php

namespace App\Http\Controllers;

use App\Models\TefasBestCategoryRate;
use Illuminate\Http\Request;

class TefasBestCategoryRateController extends Controller
{
    /**
     * Verilen dönem içindeki tüm kategorilerin en yüksek verimlilik oranlarını getir
     * 
     * GET /api/tefas/best-category-rates/period/{periodId}
     * Sonucu: En son tarihten veriler (ör: bugünün verileri)
     * @param int $periodId - Dönem ID (1=1 ay, 3=3 ay, 12=1 yıl vb.)
     * @return JSON - Kategori verimlilik verileri, çekiliş tarihi
     */
    // Verilen dönem için tüm kategorilerin en iyi verimliliğini getir
    public function getLatestByPeriod($periodId)
    {
        // Verilen dönem için en yeni çekiliş tarihini al
        $latestDate = TefasBestCategoryRate::where('period_id', $periodId)
            ->max('fetched_at');

        // Eğer veri yoksa 404 döndür
        if (!$latestDate) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for this period'
            ], 404);
        }

        // O tarihin kategorilerine ait oranları getir ve düzgün formatta döndür
        $data = TefasBestCategoryRate::with('category')
            ->where('period_id', $periodId)
            ->where('fetched_at', $latestDate)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'period_id' => $item->period_id,
                    'rate' => $item->getiri,
                    'fetched_at' => $item->fetched_at,
                    'category' => $item->category,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data,
            'fetched_at' => $latestDate
        ]);
    }
}
