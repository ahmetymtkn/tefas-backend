<?php

namespace App\Http\Controllers;

use App\Models\TefasFundDetail;
use App\Models\TefasFund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TefasFundDetailController extends Controller
{
    /**
     * Tüm fonların belirli bir tarihteki detayını getir (kod, ad, kategori, fiyat)
     * 
     * GET /api/tefas/fund-details?date=2026-03-28
     * Tarih parametreı opsiyonel: Verilmezse en son tarih kullanılır
     * @param Request $request - Op. query: date (Y-m-d formatı)
     * @return JSON - Tüm fonlar la detayları
     */
    // Tüm fonların belirli bir tarihteki bilgilerini getir
    public function getAllByDate(Request $request)
    {
        // Query parametresi: date (opsiyonel)
        $date = $request->input('date');

        // Eğer tarih verilmemişse en yeni tarih kullan
        if (!$date) {
            $date = TefasFundDetail::max('tarih');
        }

        // Eğer hala tarih yoksa (hiç veri yok) 404 döndür
        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'No fund details found'
            ], 404);
        }

        // O tarihin tüm fonlarını getir (kod ve fiyat) ve döndür
        $data = TefasFundDetail::where('tarih', $date)
            ->select('code', 'FIYAT')
            ->get()
            ->map(function ($detail) {
                $fund = TefasFund::with('category:id,name')
                    ->where('code', $detail->code)
                    ->first();

                return [
                    'code' => $detail->code,
                    'name' => $fund ? $fund->name : null,
                    'category_name' => $fund && $fund->category ? $fund->category->name : null,
                    'fiyat' => $detail->FIYAT
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Belirli bir fonun detayını kod ve tarih ile getir (portfolyo dağılımı dahil)
     * 
     * GET /api/tefas/fund-details/{code}?date=2026-03-28
     * Tarih parametreı opsiyonel: Verilmezse o fonın en son verileri kullanılır
     * @param string $code - Fon kodu
     * @param Request $request - Op. query: date (Y-m-d formatı)
     * @return JSON - Fon detayları (portfolyo dışlerı v.s)
     */
    // Belirli bir fonun detayını getir
    public function getByCodeAndDate($code, Request $request)
    {
        // Query parametresi: date (opsiyonel)
        $date = $request->input('date');

        // Eğer tarih verilmemişse bu fon için en yeni tarih kullan
        if (!$date) {
            $date = TefasFundDetail::where('code', $code)->max('tarih');
        }

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'No fund details found for this code'
            ], 404);
        }

        // Fon ve tarih için veri ara
        $data = TefasFundDetail::where('code', $code)
            ->where('tarih', $date)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for this date'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
