{{-- 
  PROFİL SAYFASI (User Profile)
  ---
  Kullanıcı bilgileri: İsim, email, ID
  Favori fonlar: Kullanıcının ekledigi tüm favori fonlar
  
  API çağrısı:
  1. GET /api/user (Authenticated kullanıcı bilgisi)
  2. GET /api/favorites (Favori fon listesi)
  3. DELETE /api/favorites/{code} (Favoriden çıkart)
--}}

@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="grid md:grid-cols-3 gap-4" id="profilePage">
  <div class="md:col-span-1">
    <div class="card">
      <h2 class="text-lg font-semibold mb-2">Kullanıcı Bilgileri</h2>
      <p class="text-sm"><span class="text-muted">İsim:</span> <span id="profileName">-</span></p>
      <p class="text-sm"><span class="text-muted">E-posta:</span> <span id="profileEmail">-</span></p>
      <p class="text-sm"><span class="text-muted">Kullanıcı ID:</span> <span id="profileId">-</span></p>
    </div>
  </div>
  <div class="md:col-span-2">
    <div class="card">
      <h2 class="text-lg font-semibold mb-2">⭐ Favori Fonlar</h2>
      <p class="text-muted mb-2" id="profileFavInfo"></p>
      <table class="table">
        <thead>
          <tr>
            <th>Kod</th>
            <th>Ad</th>
            <th>Kategori</th>
            <th style="width:60px"></th>
          </tr>
        </thead>
        <tbody id="profileFavBody"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection
