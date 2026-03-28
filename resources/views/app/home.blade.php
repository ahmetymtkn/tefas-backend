{{-- 
  ANA SAYFA (Home) - En Çok Kazandıran Kategoriler
  ---
  Fonların kategori bazlı en üst performans gösterenleri
  Dönem seçebilirsiniz: 1 hafta, 1 ay, 3 ay, ... 5 yıl
  
  API çağrısı:
  1. GET /api/tefas/best-category-rates/period/{periodId}
  2. GET /api/tefas/best-fund-rates/category/{categoryId}/period/{periodId}
  
  JavaScript: resources/js/app.js (frontend logic)
--}}

@extends('layouts.app')

@section('title', 'En Çok Kazandıranlar')

@section('content')
<div class="card" id="homePage">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-xl font-semibold mb-1">En Çok Kazandıran Kategoriler</h1>
    </div>
    <div>
      <label class="text-sm mr-1">Dönem</label>
      <select id="homePeriod" class="input" style="width:auto;display:inline-block">
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
  </div>
  <div id="homeCategories"></div>
  <div id="homeFunds" class="mt-6"></div>
</div>
@endsection
