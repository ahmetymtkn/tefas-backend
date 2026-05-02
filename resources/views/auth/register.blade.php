@extends('layouts.guest')

@section('title', 'Kayıt Ol')

@section('content')
<div class="w-full max-w-md bg-brand-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(255,159,28,0.3)] overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-brand-teal">TEFAS<span class="text-brand-orange-dark">Analytics</span></h1>
            <p class="text-gray-500 mt-2">Yeni bir hesap oluşturun</p>
        </div>

        <form id="registerForm" class="space-y-4">
            <!-- Hata/Başarı Mesajı Alanı -->
            <div id="registerAlert" class="hidden p-3 rounded-lg text-sm text-center border"></div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                <input type="text" id="name" name="name" required 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-orange-dark focus:border-transparent transition-all"
                    placeholder="Adınız Soyadınız">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Posta</label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-orange-dark focus:border-transparent transition-all"
                    placeholder="ornek@posta.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-orange-dark focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Şifre (Tekrar)</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-orange-dark focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit" id="registerBtn"
                class="w-full bg-brand-orange-dark hover:bg-orange-500 text-white font-bold py-3.5 px-4 rounded-xl transition-all flex justify-center items-center shadow-lg shadow-brand-orange-dark/30 mt-6!">
                <span>Kayıt Ol</span>
                <!-- Loading Spinner (Gizli) -->
                <svg id="registerSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Zaten hesabınız var mı? 
            <a href="{{ route('login.page') }}" class="text-brand-teal font-semibold hover:underline">Giriş Yapın</a>
        </div>
    </div>
</div>
@endsection