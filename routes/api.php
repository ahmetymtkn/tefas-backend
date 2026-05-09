<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TefasBestCategoryRateController;
use App\Http\Controllers\TefasBestFundRateController;
use App\Http\Controllers\TefasFundController;
use App\Http\Controllers\FundStatsHistoryController;
use App\Http\Controllers\TefasComparisonHistoryController;
use App\Http\Controllers\TefasFundDetailController;
use App\Http\Controllers\FavoriteFundController;
use App\Http\Controllers\TrendAnalysisController;
use App\Http\Controllers\TefasTrendCheckingController;

// ===== KİMLİK DOĞRULAMA ROTALARI (PUBLIC) =====
// Kimlik doğrulama Token'ı olmadan da erişilebilir rotalar

// Yeni kullanıcı kaydı
Route::post('/register', [UserController::class, 'register']);
// Giriş - API Token döndürür
Route::post('/login', [UserController::class, 'login']);

// ===== KİMLİK DOĞRULAMA GEREKTİREN ROTALAR (PROTECTED) =====
// Tüm bu rotalar için Sanctum API Token gereklidir (Header: Authorization: Bearer {token})
Route::middleware('auth:sanctum')->group(function () {
    // Kimliği doğrulanmış kullanıcının bilgilerini getir
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Logout - Token'ı sil
    Route::post('/logout', [UserController::class, 'logout']);
    
    // ===== TEFAS FONU İSTATİSTİKLERİ =====
    
    // 1. Kategori performans dönemine göre
    // Örn: /api/tefas/best-category-rates/period/12 (1 yıllık dönem)
    Route::get('/tefas/best-category-rates/period/{periodId}', [TefasBestCategoryRateController::class, 'getLatestByPeriod']);

    // 2. En iyi 9 fon kategori ve döneme göre
    // Örn: /api/tefas/best-fund-rates/category/2/period/12
    Route::get('/tefas/best-fund-rates/category/{categoryId}/period/{periodId}', [TefasBestFundRateController::class, 'getTopByCategoryAndPeriod']);

    // ===== FON BİLGİLERİ =====
    
    // 3. Tüm fonları listele
    // Sonuç: Kod, ad, kategori
    Route::get('/tefas/funds', [TefasFundController::class, 'getAllFunds']);

    // 4. Spesifik fon detayı
    // Örn: /api/tefas/funds/HAK
    Route::get('/tefas/funds/{code}', [TefasFundController::class, 'getFundByCode']);

    // ===== FON İSTATİSTİK GEÇMİŞİ =====
    
    // 5. Fon son istatistikleri
    // Örn: /api/tefas/fund-stats/HAK
    // Sonuç: Fiyat, getiri, investor sayısı vb
    Route::get('/tefas/fund-stats/{code}', [FundStatsHistoryController::class, 'getLatestByCode']);

    // ===== FON KARŞILAŞTIRMASI =====
    
    // 6. Fon karşılaştırma verileri dönem+fonuna göre
    // Örn: /api/tefas/comparison/HAK/period/12
    Route::get('/tefas/comparison/{code}/period/{periodId}', [TefasComparisonHistoryController::class, 'getLatestByCodeAndPeriod']);

    // ===== FON FİYATLARI VE DETAYLAR =====
    
    // 7. Tüm fonların tarihe göre detayları
    // Örn: /api/tefas/fund-details?date=2026-03-28
    // Tarih verilmezse son tarih kullanılır
    Route::get('/tefas/fund-details', [TefasFundDetailController::class, 'getAllByDate']);

    // 8. Spesifik fon detayları (portfolyo dağılımı)
    // Örn: /api/tefas/fund-details/HAK?date=2026-03-28
    Route::get('/tefas/fund-details/{code}', [TefasFundDetailController::class, 'getByCodeAndDate']);

    // ===== TREND ANALİZİ =====
    
    // 9. Periyot bazlı trend analiz verileri
    // Desteklenen periyotlar: 2, 3, 5, 7, 14, 21 gün
    // Sonuç: Fon kodu, seri gün sayısı (up/down), yüzde değişim, son fiyat
    // Örn: /api/tefas/trend-analysis/7  (7 günlük trend)
    Route::get('/tefas/trend-analysis/{periodDays}', [TrendAnalysisController::class, 'getLatestTrends']);

    // 10. Periyot bazlı trend kontrol verileri (Yükseliş/Düşüş gün sayısı)
    // Desteklenen periyotlar: 3, 5, 7, 14, 21, 30 gün
    // Örn: /api/tefas/trend-checks/30  (30 günlük kontrol)
    Route::get('/tefas/trend-checks/{periodDays}', [TefasTrendCheckingController::class, 'getLatestTrendChecks']);

    // ===== FAVORİ FONLAR =====
    
    // Kullanıcının favori listesini getir
    Route::get('/favorites', [FavoriteFundController::class, 'getFavoriteFunds']);
    // Fonu favorilere ekle
    Route::post('/favorites/add', [FavoriteFundController::class, 'addFavorite']);
    // Hangi fonların favorilerde olduğunu kontrol et
    Route::post('/favorites/check', [FavoriteFundController::class, 'checkFavorites']);
    // Fonu favorilerden çıkar
    Route::delete('/favorites/{fundCode}', [FavoriteFundController::class, 'removeFavorite']);
});

