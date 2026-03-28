<?php

/**
 * Web Rotaları - Blade Template Sayfaları
 * 
 * İde: Frontend React Native mobil uygulaması için Blade view'lar
 * Her rota bir layout + Vite ile kompile edilen JS/CSS yüklüy
 */

use Illuminate\Support\Facades\Route;

// ===== KİMLİK DOĞRULAMA SAYFALARİ =====
// Giriş ve kayıt sayfası (uygulama yüklenmeden önce)

// Login sayfası
Route::view('/login', 'auth.login')->name('login.page');
// Kayıt (Register) sayfası
Route::view('/register', 'auth.register')->name('register.page');

// ===== ANA UYGULAMA SAYFALARİ =====
// Login sonra erişilebilir sayfalar (Frontend tarafından handle edilir)

// Ana sayfa - Fon listesi
Route::view('/', 'app.home')->name('home');
// Tüm fonlar
Route::view('/funds', 'app.funds')->name('funds');
// İşlem ve favoriler geçmişi
Route::view('/history', 'app.history')->name('history');
// Kullanıcı profili
Route::view('/profile', 'app.profile')->name('profile');
// Spesifik fon detay sayfası
Route::view('/funds/{code}', 'app.fund-detail')->name('fund.detail');
