@extends('layouts.app')

@section('title', $code . ' - Fon Detayı')

@section('content')

<!-- Gizli Kod -->
<input type="hidden" id="currentFundCode" value="{{ $code }}">

<!-- Geri Dönüş Linki -->
<div class="mb-4">
    <a href="javascript:history.back()" class="text-sm font-semibold text-brand-teal hover:text-teal-600 flex items-center gap-1 w-max transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Geri Dön
    </a>
</div>

<!-- Ana Header Kartı (Premium Dark) -->
<div class="bg-gradient-to-br from-gray-900 to-slate-800 rounded-3xl p-8 mb-8 text-white relative overflow-hidden shadow-xl">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mt-10 -mr-10"></div>
    <div class="absolute bottom-0 left-20 w-40 h-40 bg-brand-teal opacity-10 rounded-full blur-2xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6 w-full md:w-auto">
            <!-- Kod Kutusu -->
            <div class="w-20 h-20 shrink-0 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black tracking-widest shadow-lg text-white">
                {{ $code }}
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold tracking-tight mb-2 line-clamp-2" id="fundName">Yükleniyor...</h2>
                <div class="inline-block px-3 py-1 bg-white/10 rounded-lg text-[10px] md:text-xs font-bold tracking-widest uppercase text-teal-100" id="fundCategory">
                    KATEGORİ BEKLENİYOR
                </div>
            </div>
        </div>
        
        <button id="favoriteBtn" class="shrink-0 flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-sm font-bold transition-all disabled:opacity-50 min-w-[150px]">
            <svg id="favIcon" class="w-5 h-5 text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <span id="favText">Favoriye Ekle</span>
        </button>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    
    <!-- Sol: Fon Bilgisi -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8 relative overflow-hidden">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            Fon Bilgisi
        </h3>
        
        <div class="space-y-1.5" id="fundInfoContainer">
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">ISIN Kodu</span>
                <span class="text-sm font-bold text-gray-700 font-mono tracking-wider" id="fi_isin">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Platform Durumu</span>
                <span class="text-sm font-bold text-brand-teal" id="fi_status">TEFAS'ta İşlem Görüyor</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">İşlem Başlama Saati</span>
                <span class="text-sm font-bold text-gray-700" id="fi_start">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">İşlem Bitiş Saati</span>
                <span class="text-sm font-bold text-gray-700" id="fi_end">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Alış Valörü</span>
                <span class="text-sm font-bold text-gray-700" id="fi_val_buy">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Satış Valörü</span>
                <span class="text-sm font-bold text-gray-700" id="fi_val_sell">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Min. Alış</span>
                <span class="text-sm font-bold text-gray-700" id="fi_min_buy">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Min. Satış</span>
                <span class="text-sm font-bold text-gray-700" id="fi_min_sell">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Giriş Komisyonu</span>
                <span class="text-sm font-bold text-gray-700" id="fi_entry">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 hover:bg-gray-50 px-2 rounded-lg transition-colors">
                <span class="text-sm font-semibold text-gray-400">Çıkış Komisyonu</span>
                <span class="text-sm font-bold text-gray-700" id="fi_exit">-</span>
            </div>
        </div>
    </div>

    <!-- Sağ: Getiri Bilgisi -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            Getiri ve Büyüklük
        </h3>

        <!-- Üst Vurgulu Alan -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Güncel Fiyat</div>
                <div class="text-2xl font-black text-gray-800" id="fs_price">-</div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Günlük Getiri</div>
                <div class="text-2xl font-black" id="fs_daily">-</div>
            </div>
        </div>
        
        <div class="space-y-1.5">
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Pay (Adet)</span>
                <span class="text-sm font-bold text-gray-700" id="fs_shares">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Toplam Değer (TL)</span>
                <span class="text-sm font-black text-gray-700" id="fs_total">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Kategori</span>
                <span class="text-sm font-bold text-gray-700" id="fs_cat">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Kategori Sıralaması</span>
                <span class="text-sm font-bold text-gray-700" id="fs_cat_rank">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Yatırımcı Sayısı</span>
                <span class="text-sm font-bold text-gray-700" id="fs_investors">-</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-gray-50/80 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                <span class="text-sm font-semibold text-gray-400">Pazar Payı (%)</span>
                <span class="text-sm font-bold text-gray-700" id="fs_market_share">-</span>
            </div>
            
            <div class="grid grid-cols-4 gap-2 pt-2">
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">1 Ay</div>
                    <div id="fs_1m" class="text-sm font-black">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">3 Ay</div>
                    <div id="fs_3m" class="text-sm font-black">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">6 Ay</div>
                    <div id="fs_6m" class="text-sm font-black">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">1 Yıl</div>
                    <div id="fs_1y" class="text-sm font-black">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Karşılaştırma Modülü -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    <div class="p-6 md:p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            Fon Getiri Karşılaştırması
        </h3>
        
        <select id="compPeriod" class="bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block px-5 py-2.5 outline-none shadow-sm cursor-pointer hover:border-brand-teal/50 transition-colors">
            <option value="1">1 Aylık</option>
            <option value="3">3 Aylık</option>
            <option value="6">6 Aylık</option>
            <option value="12">1 Yıllık</option>
            <option value="36">3 Yıllık</option>
            <option value="60" selected>5 Yıllık</option>
        </select>
    </div>
    
    <div class="p-0 md:p-4">
        <div id="compLoading" class="hidden justify-center items-center py-10">
            <div class="w-8 h-8 rounded-full border-4 border-gray-100 border-t-orange-500 animate-spin"></div>
        </div>
        <div id="compList" class="flex flex-col">
            <!-- Karşılaştırma JS listesi buraya gelecek -->
        </div>
    </div>
