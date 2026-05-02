<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEFAS Analytics - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* CSS Invert Tekniği İle Pratik Dark Mode */
        html.dark-mode { filter: invert(1) hue-rotate(180deg) brightness(1.05) contrast(0.95); background: #ffffff; }
        html.dark-mode body { background: #ffffff; }
        html.dark-mode img, 
        html.dark-mode canvas, 
        html.dark-mode .no-invert { filter: invert(1) hue-rotate(180deg); }
        html.dark-mode .medal-gradient-1,
        html.dark-mode .medal-gradient-2,
        html.dark-mode .medal-gradient-3 { filter: invert(1) hue-rotate(180deg); }
    </style>
</head>
<body class="bg-[#E1E7EF] text-text-main antialiased h-screen flex overflow-hidden">
    
    <!-- SIDEBAR -->
    <aside class="w-64 bg-white flex flex-col border-r border-gray-200 shrink-0">
        <!-- Logo -->
        <div class="h-20 flex items-center justify-center border-b border-gray-100">
            <h1 class="text-2xl font-bold text-brand-teal">TEFAS<span class="text-brand-orange-dark">Analytics</span></h1>
        </div>
        
        <!-- Navigation -->
        <div class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ana Menü</div>
        <nav class="flex-1 flex flex-col overflow-y-auto px-4 space-y-1.5 pb-4">
            @php $currentRoute = request()->route()->getName(); @endphp
            
            <a href="{{ route('analytics') }}" class="flex items-center px-4 py-3 rounded-xl font-semibold {{ $currentRoute == 'analytics' ? 'bg-brand-teal text-white shadow-md shadow-brand-teal/20' : 'text-gray-600 hover:bg-brand-teal-light/40 hover:text-brand-teal' }} transition-all">
                Analiz
            </a>
            <a href="{{ route('funds') }}" class="flex items-center px-4 py-3 rounded-xl font-semibold {{ $currentRoute == 'funds' ? 'bg-brand-teal text-white shadow-md shadow-brand-teal/20' : 'text-gray-600 hover:bg-brand-teal-light/40 hover:text-brand-teal' }} transition-all">
                Fonlar
            </a>
            <a href="{{ route('top-earners') }}" class="flex items-center px-4 py-3 rounded-xl font-semibold {{ $currentRoute == 'top-earners' ? 'bg-brand-teal text-white shadow-md shadow-brand-teal/20' : 'text-gray-600 hover:bg-brand-teal-light/40 hover:text-brand-teal' }} transition-all">
                En Çok Kazandıranlar
            </a>
            <a href="{{ route('history') }}" class="flex items-center px-4 py-3 rounded-xl font-semibold {{ $currentRoute == 'history' ? 'bg-brand-teal text-white shadow-md shadow-brand-teal/20' : 'text-gray-600 hover:bg-brand-teal-light/40 hover:text-brand-teal' }} transition-all">
                Tarihsel Veriler
            </a>
            
            <div class="pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Hesap</div>
            <a href="{{ route('profile') }}" class="flex items-center px-4 py-3 rounded-xl font-semibold {{ $currentRoute == 'profile' ? 'bg-brand-teal text-white shadow-md shadow-brand-teal/20' : 'text-gray-600 hover:bg-brand-teal-light/40 hover:text-brand-teal' }} transition-all">
                Profil
            </a>

            <!-- Gece Modu Butonu -->
            <div class="mt-auto pt-6">
                <button id="themeToggle" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-bold bg-gray-100/80 text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-all border border-gray-200/50 shadow-sm">
                    <svg id="themeIconDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="themeIconLight" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span id="themeText">Gece Modu</span>
                </button>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- HEADER KALDIRILDI -->

        <!-- PAGE CONTENT (Scrollable) -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            @yield('content')
        </main>
        
    </div>

    <!-- Layout JS (Sadece Dashboard İçin) -->
    <script>
    // Dark Mode Başlangıç Kontrolü (Görüntü titremesini engellemek için dışarıda)
    if(localStorage.getItem('app_theme') === 'dark') {
        document.documentElement.classList.add('dark-mode');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem('auth_token');
        if (!token) { window.location.href = '/login'; return; }

        const logoutBtn = document.getElementById('logoutBtn');
        if(logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                try {
                    await window.axios.post('/api/logout', {}, {
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                } catch (e) {} finally {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_data');
                    window.location.href = '/login';
                }
            });
        }

        // Dark Mode Logic
        const themeToggle = document.getElementById('themeToggle');
        const themeIconDark = document.getElementById('themeIconDark');
        const themeIconLight = document.getElementById('themeIconLight');
        const themeText = document.getElementById('themeText');
        
        const setDarkMode = (isDark) => {
            if(isDark) {
                document.documentElement.classList.add('dark-mode');
                themeIconDark.classList.add('hidden');
                themeIconLight.classList.remove('hidden');
                themeText.textContent = 'Gündüz Modu';
                localStorage.setItem('app_theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark-mode');
                themeIconLight.classList.add('hidden');
                themeIconDark.classList.remove('hidden');
                themeText.textContent = 'Gece Modu';
                localStorage.setItem('app_theme', 'light');
            }
        };

        if(localStorage.getItem('app_theme') === 'dark') {
            setDarkMode(true);
        }

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark-mode');
                setDarkMode(!isDark);
            });
        }
    });
    </script>
</body>
</html>
