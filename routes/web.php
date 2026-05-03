<?php

/**
 * Web Rotaları - Blade Template Sayfaları
 * 
 * Not: Frontend React Native mobil uygulaması için Blade view'ları
 * Her rota bir layout ve Vite ile derlenen JS/CSS dosyalarını yükler.
 */

use Illuminate\Support\Facades\Route;

// ===== KİMLİK DOĞRULAMA SAYFALARI =====
// Giriş ve kayıt sayfaları (Uygulama yüklenmeden önce çalışır)

// Login sayfası
Route::view('/login', 'auth.login')->name('login.page');
// Kayıt (Register) sayfası
Route::view('/register', 'auth.register')->name('register.page');

// ===== ANA UYGULAMA SAYFALARI =====
// Giriş yaptıktan sonra erişilebilir sayfalar (Frontend tarafından yönetilir)

// Ana sayfa - Analiz sayfası
Route::view('/', 'app.analytics')->name('analytics');
// Tüm fonlar
Route::view('/funds', 'app.funds')->name('funds');
// Spesifik Fon Detay Sayfası
Route::get('/funds/{code}', function ($code) {
    return view('app.fund-detail', ['code' => strtoupper($code)]);
})->name('fund-detail');
// En çok kazandıranlar
Route::view('/top-earners', 'app.top-earners')->name('top-earners');
// Tarihsel veriler
Route::view('/history', 'app.history')->name('history');
// Kullanıcı profili
Route::view('/profile', 'app.profile')->name('profile');
