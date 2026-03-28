{{-- 
  KAYIT SAYFASI (Register)
  ---
  Yeni kullanıcı oluşturma
  İsim, email, şifre (min 8 char) gerekli
  POST /api/register endpoint'ine istek gönderir
  Başarılıda kullanıcı oluşturulur ve login sayfasına yönlendirilir
--}}

@extends('layouts.app')

@section('title', 'Kayıt Ol')

@section('content')
<div class="max-w-md mx-auto">
  <div class="card">
    <h1 class="text-xl font-semibold mb-2">Kayıt Ol</h1>
    <p class="text-muted mb-4">Yeni bir hesap oluşturun.</p>
    <form id="registerForm" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">İsim</label>
        <input type="text" name="name" class="input" required />
      </div>
      <div>
        <label class="block text-sm mb-1">E-posta</label>
        <input type="email" name="email" class="input" required autocomplete="email" />
      </div>
      <div>
        <label class="block text-sm mb-1">Şifre (en az 8 karakter)</label>
        <input type="password" name="password" class="input" required minlength="8" />
      </div>
      <div>
        <label class="block text-sm mb-1">Şifre Tekrar</label>
        <input type="password" name="password_confirmation" class="input" required minlength="8" />
      </div>
      <button type="submit" class="btn-primary w-full" id="registerSubmit">Kayıt Ol</button>
      <p class="text-muted mt-2">Zaten hesabınız var mı? <a href="{{ route('login.page') }}" class="underline">Giriş yap</a></p>
      <p id="registerError" class="text-sm text-red-600 mt-1" style="display:none"></p>
      <p id="registerSuccess" class="text-sm text-emerald-600 mt-1" style="display:none"></p>
    </form>
  </div>
</div>
@endsection
