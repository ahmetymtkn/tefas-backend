{{-- 
  TARİHSEL VERİLER SAYFASI (Historical Data)
  ---
  Belirli bir tarihteki TÜm fonların fiyat ve portfolyo verisi
  Tarih seçebilirsiniz (default: en yeni tarih)
  
  API çağrısı:
  - GET /api/tefas/fund-details?date=2026-03-28
  - Tüm fonların o gündeki fiyatları
--}}

@extends('layouts.app')

@section('title', 'Tarihsel Veriler')

@section('content')
<div class="card" id="historyPage">
  <div class="flex items-center justify-between mb-3">
    <div>
      <h1 class="text-xl font-semibold mb-1">Tarihsel Veriler</h1>
      <p class="text-muted">Belirli bir tarihteki fon fiyat ve dağılım verileri.</p>
    </div>
    <div class="flex items-center gap-2">
      <input type="date" id="historyDate" class="input" />
      <button type="button" class="btn-primary" id="historyReload">Yükle</button>
    </div>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>Kod</th>
        <th>Ad</th>
        <th>Kategori</th>
        <th>Fiyat</th>
      </tr>
    </thead>
    <tbody id="historyTableBody"></tbody>
  </table>
  <p id="historyHint" class="text-muted mt-2"></p>
</div>
@endsection
