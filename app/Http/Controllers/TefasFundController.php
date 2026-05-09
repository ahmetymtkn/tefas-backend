<?php

namespace App\Http\Controllers;

use App\Models\TefasFund;
use App\Models\TefasFundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TefasFundController extends Controller
{
    /**
     * Tüm fonları kategori bilgileri ve güncel fiyatlarıyla birlikte listele
     * 
     * GET /api/tefas/funds
     * Sıralı: Fon koduna göre alfabetik
     * @return JSON - Fon kodu, adı, kategori adı, güncel fiyat
     */
    public function getAllFunds()
    {
        // En son tarihi tek sorguda al
        $latestDate = TefasFundDetail::max('tarih');

        // Tüm fonları ve en son fiyatlarını tek sorguda çek (N+1 yok)
        $prices = [];
        if ($latestDate) {
            $prices = TefasFundDetail::where('tarih', $latestDate)
                ->pluck('FIYAT', 'code')
                ->toArray();
        }

        $data = TefasFund::select('code', 'name', 'category_id')
            ->with(['category:id,name'])
            ->orderBy('code')
            ->get()
            ->map(fn($f) => [
                'code'          => $f->code,
                'name'          => $f->name,
                'category_name' => $f->category?->name ?? '',
                'fiyat'         => isset($prices[$f->code]) ? (float) $prices[$f->code] : null,
            ]);

        return response()->json(
            ['success' => true, 'data' => $data],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Belirli bir fonu kodu ile getir (tüm detayları ile)
     * 
     * GET /api/tefas/funds/{code}
     * @param string $code - Fon kodu (orn: "HAK", "YAT")
     * @return JSON - Fon tüm bilgileri ve kategori
     */
    public function getFundByCode($code)
    {
        // Kategori bilgisini de yükle
        $fund = TefasFund::with('category:id,name')->where('code', $code)->first();

        // Fon bulunamadı ise 404 hata döndür
        if (!$fund) {
            return response()->json(['success' => false, 'message' => 'Fund not found'], 404);
        }

        // Unicode karakterleri (Türkçe) düzgün göster
        return response()->json(['success' => true, 'data' => $fund], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