</div>

<!-- Tarih Bazlı Detay Modülü -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-12">
    <div class="p-6 md:p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/30">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            Tarih Bazlı Detay
        </h3>
        
        <div class="flex gap-2 w-full md:w-auto">
            <input type="date" id="historyDate" class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl focus:ring-brand-teal focus:border-brand-teal px-4 py-2 outline-none shadow-sm flex-1 md:flex-none">
            <button id="historyBtn" class="bg-brand-teal hover:bg-teal-500 text-white font-bold px-5 py-2 rounded-xl transition-colors shadow-sm">
                Getir
            </button>
        </div>
    </div>
    
    <div class="p-6 md:p-8 min-h-[150px]" id="historyResult">
        <div class="text-center text-gray-400 font-medium py-10" id="historyHint">
            Geçmişe dönük portföy detaylarını görmek için bir tarih seçip Getir'e basın.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if(!token) { window.location.href = '/login'; return; }

    const code = document.getElementById('currentFundCode').value;
    const authHeaders = { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' };

    // DOM Elements
    const favBtn = document.getElementById('favoriteBtn');
    const favIcon = document.getElementById('favIcon');
    const favText = document.getElementById('favText');

    let isFavorited = false;

    // 1. Favori Durumunu Kontrol Et
    const checkFavorite = async () => {
        try {
            const res = await fetch('/api/favorites/check', {
                method: 'POST',
                headers: { ...authHeaders, 'Content-Type': 'application/json' },
                body: JSON.stringify({ fund_codes: [code] })
            });
            const data = await res.json();
            if(data.success && data.data && data.data[code]) {
                setFavUI(true);
            }
        } catch(e) {}
    };

    const setFavUI = (status) => {
        isFavorited = status;
        if(isFavorited) {
            favIcon.setAttribute('fill', 'currentColor');
            favIcon.classList.remove('text-gray-300');
            favIcon.classList.add('text-amber-400');
            favText.textContent = "Favorilerde";
            favBtn.classList.add('border-amber-400/50', 'bg-amber-400/10');
            favBtn.classList.remove('border-white/20', 'bg-white/10');
        } else {
            favIcon.setAttribute('fill', 'none');
            favIcon.classList.add('text-gray-300');
            favIcon.classList.remove('text-amber-400');
            favText.textContent = "Favoriye Ekle";
            favBtn.classList.remove('border-amber-400/50', 'bg-amber-400/10');
            favBtn.classList.add('border-white/20', 'bg-white/10');
        }
    };

    favBtn.addEventListener('click', async () => {
        favBtn.disabled = true;
        try {
            if(isFavorited) {
                // Remove
                await fetch(`/api/favorites/${code}`, { method: 'DELETE', headers: authHeaders });
                setFavUI(false);
            } else {
                // Add
                await fetch('/api/favorites/add', {
                    method: 'POST',
                    headers: { ...authHeaders, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fund_code: code })
                });
                setFavUI(true);
            }
        } catch(e) {
            alert("İşlem başarısız oldu.");
        } finally {
            favBtn.disabled = false;
        }
    });

    // 2. Fon Temel Bilgisi
    const loadFundInfo = async () => {
        try {
            const res = await fetch(`/api/tefas/funds/${code}`, { headers: authHeaders });
            const data = await res.json();
            if(data.success && data.data) {
                const f = data.data;
                document.getElementById('fundName').textContent = f.name || 'Ad Yok';
                document.getElementById('fundCategory').textContent = f.category?.name || 'Kategori Yok';
                
                document.getElementById('fi_isin').textContent = f.isin_code || '-';
                if(f.platform_status) document.getElementById('fi_status').textContent = f.platform_status;
                
                if(f.start_time) document.getElementById('fi_start').textContent = f.start_time;
                if(f.end_time) document.getElementById('fi_end').textContent = f.end_time;
                
                if(f.buy_valor !== undefined) document.getElementById('fi_val_buy').textContent = f.buy_valor;
                if(f.sell_valor !== undefined) document.getElementById('fi_val_sell').textContent = f.sell_valor;
                
                if(f.min_buy_amount !== undefined) document.getElementById('fi_min_buy').textContent = parseFloat(f.min_buy_amount).toFixed(4);
                if(f.min_sell_amount !== undefined) document.getElementById('fi_min_sell').textContent = parseFloat(f.min_sell_amount).toFixed(4);
                
                if(f.entry_commission !== undefined) document.getElementById('fi_entry').textContent = parseFloat(f.entry_commission).toFixed(4);
                if(f.exit_commission !== undefined) document.getElementById('fi_exit').textContent = parseFloat(f.exit_commission).toFixed(4);
            }
        } catch(e) {}
    };

    // Formatter
    const formatMoney = (val) => new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 2 }).format(val);

    // 3. Fon Son İstatistikleri
    const loadFundStats = async () => {
        try {
            const res = await fetch(`/api/tefas/fund-stats/${code}`, { headers: authHeaders });
            const data = await res.json();
            if(data.success && data.data) {
                const s = data.data;
                
                document.getElementById('fs_price').textContent = parseFloat(s.last_price || 0).toFixed(6);
                
                const dGetiri = parseFloat(s.daily_return || 0);
                const dEl = document.getElementById('fs_daily');
                dEl.textContent = (dGetiri > 0 ? '+' : '') + dGetiri.toFixed(4) + '%';
                dEl.className = `text-2xl font-black ${dGetiri >= 0 ? 'text-emerald-500' : 'text-rose-500'}`;
                
                document.getElementById('fs_shares').textContent = formatMoney(s.shares_outstanding || 0);
                document.getElementById('fs_total').textContent = formatMoney(s.total_value || 0);
                document.getElementById('fs_cat').textContent = s.category || '-';
                document.getElementById('fs_cat_rank').textContent = s.category_rank || '-';
                document.getElementById('fs_investors').textContent = formatMoney(s.investor_count || 0);
                document.getElementById('fs_market_share').textContent = parseFloat(s.market_share || 0).toFixed(4);
                
                const getRetHtml = (val) => `<span class="${val>=0 ? 'text-emerald-500' : 'text-rose-500'} font-black">%${val.toFixed(2)}</span>`;
                
                document.getElementById('fs_1m').innerHTML = getRetHtml(parseFloat(s.return_1m || 0));
                document.getElementById('fs_3m').innerHTML = getRetHtml(parseFloat(s.return_3m || 0));
                document.getElementById('fs_6m').innerHTML = getRetHtml(parseFloat(s.return_6m || 0));
                document.getElementById('fs_1y').innerHTML = getRetHtml(parseFloat(s.return_1y || 0));
            }
        } catch(e) {}
    };

    // 4. Karşılaştırma Modülü
    const compPeriodEl = document.getElementById('compPeriod');
    const compListEl = document.getElementById('compList');
    const compLoading = document.getElementById('compLoading');

    const loadComparison = async () => {
        const periodId = compPeriodEl.value;
        compListEl.innerHTML = '';
        compLoading.classList.remove('hidden');
        compLoading.classList.add('flex');

        try {
            const res = await fetch(`/api/tefas/comparison/${code}/period/${periodId}`, { headers: authHeaders });
            const data = await res.json();
            
            compLoading.classList.add('hidden');
            compLoading.classList.remove('flex');
            
            if(data.success && data.data) {
                let names = [];
                let values = [];
                
                // Backend'den gelen veri stringified JSON ise parse et
                try {
                    names = typeof data.data.comparison_names === 'string' ? JSON.parse(data.data.comparison_names) : (data.data.comparison_names || []);
                    values = typeof data.data.comparison_values === 'string' ? JSON.parse(data.data.comparison_values) : (data.data.comparison_values || []);
                } catch(e) {
                    console.error("JSON parse hatası:", e);
                }
                
                const compArray = [];
                if (Array.isArray(names) && Array.isArray(values)) {
                    names.forEach((name, i) => {
                        // Veriler oran olarak (ör. 13.46) geldiği için yüzdeye çevirmek adına 100 ile çarpıyoruz (2 sıfır kaydırma)
                        compArray.push({ key: name, val: parseFloat(values[i] || 0) * 100 });
                    });
                }

                compArray.sort((a,b) => b.val - a.val);

                if (compArray.length === 0) {
                    compListEl.innerHTML = '<div class="text-center py-10 text-sm font-semibold text-gray-400">Karşılaştırma verisi bulunamadı.</div>';
                    return;
                }

                compArray.forEach(item => {
                    const isMainFund = item.key === code;
                    const isPos = item.val >= 0;
                    const row = document.createElement('div');
                    
                    row.className = `flex justify-between items-center p-4 md:px-8 border-b last:border-0 border-gray-100 hover:bg-gray-50 transition-colors ${isMainFund ? 'bg-brand-teal/5' : ''}`;
                    
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs ${isMainFund ? 'bg-brand-teal text-white shadow-md' : 'bg-gray-100 text-gray-500'}">
                                ${item.key.substring(0, 3)}
                            </div>
                            <div class="font-bold text-gray-800 text-sm md:text-base">${item.key}</div>
                        </div>
                        <div class="text-base md:text-lg font-black ${isMainFund ? 'text-brand-teal' : (isPos ? 'text-emerald-500' : 'text-rose-500')}">
                            ${isPos ? '+' : ''}%${item.val.toFixed(2)}
                        </div>
                    `;
                    compListEl.appendChild(row);
                });
            } else {
                compListEl.innerHTML = '<div class="text-center py-10 text-sm font-semibold text-gray-400">Veri bulunamadı.</div>';
            }
        } catch(e) {
            compLoading.classList.add('hidden');
            compListEl.innerHTML = '<div class="text-center py-10 text-sm font-semibold text-red-500">Bağlantı hatası oluştu.</div>';
        }
    };
    
    compPeriodEl.addEventListener('change', loadComparison);

    // 5. Tarih Bazlı Detay
    const historyBtn = document.getElementById('historyBtn');
    const historyDate = document.getElementById('historyDate');
    const historyResult = document.getElementById('historyResult');

    // Default 1 gün öncesi
    const today = new Date();
    today.setDate(today.getDate() - 1);
    historyDate.value = today.toISOString().split('T')[0];

    // Query parametresinden date alma (diğer sayfalardan gelirken)
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('date')) {
        historyDate.value = urlParams.get('date');
        setTimeout(() => historyBtn.click(), 500); // Otomatik yükle
    }

    historyBtn.addEventListener('click', async () => {
        const d = historyDate.value;
        if(!d) return;

        historyBtn.disabled = true;
        historyBtn.textContent = '...';
        historyResult.innerHTML = '<div class="text-center text-brand-teal py-10 font-semibold animate-pulse">Portföy detayı aranıyor...</div>';

        try {
            const res = await fetch(`/api/tefas/fund-details/${code}?date=${d}`, { headers: authHeaders });
            const data = await res.json();
            
            if(data.success && data.data) {
                const item = data.data;
                const price = parseFloat(item.FIYAT || item.fiyat || 0).toFixed(6);
                const pay = parseFloat(item.TEDPAYSAYISI || item.pay || 0);
                const toplamDeger = parseFloat(item.PORTFOYBUYUKLUK || item.toplam_deger || 0);
                const kisiSayisi = parseFloat(item.KISISAYISI || item.kisi_sayisi || 0);
                const tarih = item.tarih || item.date || d;
                
                // Portföy Dağılımı Ayıklama
                const excludedKeys = ['code', 'tarih', 'FIYAT', 'BilFiyat', 'TEDPAYSAYISI', 'KISISAYISI', 'PORTFOYBUYUKLUK', 'BORSABULTENFIYAT', 'id', 'created_at', 'updated_at'];
                let portfolioItems = [];
                for(let key in item) {
                    if(!excludedKeys.includes(key)) {
                        let val = parseFloat(item[key]);
                        if(!isNaN(val) && val > 0) {
                            portfolioItems.push({ name: key, value: val });
                        }
                    }
                }
                
                portfolioItems.sort((a,b) => b.value - a.value);
                
                let portfolioHTML = '';
                if(portfolioItems.length > 0) {
                    portfolioHTML = `
                        <div class="mt-8 pt-6 border-t border-gray-200/60">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                                Portföy Dağılımı (%)
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                ${portfolioItems.map(p => `
                                    <div class="flex flex-col p-3 rounded-xl bg-white border border-gray-100 shadow-sm hover:border-brand-teal/30 transition-colors">
                                        <span class="text-[10px] font-bold text-gray-400 mb-1" title="${p.name}">${p.name}</span>
                                        <span class="text-sm font-black text-gray-800">%${p.value.toFixed(2)}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                historyResult.innerHTML = `
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 md:p-8">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tarih</div>
                                <div class="font-bold text-gray-800">${tarih}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kapanış Fiyatı</div>
                                <div class="font-bold text-brand-teal text-lg">${price} TL</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pay (Adet)</div>
                                <div class="font-bold text-gray-800">${formatMoney(pay)}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Yatırımcı Sayısı</div>
                                <div class="font-bold text-gray-800">${formatMoney(kisiSayisi)} Kişi</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Toplam Değer</div>
                                <div class="font-bold text-gray-800">${formatMoney(toplamDeger)} TL</div>
                            </div>
                        </div>
                        ${portfolioHTML}
                    </div>
                `;
            } else {
                historyResult.innerHTML = `<div class="text-center text-gray-400 font-medium py-10">Bu tarihe ait özel bir detay bulunamadı.</div>`;
            }
        } catch(e) {
            historyResult.innerHTML = `<div class="text-center text-red-400 font-medium py-10">Bağlantı hatası.</div>`;
        } finally {
            historyBtn.disabled = false;
            historyBtn.textContent = 'Getir';
        }
    });

    // İlk Yüklemeler
    checkFavorite();
    loadFundInfo();
    loadFundStats();
    loadComparison();
});
</script>
@endsection
