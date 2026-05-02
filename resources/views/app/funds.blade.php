@extends('layouts.app')

@section('title', 'Tüm Fonlar')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Tüm Fonlar</h2>
    <p class="text-gray-500 mt-1">TEFAS sistemindeki tüm yatırım fonlarını inceleyin ve güncel fiyatlarını takip edin.</p>
</div>

<!-- Filtre ve Arama Alanı -->
<div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
    <div class="relative w-full md:w-96">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        <input type="text" id="searchInput" class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full shadow-sm font-medium outline-none transition-all placeholder-gray-400" placeholder="Fon Kodu veya Adı Ara...">
    </div>

    <div class="flex items-center gap-3">
        <select id="categoryFilter" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block p-2.5 shadow-sm font-medium outline-none">
            <option value="all">Tüm Kategoriler</option>
        </select>
        <div class="text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm">
            Toplam: <span id="totalFundsCount" class="text-brand-orange-dark">0</span> Fon
        </div>
    </div>
</div>

<!-- Tablo Alanı -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fon Bilgisi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Güncel Fiyat (TL)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Fav</th>
                </tr>
            </thead>
            <tbody id="fundsTableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <svg class="animate-spin h-6 w-6 text-brand-teal mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Fonlar Yükleniyor...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if(!token) return;

    let fundData = [];
    let favorites = new Set();
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const tbody = document.getElementById('fundsTableBody');
    const totalCountLabel = document.getElementById('totalFundsCount');

    const loadFundsAndFavorites = async () => {
        try {
            // Paralel sekilde fonlari ve favorileri cek
            const [fundsRes, favsRes] = await Promise.all([
                window.axios.get('/api/tefas/fund-details', { headers: { 'Authorization': `Bearer ${token}` } }),
                window.axios.get('/api/favorites', { headers: { 'Authorization': `Bearer ${token}` } })
            ]);

            if(favsRes.data && favsRes.data.success) {
                // response'da objects mi geliyor string array mi? eger object ise code'lari alalim:
                const favList = favsRes.data.data;
                favList.forEach(f => {
                    favorites.add(typeof f === 'string' ? f : f.fund_code || f.code);
                });
            }

            if(fundsRes.data && fundsRes.data.success) {
                fundData = fundsRes.data.data;
                populateCategories();
                renderTable();
            }
        } catch(err) {
            console.error("Veriler cekilemedi", err);
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-red-500">Veri alınamadı. Lütfen tekrar deneyin.</td></tr>';
        }
    };

    const toggleFavorite = async (fundCode, btnElement) => {
        const isFav = favorites.has(fundCode);
        
        // Optimistik olarak UI'i guncelle
        if (isFav) {
            favorites.delete(fundCode);
            btnElement.innerHTML = `<svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`;
        } else {
            favorites.add(fundCode);
            btnElement.innerHTML = `<svg class="w-6 h-6 text-yellow-400" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`;
        }

        try {
            if (isFav) {
                await window.axios.delete(`/api/favorites/${fundCode}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            } else {
                await window.axios.post('/api/favorites/add', { fund_code: fundCode }, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            }
        } catch(err) {
            console.error("Favori isleminde hata", err);
            // Hata olursa geri al (rollback)
            if (isFav) favorites.add(fundCode);
            else favorites.delete(fundCode);
            renderTable(); // Tabloyu bastan cizerek hatali UI durumunu duzeltir
        }
    };

    const populateCategories = () => {
        const catMap = {};
        fundData.forEach(f => {
            if(f.category_name) catMap[f.category_name] = true;
        });
        const cats = Object.keys(catMap).sort();
        
        cats.forEach(c => {
            categoryFilter.innerHTML += `<option value="${c}">${c}</option>`;
        });
    };

    const applyFilters = () => {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCat = categoryFilter.value;

        return fundData.filter(fund => {
            const matchSearch = (fund.code && fund.code.toLowerCase().includes(query)) || 
                                (fund.name && fund.name.toLowerCase().includes(query));
            const matchCat = selectedCat === 'all' || fund.category_name === selectedCat;
            
            return matchSearch && matchCat;
        });
    };

    const renderTable = () => {
        const filteredData = applyFilters();
        tbody.innerHTML = '';
        totalCountLabel.textContent = filteredData.length;

        if(filteredData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Arama kriterlerine uygun fon bulunamadı.</td></tr>';
            return;
        }

        filteredData.forEach(fund => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition-colors cursor-pointer";
            
            // Satıra tıklama event'i
            tr.addEventListener('click', (e) => {
                // Eğer tıklanan element fav-btn ise detay sayfasına gitmesini engelle
                if(e.target.closest('.fav-btn')) return;
                window.location.href = `/funds/${fund.code}`;
            });
            
            // Fiyat formatlama (Eger gelmiyorsa vs)
            const price = parseFloat(fund.fiyat);
            const priceDisplay = isNaN(price) ? '-' : price.toFixed(6);
            const isFav = favorites.has(fund.code);

            const starIconSvg = isFav 
                ? `<svg class="w-6 h-6 text-yellow-400" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`
                : `<svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`;

            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-teal-light/20 flex items-center justify-center font-bold tracking-wider text-brand-teal">
                            ${fund.code}
                        </div>
                        <div class="ml-4">
                            <div class="font-bold text-gray-800 uppercase tracking-widest">${fund.code}</div>
                            <div class="text-xs text-gray-500 truncate w-48 md:w-80" title="${fund.name}">${fund.name || 'İsimsiz Fon'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-600">${fund.category_name || 'Kategori Yok'}</td>
                <td class="px-6 py-4 text-right font-bold text-gray-800">
                    ₺ ${priceDisplay}
                </td>
                <td class="px-6 py-4 text-center">
                    <button class="fav-btn p-1 focus:outline-none" data-code="${fund.code}">
                        ${starIconSvg}
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Add event listeners to newly created favorite buttons
        document.querySelectorAll('.fav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const code = e.currentTarget.getAttribute('data-code');
                toggleFavorite(code, e.currentTarget);
            });
        });
    };

    searchInput.addEventListener('input', renderTable);
    categoryFilter.addEventListener('change', renderTable);

    loadFundsAndFavorites();
});
</script>
@endsection
