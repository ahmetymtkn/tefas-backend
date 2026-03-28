<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Fon Analiz')</title>
    @vite(['resources/css/app.css', 'resources/js/web-app.js'])
  </head>
  <body class="bg-slate-100 min-h-screen">
    <header class="bg-sky-700 text-white shadow-md">
      <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-semibold text-xl tracking-tight">Fon Analiz</a>
        <nav class="flex items-center gap-4" id="nav-links">
          <a href="{{ route('home') }}" class="nav-link">En Çok Kazandıranlar</a>
          <a href="{{ route('funds') }}" class="nav-link">Fonlar</a>
          <a href="{{ route('history') }}" class="nav-link">Tarihsel Veriler</a>
          <a href="{{ route('profile') }}" class="nav-link nav-auth-only hidden">Profil</a>
          <a href="{{ route('login.page') }}" class="nav-link nav-guest-only">Giriş</a>
          <a href="{{ route('register.page') }}" class="nav-link nav-guest-only">Kayıt</a>
          <button type="button" class="nav-link nav-auth-only hidden" id="logoutButton">Çıkış</button>
        </nav>
      </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-6">
      @yield('content')
    </main>
  </body>
</html>
