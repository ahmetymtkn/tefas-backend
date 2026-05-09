@extends('layouts.app')

@section('title', 'Tüm Fonlar')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Tüm Fonlar</h2>
    <p class="text-gray-500 mt-1">TEFAS sistemindeki tüm yatırım fonlarını inceleyin ve güncel fiyatlarını takip edin.</p>
</div>

<!-- Arama ve Sayaç -->
<div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
    <div class="relative w-full md:w-96">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        <input type="text" id="searchInput" class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full shadow-sm font-medium outline-none transition-all placeholder-gray-400" placeholder="Fon Kodu veya Adı Ara...">
    </div>
    <div class="text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm">
        Toplam: <span id="totalFundsCount" class="text-brand-orange-dark">0</span> Fon
    </div>
</div>

<!-- Tablo Alanı -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr id="fundsTableHead" class="bg-gray-50 border-b border-gray-200">
                    <!-- JS tarafından doldurulur -->
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

<!-- Floating Dropdown -->
<div id="fundsDropdown"
     class="hidden fixed z-50 bg-white border border-gray-200 rounded-xl shadow-xl min-w-[220px]"
     style="top:0;left:0;">
    <div id="fundsDropList" class="max-h-64 overflow-y-auto"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    let fundData          = [];
    let favorites         = new Set();
    let allCategories     = [];
    let selectedCategories = new Set(); // boş = hepsi seçili
    let openDrop          = false;

    const searchInput    = document.getElementById('searchInput');
    const tbody          = document.getElementById('fundsTableBody');
    const thead          = document.getElementById('fundsTableHead');
    const totalCountLabel = document.getElementById('totalFundsCount');
    const dropdown       = document.getElementById('fundsDropdown');
    const dropList       = document.getElementById('fundsDropList');

    // ── Kategori başlık etiketi ───────────────────────────────────────────────
    const catLabel = () => {
        if (selectedCategories.size === 0) return 'Kategori';
        return `Kategori <span class="ml-1 px-1.5 py-0.5 rounded-full bg-brand-teal text-white text-[10px] font-bold">${selectedCategories.size}</span>`;
    };

    // ── Tablo başlıklarını çiz ────────────────────────────────────────────────
    const renderHeaders = () => {
        thead.innerHTML = `
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-72">Fon Bilgisi</th>
            <th id="catHeader" class="px-6 py-4 text-xs font-bold uppercase tracking-wider cursor-pointer select-none">
                <div class="flex items-center gap-1.5 ${openDrop ? 'text-brand-teal' : 'text-gray-400 hover:text-gray-600'}">
                    <span>${catLabel()}</span>
                    <svg class="w-3.5 h-3.5 transition-transform ${openDrop ? 'rotate-180 text-brand-teal' : ''}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Güncel Fiyat (TL)</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Fav</th>
        `;
        document.getElementById('catHeader').addEventListener('click', (e) => {
            e.stopPropagation();
            toggleCatDropdown();
        });
    };

    // ── Kategori Dropdown ─────────────────────────────────────────────────────
    const toggleCatDropdown = () => {
        if (!dropdown.classList.contains('hidden')) {
            closeDropdown();
            return;
        }
        openDrop = true;
        renderHeaders();
        buildDropList();
        const anchor = document.getElementById('catHeader');
        const rect = anchor.getBoundingClientRect();
        dropdown.style.top  = `${rect.bottom + window.scrollY + 6}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        dropdown.classList.remove('hidden');
    };

    const buildDropList = () => {
        dropList.innerHTML = '';
        allCategories.forEach(cat => {
            const isChecked = selectedCategories.size === 0 || selectedCategories.has(cat);
            const row = document.createElement('label');
            row.className = 'flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition-colors';
            row.innerHTML = `
                <input type="checkbox" class="w-4 h-4 rounded accent-brand-teal cursor-pointer" ${isChecked ? 'checked' : ''} data-cat="${cat}">
                <span class="text-sm text-gray-700 flex-1">${cat}</span>`;
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                const cb = row.querySelector('input');
                setTimeout(() => {
                    toggleCategory(cat, cb.checked);
                }, 0);
            });
            dropList.appendChild(row);
        });
    };

    const toggleCategory = (cat, checked) => {
        const allSelected = selectedCategories.size === 0;
        if (allSelected) {
            // Hepsi seçiliyken biri kaldırılırsa → geri kalanları ekle
            allCategories.forEach(c => { if (c !== cat) selectedCategories.add(c); });
        } else {
            if (checked) {
                selectedCategories.add(cat);
                if (selectedCategories.size === allCategories.length) selectedCategories.clear();
            } else {
                selectedCategories.delete(cat);
                if (selectedCategories.size === 0) selectedCategories.add(allCategories[0]);
            }
        }
        renderHeaders();
        renderTable();
    };

    const closeDropdown = () => {
        dropdown.classList.add('hidden');
        openDrop = false;
        renderHeaders();
    };

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) closeDropdown();
    });
    dropList.addEventListener('click', e => e.stopPropagation());

    // ── Veri Yükleme ──────────────────────────────────────────────────────────
    const loadFundsAndFavorites = async () => {
        try {
            const [fundsRes, favsRes] = await Promise.all([
                window.axios.get('/api/tefas/funds', { headers: { 'Authorization': `Bearer ${token}` } }),
                window.axios.get('/api/favorites',   { headers: { 'Authorization': `Bearer ${token}` } })
            ]);

            if (favsRes.data && favsRes.data.success) {
                favsRes.data.data.forEach(f => {
                    favorites.add(typeof f === 'string' ? f : f.fund_code || f.code);
                });
            }

            if (fundsRes.data && fundsRes.data.success) {
                fundData = fundsRes.data.data;
                allCategories = [...new Set(fundData.map(f => f.category_name).filter(Boolean))].sort();
                renderHeaders();
                renderTable();
            }
        } catch (err) {
            console.error('Veriler çekilemedi', err);
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-red-500">Veri alınamadı. Lütfen tekrar deneyin.</td></tr>';
        }
    };

    // ── Favori Toggle ─────────────────────────────────────────────────────────
    const starSvg = (active) => active
        ? `<svg class="w-6 h-6 text-yellow-400" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`
        : `<svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path></svg>`;

    const toggleFavorite = async (fundCode, btnElement) => {
        const isFav = favorites.has(fundCode);
        if (isFav) { favorites.delete(fundCode); } else { favorites.add(fundCode); }
        btnElement.innerHTML = starSvg(!isFav);

        try {
            if (isFav) {
                await window.axios.delete(`/api/favorites/${fundCode}`, { headers: { 'Authorization': `Bearer ${token}` } });
            } else {
                await window.axios.post('/api/favorites/add', { fund_code: fundCode }, { headers: { 'Authorization': `Bearer ${token}` } });
            }
        } catch (err) {
            console.error('Favori işleminde hata', err);
            if (isFav) favorites.add(fundCode); else favorites.delete(fundCode);
            renderTable();
        }
    };

    // ── Filtreleme ────────────────────────────────────────────────────────────
    const applyFilters = () => {
        const query = searchInput.value.toLowerCase().trim();
        return fundData.filter(fund => {
            const matchSearch = (!query)
                || (fund.code && fund.code.toLowerCase().includes(query))
                || (fund.name && fund.name.toLowerCase().includes(query));
            const matchCat = selectedCategories.size === 0 || selectedCategories.has(fund.category_name);
            return matchSearch && matchCat;
        });
    };

    // ── Tablo Render ──────────────────────────────────────────────────────────
    const renderTable = () => {
        const filteredData = applyFilters();
        tbody.innerHTML = '';
        totalCountLabel.textContent = filteredData.length;

        if (filteredData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Arama kriterlerine uygun fon bulunamadı.</td></tr>';
            return;
        }

        filteredData.forEach(fund => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors cursor-pointer';

            tr.addEventListener('click', (e) => {
                if (e.target.closest('.fav-btn')) return;
                window.location.href = `/funds/${fund.code}`;
            });

            const price = parseFloat(fund.fiyat);
            const priceDisplay = isNaN(price) ? '-' : price.toFixed(6);
            const isFav = favorites.has(fund.code);

            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-teal-light/20 flex items-center justify-center font-bold tracking-wider text-brand-teal text-xs">
                            ${fund.code}
                        </div>
                        <div class="ml-4">
                            <div class="font-bold text-gray-800 uppercase tracking-widest">${fund.code}</div>
                            <div class="text-xs text-gray-500 truncate w-48 md:w-80" title="${fund.name}">${fund.name || 'İsimsiz Fon'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-600">${fund.category_name || 'Kategori Yok'}</td>
                <td class="px-6 py-4 text-right font-bold text-gray-800">₺ ${priceDisplay}</td>
                <td class="px-6 py-4 text-center">
                    <button class="fav-btn p-1 focus:outline-none" data-code="${fund.code}">
                        ${starSvg(isFav)}
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.querySelectorAll('.fav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const code = e.currentTarget.getAttribute('data-code');
                toggleFavorite(code, e.currentTarget);
            });
        });
    };

    searchInput.addEventListener('input', renderTable);

    loadFundsAndFavorites();
});
</script>
@endsection
