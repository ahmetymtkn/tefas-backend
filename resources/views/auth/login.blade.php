@extends('layouts.guest')

@section('title', 'Giriş Yap')

@section('content')
<div class="w-full max-w-md bg-brand-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(46,196,182,0.3)] overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-brand-teal">TEFAS<span class="text-brand-orange-dark">Analytics</span></h1>
            <p class="text-gray-500 mt-2">Hesabınıza giriş yapın</p>
        </div>

        <form id="loginForm" class="space-y-5">
            <!-- Hata Mesajı Alanı -->
            <div id="loginAlert" class="hidden bg-red-50 text-red-600 border border-red-200 p-3 rounded-lg text-sm text-center"></div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Posta</label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all"
                    placeholder="ornek@posta.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit" id="loginBtn"
                class="w-full bg-brand-teal hover:bg-[#25ab9f] text-white font-bold py-3.5 px-4 rounded-xl transition-all flex justify-center items-center shadow-lg shadow-brand-teal/30">
                <span>Giriş Yap</span>
                <!-- Loading Spinner (Gizli) -->
                <svg id="loginSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Hesabınız yok mu? 
            <a href="{{ route('register.page') }}" class="text-brand-orange-dark font-semibold hover:underline">Hemen Kayıt Olun</a>
        </div>
    </div>
</div>
@endsection