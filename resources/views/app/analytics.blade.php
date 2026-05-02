@extends('layouts.app')

@section('title', 'Analiz Genel Bakış')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Analizler ve Trendler</h2>
    <p class="text-gray-500 mt-1">Yatırım fonlarının geçmişe dönük performanslarını, yükseliş/düşüş serilerini ve aylık başarı oranlarını detaylı şekilde inceleyin.</p>
</div>

<!-- Sekmeler ve Filtreler -->
<div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-6 gap-4">
    
    <!-- Tab Butonları -->
    <div class="flex bg-white rounded-xl border border-gray-200 p-1 shadow-sm w-full md:w-auto">
        <button id="tabAnalysis" class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-semibold transition-all bg-brand-teal text-white shadow-md">
            Aylık Trend Analizi
        </button>
        <button id="tabChecks" class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500 hover:text-gray-800 hover:bg-gray-50">
            Son 30 Gün Kontrolü
        </button>
    </div>

    <!-- Filtre ve Sıralama -->
    <div class="flex gap-3 w-full md:w-auto">
        <select id="streakFilter" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full md:w-48 p-2.5 shadow-sm font-medium outline-none transition-all">
            <option value="all">Tümü (Düşüşler Dahil)</option>
            <option value="2">En Az 2 Gün</option>
            <option value="3">En Az 3 Gün</option>
            <option value="5">En Az 5 Gün</option>
            <option value="7">En Az 7 Gün</option>
            <option value="14">En Az 14 Gün</option>
        </select>
        
        <select id="categoryFilter" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full md:w-48 p-2.5 shadow-sm font-medium outline-none">
            <option value="all">Tüm Kategoriler</option>
        </select>
        
        <button id="sortByUpBtn" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
            <span>Yükselişe Göre</span>
            <svg id="sortIcon" class="w-4 h-4 text-brand-teal transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </button>
    </div>

</div>

