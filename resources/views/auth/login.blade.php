{{-- 
  LOGIN SAYFASI (Giriş Yap)
  ---
  Kullanıcının email ve şifre ile giriş yapması
  POST /api/login endpoint'ine istek gönderir
  Başarılıda API token alır ve local storage'a kaydeder
--}}

@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('content')
<div class="max-w-md mx-auto">
  <div class="card">
    <h1 class="text-xl font-semibold mb-2">Giriş Yap</h1>
    <p class="text-muted mb-4">Hesabınıza giriş yapın.</p>
    <form id="loginForm" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">E-posta</label>
        <input type="email" name="email" class="input" required autocomplete="email" />
      </div>
      <div>
        <label class="block text-sm mb-1">Şifre</label>
        <input type="password" name="password" class="input" required autocomplete="current-password" />
      </div>
      <button type="submit" class="btn-primary w-full" id="loginSubmit">Giriş Yap</button>
      <p class="text-muted mt-2">Hesabınız yok mu? <a href="{{ route('register.page') }}" class="underline">Kayıt ol</a></p>
      <p id="loginError" class="text-sm text-red-600 mt-1" style="display:none"></p>
    </form>
  </div>
</div>
@endsection
