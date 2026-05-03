<?php

namespace App\Http\Controllers;

use App\Models\TefasFund;
use Illuminate\Http\Request;

class TefasFundController extends Controller
{
    /**
     * Tüm fonları kategori bilgileriyle birlikte listele
     * 
     * GET /api/tefas/funds
     * Sıralı: Fon koduna göre alfabetik
     * @return JSON - Fon kodu, adı ve kategori adı
     */
    public function getAllFunds()
    {
        // Performans için sadece gerekli alanları seçiyoruz.
        // İlişkili kategori bilgisini yüklüyoruz (Eager Loading).
        // Veri boyutunu küçültmek ve kategori adını düz bir şekilde dönmek için map'liyoruz.
        $data = TefasFund::select('code', 'name', 'category_id')
            ->with(['category:id,name'])
            ->orderBy('code')
            ->get()
            ->map(fn($f) => [
                'code'          => $f->code,
                'name'          => $f->name,
                'category_name' => $f->category?->name ?? '',
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
