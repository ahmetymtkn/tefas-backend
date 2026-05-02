@extends('layouts.app')

@section('title', 'Lider Fonlar')

@section('content')
<!-- Sayfa Başlığı -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">En Çok Kazandıranlar</h2>
    <p class="text-gray-500 mt-1">Belirli dönemlere ve kategorilere göre en yüksek getiri sağlayan lider fonlar.</p>
</div>

<!-- Filtreler: Dönem -->
<div class="bg-white/80 backdrop-blur-xl p-4 md:p-6 rounded-3xl border border-white shadow-lg mb-8 relative z-20">
    <div class="w-full">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 ml-1">Performans Dönemi</label>
        <div class="flex flex-wrap gap-2 p-1 bg-gray-100/80 rounded-2xl border border-gray-200/60" id="periodSelector">
            <button data-period="0" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">YTD (Yılbaşı)</button>
            <button data-period="13" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">Haftalık</button>
            <button data-period="1" class="period-btn active-period px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 bg-brand-teal text-white shadow-md shadow-brand-teal/30 scale-100">1 Ay</button>
            <button data-period="3" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">3 Ay</button>
            <button data-period="6" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">6 Ay</button>
            <button data-period="12" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">1 Yıl</button>
            <button data-period="36" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">3 Yıl</button>
            <button data-period="60" class="period-btn px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 text-gray-500 hover:text-gray-800 hover:bg-white scale-100 hover:shadow-sm">5 Yıl</button>
        </div>
    </div>
</div>

<!-- Loading Göstergesi -->
<div id="loadingIndicator" class="hidden flex-col items-center justify-center py-20">
    <div class="relative w-16 h-16 mb-6">
        <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
        <div class="absolute inset-0 rounded-full border-4 border-brand-teal border-t-transparent animate-spin"></div>
    </div>
    <span class="text-base font-bold text-gray-600 animate-pulse" id="loadingText">Veriler analiz ediliyor...</span>
</div>

<!-- Kategori Grid Görünümü -->
<div id="categoryView">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Fon Kategorileri</h3>
            <p class="text-sm text-gray-500 mt-1">Lider fonları görmek için bir kategori seçin.</p>
        </div>
    </div>
    
    <div id="categoryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <!-- JS ile dolacak -->
    </div>
</div>

<!-- Fonlar Grid Görünümü -->
<div id="fundsView" class="hidden">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 bg-white/80 backdrop-blur-md p-5 rounded-3xl border border-white shadow-sm gap-4">
        <div>
            <h3 class="text-xl font-bold text-brand-teal" id="selectedCategoryTitle">Kategori Adı</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Bu kategorideki en yüksek getirili ilk 9 fon</p>
        </div>
        <button id="backToCategoriesBtn" class="bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 hover:text-gray-900 text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kategorilere Dön
        </button>
    </div>
    
    <div id="fundsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
        <!-- JS ile dolacak -->
    </div>
</div>

