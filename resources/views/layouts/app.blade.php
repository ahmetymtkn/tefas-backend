<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEFAS Analytics - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html.dark-mode { filter: invert(1) hue-rotate(180deg) brightness(1.05) contrast(0.95); background: #ffffff; }
        html.dark-mode body { background: #ffffff; }
        html.dark-mode img, html.dark-mode canvas, html.dark-mode .no-invert { filter: invert(1) hue-rotate(180deg); }
        html.dark-mode .medal-gradient-1, html.dark-mode .medal-gradient-2, html.dark-mode .medal-gradient-3 { filter: invert(1) hue-rotate(180deg); }

        #acc-menu {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        #acc-arrow {
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-[#E1E7EF] text-text-main antialiased h-screen flex overflow-hidden">

    @php
        $currentRoute = request()->route()->getName();
        $analizActive = in_array($currentRoute, ['trend-analysis', 'home', 'trend-checks']);
    @endphp

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white flex flex-col border-r border-gray-200 shrink-0">

        <div class="h-20 flex items-center justify-center border-b border-gray-100">
            <h1 class="text-2xl font-bold text-brand-teal">TEFAS<span class="text-brand-orange-dark">Analytics</span></h1>
        </div>

        <div class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ana Menü</div>

        <nav class="flex-1 overflow-y-auto px-4 pb-4" style="display:flex; flex-direction:column; gap:4px;">

            <!-- ANALİZ ACCORDION -->
            <div>
                <button id="acc-btn" type="button" style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-radius:12px; font-weight:600; font-size:14px; border:none; cursor:pointer; transition:background 0.15s, color 0.15s; background:{{ $analizActive ? 'rgba(255,159,28,0.1)' : 'transparent' }}; color:{{ $analizActive ? '#FF9F1C' : '#4B5563' }};">
                    <span>Analiz</span>
                    <svg id="acc-arrow" style="width:16px; height:16px; transform:{{ $analizActive ? 'rotate(180deg)' : 'rotate(0deg)' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="acc-menu" style="max-height:{{ $analizActive ? '300px' : '0px' }}; padding-left:12px;">
                    <div style="display:flex; flex-direction:column; gap:2px; padding-top:4px; padding-bottom:4px;">

                        <a href="{{ route('trend-analysis') }}" style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:500; text-decoration:none; background:{{ in_array($currentRoute, ['trend-analysis','home']) ? '#FF9F1C' : 'transparent' }}; color:{{ in_array($currentRoute, ['trend-analysis','home']) ? '#fff' : '#6B7280' }};">
                            <span style="width:6px; height:6px; border-radius:50%; background:{{ in_array($currentRoute, ['trend-analysis','home']) ? '#fff' : '#D1D5DB' }};"></span>
                            Trend Analizi
                        </a>

                        <a href="{{ route('trend-checks') }}" style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:500; text-decoration:none; background:{{ $currentRoute == 'trend-checks' ? '#FF9F1C' : 'transparent' }}; color:{{ $currentRoute == 'trend-checks' ? '#fff' : '#6B7280' }};">
                            <span style="width:6px; height:6px; border-radius:50%; background:{{ $currentRoute == 'trend-checks' ? '#fff' : '#D1D5DB' }};"></span>
                            Periyodik Kontrol
                        </a>

                    </div>
                </div>
            </div>

            <a href="{{ route('funds') }}" style="display:flex; align-items:center; padding:12px 16px; border-radius:12px; font-weight:600; font-size:14px; text-decoration:none; background:{{ $currentRoute == 'funds' ? '#FF9F1C' : 'transparent' }}; color:{{ $currentRoute == 'funds' ? '#fff' : '#4B5563' }};">
                Fonlar
            </a>

            <a href="{{ route('top-earners') }}" style="display:flex; align-items:center; padding:12px 16px; border-radius:12px; font-weight:600; font-size:14px; text-decoration:none; background:{{ $currentRoute == 'top-earners' ? '#FF9F1C' : 'transparent' }}; color:{{ $currentRoute == 'top-earners' ? '#fff' : '#4B5563' }};">
                En Çok Kazandıranlar
            </a>

            <a href="{{ route('history') }}" style="display:flex; align-items:center; padding:12px 16px; border-radius:12px; font-weight:600; font-size:14px; text-decoration:none; background:{{ $currentRoute == 'history' ? '#FF9F1C' : 'transparent' }}; color:{{ $currentRoute == 'history' ? '#fff' : '#4B5563' }};">
                Tarihsel Veriler
            </a>

            <div style="padding:24px 0 8px; font-size:10px; font-weight:600; letter-spacing:0.08em; color:#9CA3AF; text-transform:uppercase;">Hesap</div>

            <a href="{{ route('profile') }}" style="display:flex; align-items:center; padding:12px 16px; border-radius:12px; font-weight:600; font-size:14px; text-decoration:none; background:{{ $currentRoute == 'profile' ? '#FF9F1C' : 'transparent' }}; color:{{ $currentRoute == 'profile' ? '#fff' : '#4B5563' }};">
                Profil
            </a>

            <div style="margin-top:auto; padding-top:24px;">
                <button id="themeToggle" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; font-weight:700; font-size:14px; background:#F3F4F6; color:#4B5563; border:1px solid #E5E7EB; cursor:pointer;">
                    <svg id="themeIconDark" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="themeIconLight" style="width:20px;height:20px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span id="themeText">Gece Modu</span>
                </button>
            </div>

        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            @yield('content')
        </main>
    </div>

    {{-- Dark mode flash engellemek için --}}
    <script>
        if (localStorage.getItem('app_theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>

    {{-- Accordion ve Dark Mode --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Analiz Accordion
            var btn     = document.getElementById('acc-btn');
            var menu    = document.getElementById('acc-menu');
            var arrow   = document.getElementById('acc-arrow');

            if (btn && menu && arrow) {
                // Eğer sayfa yüklendiğinde menu açıksa, max-height değerini tam oturacak şekilde ayarla
                if (menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '') {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                }

                btn.addEventListener('click', function () {
                    // Şu anki stil 0px ise VEYA boşsa kapalıdır -> Aç
                    if (menu.style.maxHeight === '0px' || menu.style.maxHeight === '') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        // Açıktır -> Kapat
                        menu.style.maxHeight = '0px';
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Auth kontrol
            var token = localStorage.getItem('auth_token');
            if (!token) { window.location.href = '/login'; return; }

            // Dark Mode
            var themeToggle    = document.getElementById('themeToggle');
            var themeIconDark  = document.getElementById('themeIconDark');
            var themeIconLight = document.getElementById('themeIconLight');
            var themeText      = document.getElementById('themeText');

            function setDarkMode(on) {
                if (on) {
                    document.documentElement.classList.add('dark-mode');
                    if (themeIconDark) themeIconDark.style.display  = 'none';
                    if (themeIconLight) themeIconLight.style.display = 'block';
                    if (themeText) themeText.textContent = 'Gündüz Modu';
                    localStorage.setItem('app_theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    if (themeIconLight) themeIconLight.style.display = 'none';
                    if (themeIconDark) themeIconDark.style.display  = 'block';
                    if (themeText) themeText.textContent = 'Gece Modu';
                    localStorage.setItem('app_theme', 'light');
                }
            }

            if (localStorage.getItem('app_theme') === 'dark') { setDarkMode(true); }

            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    setDarkMode(!document.documentElement.classList.contains('dark-mode'));
                });
            }
        });
    </script>

</body>
</html>