<!-- Tablo Alanı -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 id="tableTitle" class="text-lg font-bold text-gray-800">Aylık Trend Analizi</h3>
            <p id="tableDesc" class="text-sm text-gray-500 mt-0.5">Fonların ardışık gün bazındaki yükseliş ve düşüş trendleri.</p>
        </div>
        <div class="flex gap-4 items-center">
            <div class="text-sm text-gray-500 font-medium">
                Tarih: <span id="latestDateMetric" class="text-gray-800 font-bold">--</span>
            </div>
            <div class="text-sm text-gray-500 font-medium">
                Sonuç: <span id="totalFundsMetric" class="text-brand-orange-dark font-bold">--</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr id="tableHeaders" class="bg-white border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fon</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Seri/Gün</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Getiri %</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="animate-spin h-6 w-6 text-brand-teal mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Veriler Yükleniyor...
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

    let currentTab = 'analysis'; // 'analysis' | 'checks'
    let rawData = [];
    let sortDesc = true; // true: Azalan, false: Artan

    const tabAnalysisBtn = document.getElementById('tabAnalysis');
    const tabChecksBtn = document.getElementById('tabChecks');
    const categoryFilter = document.getElementById('categoryFilter');
    const streakFilter = document.getElementById('streakFilter');
    const sortByUpBtn = document.getElementById('sortByUpBtn');
    const sortIcon = document.getElementById('sortIcon');
    
    // UI Elements
    const titleOpts = {
        'analysis': { tab: tabAnalysisBtn, altTab: tabChecksBtn, title: 'Aylık Trend Analizi', desc: 'Fonların ardışık gün bazındaki yükseliş ve düşüş trendleri.' },
        'checks': { tab: tabChecksBtn, altTab: tabAnalysisBtn, title: 'Son 30 Gün Kontrolü', desc: 'Son 30 gün içinde toplam kaç gün yükseldiği ve düştüğü.' }
    };

    // Load Data
    const loadData = async () => {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500"><svg class="animate-spin h-6 w-6 text-brand-teal mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Yükleniyor...</td></tr>';
        
        try {
            const endpoint = currentTab === 'analysis' ? '/api/tefas/trend-analysis' : '/api/tefas/trend-checks';
            const response = await window.axios.get(endpoint, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if(response.data && response.data.success) {
                rawData = response.data.data;
                document.getElementById('totalFundsMetric').textContent = response.data.total_funds;
                document.getElementById('latestDateMetric').textContent = response.data.analysis_date;
                
                // Kategorileri Çek ve Doldur
                updateCategoryFilter();
                
                // Görüntüle
                applyFilterAndRender();
            }
        } catch(err) {
            console.error("Veri çekme hatası", err);
            document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-red-500">Veri alınamadı.</td></tr>';
        }
    };

    // Kategorileri Menüye Koy
    const updateCategoryFilter = () => {
        const catMap = {};
        rawData.forEach(f => {
            if(f.category_name) catMap[f.category_name] = true;
        });
        const cats = Object.keys(catMap).sort();
        
        const currentSel = categoryFilter.value;
        categoryFilter.innerHTML = '<option value="all">Tüm Kategoriler</option>';
        cats.forEach(c => {
            categoryFilter.innerHTML += `<option value="${c}">${c}</option>`;
        });
        // Eğer önceki seçim hala varsa onu seçili tut
        if(cats.includes(currentSel)) categoryFilter.value = currentSel;
    };

    // Filtreleme ve Sıralama
    const applyFilterAndRender = () => {
        let filtered = [...rawData];

        // 1. Kategori Filtresi
        const selectedCat = categoryFilter.value;
        if(selectedCat !== 'all') {
            filtered = filtered.filter(f => f.category_name === selectedCat);
        }

        // 2. Yükseliş Serisi Filtresi (Sadece Analysis modunda)
        if(currentTab === 'analysis') {
            const streakVal = streakFilter.value;
            if(streakVal !== 'all') {
                const minDays = parseInt(streakVal);
                filtered = filtered.filter(f => f.streak_days >= minDays);
            }
        }

        // 3. Sıralama (Yükselişe Göre)
        filtered.sort((a, b) => {
            let valA = currentTab === 'analysis' ? a.streak_days : a.up_days_count;
            let valB = currentTab === 'analysis' ? b.streak_days : b.up_days_count;
            
            return sortDesc ? (valB - valA) : (valA - valB);
        });

        // 4. Ekrana Bas
        renderTable(filtered);
    };

    // Tablo Çizimi
    const renderTable = (data) => {
        const tbody = document.getElementById('tableBody');
        const thead = document.getElementById('tableHeaders');
        tbody.innerHTML = '';

        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Kriterlere uygun kayıt bulunamadı.</td></tr>';
            return;
        }

        // Header Değişimi
        if(currentTab === 'analysis') {
            thead.innerHTML = `
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fon</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Seri (Gün)</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Değişim %</th>
            `;
        } else {
             thead.innerHTML = `
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fon</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Yükseliş / Düşüş</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Başarı Oranı</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Toplam Getiri</th>
            `;
        }

        data.forEach(fund => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-gray-50 transition-colors cursor-pointer";
            
            // Satıra tıklandığında detay sayfasına git
            tr.addEventListener('click', () => {
                window.location.href = `/funds/${fund.fund_code}`;
            });
            
            if(currentTab === 'analysis') {
                const isPositive = fund.streak_days > 0;
                const statusBadge = isPositive 
                    ? `<span class="bg-brand-teal-light/40 text-brand-teal px-2.5 py-1 rounded-md text-xs font-bold">Yükseliş Trendi</span>`
                    : `<span class="bg-red-50 text-red-600 px-2.5 py-1 rounded-md text-xs font-bold">Düşüş Trendi</span>`;

                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">${fund.fund_code}</div>
                        <div class="text-xs text-gray-500 truncate w-48 text-ellipsis overflow-hidden whitespace-nowrap" title="${fund.fund_name}">${fund.fund_name}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-600">${fund.category_name}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5 font-bold ${isPositive ? 'text-brand-teal' : 'text-red-500'}">
                            ${isPositive ? '↑' : '↓'} ${Math.abs(fund.streak_days)} Gün
                        </div>
                    </td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-right font-bold ${fund.change_percent >= 0 ? 'text-brand-teal' : 'text-red-500'}">
                        %${parseFloat(fund.change_percent).toFixed(2)}
                    </td>
                `;
            } else {
                // TREND CHECKS
                const totalGames = fund.up_days_count + fund.down_days_count;
                const successRate = totalGames > 0 ? Math.round((fund.up_days_count / totalGames) * 100) : 0;
                
                tr.innerHTML = `
                     <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">${fund.fund_code}</div>
                        <div class="text-xs text-gray-500 truncate w-48 text-ellipsis overflow-hidden whitespace-nowrap" title="${fund.fund_name}">${fund.fund_name}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-600">${fund.category_name}</td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <span class="text-brand-teal font-bold">${fund.up_days_count}↑</span> 
                        <span class="text-gray-300 mx-1">/</span> 
                        <span class="text-red-500 font-bold">${fund.down_days_count}↓</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[100px] mb-1">
                            <div class="bg-brand-teal h-2.5 rounded-full" style="width: ${successRate}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-500">%${successRate}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-bold ${fund.total_return >= 0 ? 'text-brand-teal' : 'text-red-500'}">
                        %${parseFloat(fund.total_return).toFixed(2)}
                    </td>
                `;
            }
            tbody.appendChild(tr);
        });
    };

    // Events
    const switchTab = (mode) => {
        if(currentTab === mode) return;
        
        currentTab = mode;
        const opt = titleOpts[mode];
        
        // Yükseliş serisi filtresini göster/gizle
        if(mode === 'analysis') {
            streakFilter.classList.remove('hidden');
        } else {
            streakFilter.classList.add('hidden');
            streakFilter.value = 'all'; // Diğer sekmeye geçerken sıfırla
        }
        
        // Stilleri Güncelle
        opt.tab.classList.replace('text-gray-500', 'text-white');
        opt.tab.classList.replace('hover:text-gray-800', 'text-white');
        opt.tab.classList.replace('hover:bg-gray-50', 'bg-brand-teal');
        opt.tab.classList.add('bg-brand-teal', 'shadow-md');
        
        opt.altTab.classList.replace('text-white', 'text-gray-500');
        opt.altTab.classList.replace('bg-brand-teal', 'hover:bg-gray-50');
        opt.altTab.classList.remove('shadow-md');
        opt.altTab.classList.add('hover:text-gray-800');

        // Textleri Güncelle
        document.getElementById('tableTitle').textContent = opt.title;
        document.getElementById('tableDesc').textContent = opt.desc;

        // Sıralamayı Resetle
        sortDesc = true;
        sortIcon.classList.remove('rotate-180');

        loadData();
    };

    tabAnalysisBtn.addEventListener('click', () => switchTab('analysis'));
    tabChecksBtn.addEventListener('click', () => switchTab('checks'));

    categoryFilter.addEventListener('change', () => applyFilterAndRender());
    streakFilter.addEventListener('change', () => applyFilterAndRender());

    sortByUpBtn.addEventListener('click', () => {
        sortDesc = !sortDesc;
        if(sortDesc) sortIcon.classList.remove('rotate-180');
        else sortIcon.classList.add('rotate-180');
        
        applyFilterAndRender();
    });

    // İlk Yükleme
    loadData();
});
</script>
@endsection