<style>
    .medal-gradient-1 { background: linear-gradient(135deg, #FFD700 0%, #FDB931 100%); }
    .medal-gradient-2 { background: linear-gradient(135deg, #E3E4E5 0%, #B5C6CE 100%); }
    .medal-gradient-3 { background: linear-gradient(135deg, #FF9933 0%, #D2691E 100%); }
    
    .cat-gradient { background: linear-gradient(to right bottom, #ffffff, #f8fafc); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if(!token) {
        window.location.href = '/login';
        return;
    }

    let currentPeriod = '1';
    
    const periodBtns = document.querySelectorAll('.period-btn');
    
    const categoryView = document.getElementById('categoryView');
    const categoryGrid = document.getElementById('categoryGrid');
    
    const fundsView = document.getElementById('fundsView');
    const fundsGrid = document.getElementById('fundsGrid');
    const backToCategoriesBtn = document.getElementById('backToCategoriesBtn');
    const selectedCategoryTitle = document.getElementById('selectedCategoryTitle');

    const loadingIndicator = document.getElementById('loadingIndicator');
    const loadingText = document.getElementById('loadingText');
    const activePeriodLabel = document.getElementById('activePeriodLabel');

    // Dönem isimleri haritası
    const periodNames = { 
        '0': 'YTD', 
        '13': 'Haftalık', 
        '1': '1 Ay', 
        '3': '3 Ay', 
        '6': '6 Ay', 
        '12': '1 Yıl', 
        '36': '3 Yıl', 
        '60': '5 Yıl' 
    };

    const showLoading = (text) => {
        categoryView.classList.add('hidden');
        fundsView.classList.add('hidden');
        loadingText.textContent = text;
        loadingIndicator.classList.remove('hidden');
        loadingIndicator.classList.add('flex');
    };

    const hideLoading = () => {
        loadingIndicator.classList.add('hidden');
        loadingIndicator.classList.remove('flex');
    };

    // --- 1. Kategorileri Yükle ve Ekrana Bas ---
    const loadCategoriesForPeriod = async (periodId) => {
        showLoading('Kategoriler getiriliyor...');

        try {
            const res = await fetch(`/api/tefas/best-category-rates/period/${periodId}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            hideLoading();
            categoryView.classList.remove('hidden');

            if (data && data.success && data.data.length > 0) {
                renderCategoryGrid(data.data, periodId);
            } else {
                categoryGrid.innerHTML = '<div class="col-span-full py-16 text-center text-gray-500 bg-white/50 backdrop-blur-md rounded-3xl border border-white shadow-sm font-medium">Bu dönem için kategori verisi bulunamadı.</div>';
            }
        } catch (err) {
            console.error("Kategori verisi çekilemedi", err);
            hideLoading();
            categoryView.classList.remove('hidden');
            categoryGrid.innerHTML = '<div class="col-span-full py-16 text-center text-red-500 bg-white/50 backdrop-blur-md rounded-3xl border border-white shadow-sm font-medium">Kategoriler alınamadı. Backend bağlantınızı kontrol edin.</div>';
        }
    };

    const renderCategoryGrid = (categories, periodId) => {
        categoryGrid.innerHTML = '';
        
        categories.forEach(cat => {
            const rate = parseFloat(cat.rate);
            const isPositive = rate >= 0;
            
            const card = document.createElement('div');
            card.className = "cat-gradient p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-teal/30 cursor-pointer transition-all duration-300 group flex flex-col justify-between h-full relative overflow-hidden";
            
            // Arka plan dekoru
            const bgCircle = document.createElement('div');
            bgCircle.className = `absolute -right-6 -bottom-6 w-24 h-24 rounded-full opacity-5 group-hover:scale-150 transition-transform duration-500 ${isPositive ? 'bg-emerald-500' : 'bg-rose-500'}`;
            
            card.innerHTML = `
                <div class="relative z-10">
                    <div class="flex items-start justify-between gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-brand-teal/10 group-hover:text-brand-teal transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                    <h4 class="font-bold text-gray-800 text-[15px] leading-tight mb-2 group-hover:text-brand-teal transition-colors line-clamp-2">${cat.category?.name || 'Bilinmeyen Kategori'}</h4>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-end justify-between relative z-10">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ort. Getiri</span>
                    <span class="text-xl font-black ${isPositive ? 'text-emerald-500' : 'text-rose-500'}">
                        ${isPositive ? '+' : ''}%${rate.toFixed(2)}
                    </span>
                </div>
            `;
            
            card.appendChild(bgCircle);
            
            card.addEventListener('click', () => {
                selectedCategoryTitle.textContent = cat.category?.name || 'Kategori';
                loadTopFunds(cat.category_id, periodId);
            });
            
            categoryGrid.appendChild(card);
        });
    };

    // --- 2. Seçili Kategori İçin Fonları Yükle ---
    const loadTopFunds = async (categoryId, periodId) => {
        showLoading('Lider fonlar listeleniyor...');

        try {
            const res = await fetch(`/api/tefas/best-fund-rates/category/${categoryId}/period/${periodId}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            hideLoading();
            fundsView.classList.remove('hidden');

            if (data && data.success && data.data.length > 0) {
                renderFundsGrid(data.data);
            } else {
                fundsGrid.innerHTML = '<div class="col-span-full py-16 text-center text-gray-500 bg-white/50 backdrop-blur-md rounded-3xl border border-white shadow-sm font-medium">Bu kategoride gösterilecek fon bulunamadı.</div>';
            }
        } catch (err) {
            console.error("Fonlar çekilemedi", err);
            hideLoading();
            fundsView.classList.remove('hidden');
            fundsGrid.innerHTML = '<div class="col-span-full py-16 text-center text-red-500 bg-white/50 backdrop-blur-md rounded-3xl border border-white shadow-sm font-medium">Fon verileri alınamadı. Backend bağlantınızı kontrol edin.</div>';
        }
    };

    const renderFundsGrid = (funds) => {
        fundsGrid.innerHTML = '';

        funds.forEach((fundRateData, index) => {
            const fund = fundRateData.fund || {};
            const rate = parseFloat(fundRateData.rate);
            const isPositive = rate >= 0;
            
            // Sıralama ve Madalya Stilleri
            const rank = index + 1;
            let badgeHtml = '';
            let ringClass = 'ring-1 ring-gray-100 border-white';
            
            if (rank === 1) {
                badgeHtml = `<div class="absolute -top-4 -right-4 w-12 h-12 rounded-full medal-gradient-1 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-yellow-400/40 border-4 border-white z-10">1</div>`;
                ringClass = 'ring-2 ring-yellow-400 border-yellow-100 bg-gradient-to-b from-yellow-50/30 to-white';
            } else if (rank === 2) {
                badgeHtml = `<div class="absolute -top-4 -right-4 w-12 h-12 rounded-full medal-gradient-2 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-gray-400/40 border-4 border-white z-10">2</div>`;
                ringClass = 'ring-2 ring-gray-300 border-gray-100 bg-gradient-to-b from-gray-50/30 to-white';
            } else if (rank === 3) {
                badgeHtml = `<div class="absolute -top-4 -right-4 w-12 h-12 rounded-full medal-gradient-3 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-orange-500/30 border-4 border-white z-10">3</div>`;
                ringClass = 'ring-2 ring-orange-300 border-orange-50 bg-gradient-to-b from-orange-50/30 to-white';
            } else {
                badgeHtml = `<div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 font-bold text-sm shadow-md border border-gray-100 z-10">${rank}</div>`;
                ringClass = 'border border-white bg-white/60';
            }

            const card = document.createElement('div');
            card.className = `group relative rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 backdrop-blur-xl ${ringClass} flex flex-col justify-between cursor-default bg-white`;
            
            card.innerHTML = `
                ${badgeHtml}
                
                <div>
                    <!-- Fon Kodu ve İkon -->
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-teal to-blue-500 flex items-center justify-center text-white font-black tracking-widest shadow-md shadow-brand-teal/30 transform group-hover:rotate-3 transition-transform duration-300">
                            ${fund.code || '?'}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2" title="${fund.name}">${fund.name || 'Fon Adı Bulunamadı'}</h3>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mt-1">${fundRateData.category?.name || ''}</p>
                        </div>
                    </div>
                    
                    <!-- Getiri Oranı -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 mb-2 transition-colors duration-300 group-hover:bg-white">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1">Dönemsel Getiri</div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black ${isPositive ? 'text-emerald-500' : 'text-rose-500'}">
                                ${isPositive ? '+' : ''}%${rate.toFixed(2)}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100/80 flex justify-end items-center">
                    <button onclick="window.location.href='/funds/${fund.code}'" class="bg-white hover:bg-brand-teal text-brand-teal hover:text-white border border-brand-teal/20 text-xs font-bold px-4 py-2 rounded-xl transition-all duration-300 shadow-sm hover:shadow-brand-teal/30">
                        Detaylar &rarr;
                    </button>
                </div>
            `;
            
            fundsGrid.appendChild(card);
        });
    };

    // Geri Dön Butonu
    backToCategoriesBtn.addEventListener('click', () => {
        fundsView.classList.add('hidden');
        categoryView.classList.remove('hidden');
    });

    // Dönem Seçim Event'i
    periodBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const selectedPeriod = e.target.getAttribute('data-period');
            if (currentPeriod === selectedPeriod) return;

            // UI Güncelle
            periodBtns.forEach(b => {
                b.classList.remove('bg-brand-teal', 'text-white', 'shadow-md', 'shadow-brand-teal/30', 'active-period');
                b.classList.add('text-gray-500', 'hover:text-gray-800', 'hover:bg-white', 'hover:shadow-sm');
            });
            e.target.classList.remove('text-gray-500', 'hover:text-gray-800', 'hover:bg-white', 'hover:shadow-sm');
            e.target.classList.add('bg-brand-teal', 'text-white', 'shadow-md', 'shadow-brand-teal/30', 'active-period');

            currentPeriod = selectedPeriod;
            if (activePeriodLabel) {
                activePeriodLabel.textContent = periodNames[currentPeriod] || 'Bilinmiyor';
            }
            
            // Eğer fonlar ekranındaysa, kategori ekranına dönerek yeniden yükle
            fundsView.classList.add('hidden');
            loadCategoriesForPeriod(currentPeriod);
        });
    });

    // Başlangıç
    loadCategoriesForPeriod(currentPeriod);
});
</script>
@endsection
