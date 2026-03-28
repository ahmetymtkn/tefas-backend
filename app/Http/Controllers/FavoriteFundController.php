<?php

namespace App\Http\Controllers;

use App\Models\UserFavoriteFund;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// Favori fon yönetimi: listele, ekle, çıkar, kontrol et
class FavoriteFundController extends Controller
{
    // Kullanıcının favori fonlarını getir
    public function getFavoriteFunds(Request $request)
    {
        $user = $request->user();
        
        // Favori fonları kategori bilgisi ile yükle
        $favorites = $user->favoriteFunds()
            ->with('fund.category:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($favorite) {
                return [
                    'fund_code' => $favorite->fund_code,
                    'name' => $favorite->fund->name ?? null,
                    'category_name' => $favorite->fund->category->name ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $favorites,
            'count' => $favorites->count(),
        ], 200);
    }

    // Fonu favori listesine ekle
    public function addFavorite(Request $request)
    {
        // Fon kodunu valide et
        $request->validate([
            'fund_code' => 'required|string|max:50',
        ]);

        $user = $request->user();
        $fundCode = $request->input('fund_code');

        // Zaten favorite'de var mı kontrol et
        $exists = UserFavoriteFund::where('user_id', $user->id)
            ->where('fund_code', $fundCode)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bu fon zaten favorilere ekli',
            ], 409);
        }

        // Yeni favorite kaydını oluştur
        $favorite = UserFavoriteFund::create([
            'user_id' => $user->id,
            'fund_code' => $fundCode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fon favorilere eklendi',
            'data' => $favorite,
        ], 201);
    }

    // Fonu favori listesinden çıkar
    public function removeFavorite(Request $request, $fundCode)
    {
        $user = $request->user();

        // Fonu bul ve sil
        $deleted = UserFavoriteFund::where('user_id', $user->id)
            ->where('fund_code', $fundCode)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Bu fon favorilerde bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fon favorilerden çıkarıldı',
        ], 200);
    }

    // Verilen fonlardan hangilerinin favorite olduğunu kontrol et
    public function checkFavorites(Request $request)
    {
        // Fon kodları array'ini valide et
        $request->validate([
            'fund_codes' => 'required|array',
            'fund_codes.*' => 'required|string|max:50',
        ]);

        $user = $request->user();
        $fundCodes = $request->input('fund_codes');

        // Kullanıcının favori fon kodlarını getir
        $favoriteFunds = UserFavoriteFund::where('user_id', $user->id)
            ->whereIn('fund_code', $fundCodes)
            ->pluck('fund_code')
            ->toArray();

        // Her kod için true/false döndür
        $result = [];
        foreach ($fundCodes as $code) {
            $result[$code] = in_array($code, $favoriteFunds);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200);
    }
}
