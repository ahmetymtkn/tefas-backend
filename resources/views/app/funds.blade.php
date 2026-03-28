{{-- 
  FONLAR SAYFASI (All Funds)
  ---
  Tüm fonların tümleri listesi
  Arama: Kod veya ad ile arayabilirsiniz
  Favoriler: Yidılış işareti ile fon favorilere eklenebilir
  
  API çağrısı:
  1. GET /api/tefas/funds (Tüm fonları getir)
  2. POST /api/favorites/add (Favori ekle)
  3. POST /api/favorites/check (Hangisi favorite mi kontrol et)
  4. DELETE /api/favorites/{code} (Favoriden çıkart)
--}}

@extends('layouts.app')

@section('title', 'Fonlar')

@section('content')
<div class="card" id="fundsPage">
  <div class="flex items-center justify-between mb-3">
    <div>
      <h1 class="text-xl font-semibold mb-1">Fonlar</h1>
      <p class="text-muted">Tüm fon listesi, arama ve favoriler.</p>
    </div>
    <div class="flex items-center gap-2">
      <input id="fundSearch" class="input" placeholder="Kod veya isim ile ara" style="width:220px" />
    </div>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>Kod</th>
        <th>Ad</th>
        <th>Kategori</th>
        <th style="width:60px">Fav</th>
      </tr>
    </thead>
    <tbody id="fundsTableBody"></tbody>
  </table>
  <p id="fundsHint" class="text-muted mt-2"></p>
</div>
@endsection
