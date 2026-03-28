{{-- 
  FON DETAY SAYFASI (Fund Detail)
  ---
  Tek bir fonun tüm detayları
  Bilgi: Kategori, isin, komisyon, vd
  Getiri: 1 ay, 3 ay, 6 ay, 1 yıl getiri oranları
  Karşılaştırma: Diğer fonlarla karşılaştırma
  Tarih Bazlı: Belirli tarihteki portfolyo dağılımı
  
  API çağrısı:
  1. GET /api/tefas/funds/{code} (Fon bilgisi)
  2. GET /api/tefas/fund-stats/{code} (Fon istatistikleri)
  3. GET /api/tefas/comparison/{code}/period/{periodId} (Karşılaştırma)
  4. GET /api/tefas/fund-details/{code}?date=... (Tarih bazlı detay)
--}}

@extends('layouts.app')

@section('title', 'Fon Detay')

@section('content')
<div id="fundDetailPage" data-code="{{ request()->route('code') }}">
  <div class="card mb-3">
    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-sm text-muted">Fon Kodu</p>
        <h1 class="text-2xl font-semibold" id="fdCode">{{ request()->route('code') }}</h1>
        <p class="text-sm text-muted" id="fdCategory"></p>
      </div>
      <div class="text-right">
        <p class="text-sm text-muted">Fon Adı</p>
        <p class="font-medium" id="fdName">-</p>
      </div>
    </div>
  </div>
  <div class="grid md:grid-cols-2 gap-4 mb-3">
    <div class="card" id="fdInfoCard">
      <h2 class="text-lg font-semibold mb-2">Fon Bilgi</h2>
      <div id="fdInfoRows" class="space-y-1 text-sm"></div>
    </div>
    <div class="card" id="fdStatsCard">
      <h2 class="text-lg font-semibold mb-2">Getiri Bilgisi</h2>
      <div id="fdStatsRows" class="space-y-1 text-sm"></div>
    </div>
  </div>
  <div class="card mb-3" id="fdComparisonCard">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-lg font-semibold">Fon Getiri Karşılaştırma</h2>
      <select id="fdPeriod" class="input" style="width:auto">
        <option value="13">Haftalık</option>
        <option value="1">1 Aylık</option>
        <option value="3">3 Aylık</option>
        <option value="6">6 Aylık</option>
        <option value="12">1 Yıllık</option>
        <option value="0">Yılbaşı</option>
        <option value="36">3 Yıllık</option>
        <option value="60">5 Yıllık</option>
      </select>
    </div>
    <div id="fdComparisonBody" class="space-y-1 text-sm"></div>
  </div>
  <div class="card" id="fdDateCard">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-lg font-semibold">Tarih Bazlı Detay</h2>
      <div class="flex items-center gap-2">
        <input type="date" id="fdDate" class="input" />
        <button type="button" class="btn-primary" id="fdDateReload">Getir</button>
      </div>
    </div>
    <div id="fdDateRows" class="space-y-1 text-sm"></div>
  </div>
</div>
@endsection
